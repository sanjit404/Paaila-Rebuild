<?php


echo"\n\n\n\n+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+\n";
echo"+      Paaila Pre-Setup Requirement Detection     +\n";
echo"+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+\n\n\n\n";


if (PHP_SAPI !== 'cli') {
    echo "ERROR: Run this via CLI only.\n";
    exit;
}


function detectPhpIni()
{
    $paths = [
        "C:\\xampp\\php\\php.ini",
        "D:\\xampp\\php\\php.ini",
        "E:\\xampp\\php\\php.ini",
    ];

    foreach ($paths as $p) {
        if (file_exists($p)) {
            return $p;
        }
    }

    return null;
}

$iniPath = detectPhpIni();


if (!$iniPath) {

    echo "[WARNING] php.ini not auto-detected.\n";

    while (true) {

        echo "Enter php.ini path: ";

        $handle = fopen("php://stdin", "r");
        $input = trim(fgets($handle));

        if (file_exists($input)) {
            $iniPath = $input;
            break;
        }

        echo "[ERROR] File not found. Try again.\n\n";
    }

    echo "\n[OK] php.ini detected: $iniPath\n\n";
} else {
    echo "[OK] php.ini found: $iniPath\n\n";
}


echo "[CHECK] PHP Version\n";

if (version_compare(PHP_VERSION, '8.0.0', '<')) {
    echo "[FAIL] PHP 8+ required. Current: " . PHP_VERSION . "\n";
    exit;
}

echo "[OK] PHP " . PHP_VERSION . "\n\n";


$required = ['xml', 'dom', 'mbstring', 'openssl', 'pdo'];

echo "[CHECK] PHP Extensions\n";

$missing = [];

foreach ($required as $ext) {

    if (extension_loaded($ext)) {
        echo " OK  $ext\n";
    } else {
        echo " MISS $ext\n";
        $missing[] = $ext;
    }
}


if (!empty($missing)) {

    echo "\n[BLOCKED] Missing PHP extensions detected:\n";

    $content = file_get_contents($iniPath);

    foreach ($missing as $ext) {

        echo "\n----------------------------\n";
        echo "[ISSUE] $ext\n";

        $commentPattern = "/^\\s*;\\s*extension\\s*=\\s*$ext\\b.*$/mi";

        $existsPattern = "/^\\s*extension\\s*=\\s*$ext\\b.*$/mi";

        if (preg_match($commentPattern, $content, $match)) {

            echo "STATUS: COMMENTED OUT\n";
            echo "FIX: Remove ';' from this line in php.ini\n\n";
            echo "CURRENT LINE:\n";
            echo $match[0] . "\n";

            echo "\nFIXED LINE SHOULD BE:\n";
            echo preg_replace("/^\\s*;\\s*/", "", $match[0]) . "\n";

        } elseif (!preg_match($existsPattern, $content)) {

            echo "STATUS: NOT FOUND\n";
            echo "FIX: Add this line to php.ini:\n";
            echo "extension=$ext\n";
        } else {
            echo "STATUS: Already enabled (but not detected properly)\n";
        }
    }

    echo "\n==============================\n";
    echo "ACTION REQUIRED\n";
    echo "Fix above issues manually in php.ini\n";
    echo "Then restart Apache/XAMPP\n";
    echo "Re-run: php bootstrap.php\n";
    echo "==============================\n\n";

    exit;
}


echo "\n[CHECK] Composer\n";

exec("composer -V 2>&1", $out, $code);

if ($code !== 0) {
    echo "[WARN] Composer not found or not in PATH\n";
} else {
    echo "[OK] Composer detected\n";
}


echo "\n[CHECK] Node\n";

exec("node -v 2>&1", $nodeOut, $nodeCode);

$nodeStr = implode("\n", $nodeOut);

if ($nodeCode !== 0 || stripos($nodeStr, 'v') === false) {
    echo "[WARN] Node not found\n";
} else {
    echo "[OK] Node: " . trim($nodeStr) . "\n";
}


echo "\n==============================\n";
echo "BOOTSTRAP COMPLETE\n";
echo "System ready for Laravel setup\n";
echo "Run: php artisan paaila:setup\n";
echo "==============================\n\n";



function slow($text, $delay = 100) {
    foreach (str_split($text) as $char) {
        echo $char;
        usleep($delay);
    }
}
echo "\n\n";
usleep(1000);

$logo = [
"██████╗  █████╗  █████╗ ██╗██╗      █████╗",
"██╔══██╗██╔══██╗██╔══██╗██║██║     ██╔══██╗",
"██████╔╝███████║███████║██║██║     ███████║",
"██╔═══╝ ██╔══██║██╔══██║██║██║     ██╔══██║",
"██║     ██║  ██║██║  ██║██║███████╗██║  ██║",
"╚═╝     ╚═╝  ╚═╝╚═╝  ╚═╝╚═╝╚══════╝╚═╝  ╚═╝",
"          "
];

foreach ($logo as $line) {
    slow($line . "\n", 100);
}