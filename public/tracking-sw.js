const CACHE_NAME    = 'paaila-tracking-v1';
const SYNC_TAG      = 'paaila-location-sync';
const LOCATION_STORE = 'pending-locations'; // IndexedDB object store name


self.addEventListener('install', function (event) {
    self.skipWaiting(); // activate immediately
});

self.addEventListener('activate', function (event) {
    event.waitUntil(self.clients.claim()); // take control of all pages
});


self.addEventListener('message', function (event) {
    if (!event.data || event.data.type !== 'LOCATION_UPDATE') return;

    var payload = event.data.payload; // { bookingId, latitude, longitude, accuracy, ... }

    // Try to send immediately
    sendLocation(payload).catch(function () {
        // Failed (offline / server error) → queue for BackgroundSync
        queueLocation(payload).then(function () {
            self.registration.sync.register(SYNC_TAG).catch(function () {
                // BackgroundSync not supported — location is queued in IDB,
                // will be retried next time the SW wakes up
            });
        });
    });
});


self.addEventListener('sync', function (event) {
    if (event.tag !== SYNC_TAG) return;

    event.waitUntil(flushQueue());
});


self.addEventListener('periodicsync', function (event) {
    if (event.tag !== 'paaila-location-heartbeat') return;
    event.waitUntil(flushQueue());
});


function sendLocation(payload) {
    return fetch('/api/tracking/' + payload.bookingId + '/location', {
        method:  'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN':  payload.csrfToken,
        },
        body: JSON.stringify({
            latitude:      payload.latitude,
            longitude:     payload.longitude,
            accuracy:      payload.accuracy  || null,
            speed:         payload.speed     || null,
            altitude:      payload.altitude  || null,
            heading:       payload.heading   || null,
            battery_level: payload.battery   || null,
        }),
        keepalive: true, // allows request to complete even if page unloads
    }).then(function (res) {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res;
    });
}


function openDb() {
    return new Promise(function (resolve, reject) {
        var req = indexedDB.open('paaila-tracking', 1);

        req.onupgradeneeded = function (e) {
            e.target.result.createObjectStore(LOCATION_STORE, {
                keyPath:       'id',
                autoIncrement: true,
            });
        };

        req.onsuccess = function (e) { resolve(e.target.result); };
        req.onerror   = function (e) { reject(e.target.error);   };
    });
}

function queueLocation(payload) {
    return openDb().then(function (db) {
        return new Promise(function (resolve, reject) {
            var tx    = db.transaction(LOCATION_STORE, 'readwrite');
            var store = tx.objectStore(LOCATION_STORE);

            // Keep only last 20 pending locations to avoid unbounded growth
            store.count().onsuccess = function (e) {
                if (e.target.result >= 20) {
                    store.openCursor().onsuccess = function (ce) {
                        var cursor = ce.target.result;
                        if (cursor) cursor.delete();
                    };
                }
            };

            var addReq = store.add({ payload: payload, queuedAt: Date.now() });
            addReq.onsuccess = function () { resolve(); };
            addReq.onerror   = function (e) { reject(e.target.error); };
        });
    });
}

function flushQueue() {
    return openDb().then(function (db) {
        return new Promise(function (resolve) {
            var tx      = db.transaction(LOCATION_STORE, 'readwrite');
            var store   = tx.objectStore(LOCATION_STORE);
            var pending = [];

            store.getAll().onsuccess = function (e) {
                pending = e.target.result;

                if (pending.length === 0) {
                    resolve();
                    return;
                }

                var promises = pending.map(function (record) {
                    return sendLocation(record.payload).then(function () {
                        // Sent successfully — delete from queue
                        var tx2 = db.transaction(LOCATION_STORE, 'readwrite');
                        tx2.objectStore(LOCATION_STORE).delete(record.id);
                    });
                });

                Promise.allSettled(promises).then(function () { resolve(); });
            };
        });
    });
}