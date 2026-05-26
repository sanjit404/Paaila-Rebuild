<?php

namespace App\Http\Controllers;

use App\Models\TourBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
  
    public function testMode(TourBooking $booking)
    {
        $this->authorize('view', $booking);

        if (!$booking->isPending()) {
            return redirect()
                ->route('bookings.show', $booking)
                ->with('info', 'This booking has already been processed.');
        }

        return view('payment.test-mode', compact('booking'));
    }

    public function testProcess(Request $request, TourBooking $booking)
    {
        $this->authorize('view', $booking);

        if (!$booking->isPending()) {
            return redirect()
                ->route('bookings.show', $booking)
                ->with('error', 'Booking cannot be processed.');
        }

        $status = $request->input('status', 'success');

        Log::info('Test payment processing', [
            'booking_id' => $booking->id,
            'status' => $status,
        ]);

        if ($status === 'success') {
    $booking->update([
        'status' => 'confirmed',
        'confirmed_at' => now(),
    ]);

    return redirect()->route('bookings.show', $booking)
        ->with('success', 'Payment successful!');
} else {
    $booking->update([
        'status' => 'pending',
        'cancelled_at' => now(),
    ]);

    return redirect()->route('bookings.index')
        ->with('error', 'Payment failed.');
}
    }
   
    

     public function esewa(TourBooking $booking)
{
    $amount = $booking->total_amount;
    $taxAmount = 0;
    $serviceCharge = 0;
    $deliveryCharge = 0;

    $totalAmount = $amount + $taxAmount + $serviceCharge + $deliveryCharge;

    $transactionUuid = 'BOOKING-' . $booking->id . '-' . time();

    $productCode = 'EPAYTEST';

    $message = "total_amount={$totalAmount},transaction_uuid={$transactionUuid},product_code={$productCode}";

    $secret = "8gBm/:&EnhH.1/q";

    $signature = base64_encode(
        hash_hmac('sha256', $message, $secret, true)
    );

    $esewaConfig = [
        'amount' => $amount,
        'tax_amount' => $taxAmount,
        'total_amount' => $totalAmount,
        'transaction_uuid' => $transactionUuid,
        'product_code' => $productCode,
        'signature' => $signature,
        'success_url' => route('payment.esewa.success'),
        'failure_url' => route('payment.esewa.failure'),
    ];

    return view('payment.esewa', compact('booking', 'esewaConfig'));
}

public function esewaSuccess(Request $request)
{
    Log::info('eSewa success callback', $request->all());

    try {

        $encodedData = $request->input('data');

        if (!$encodedData) {

            return redirect()
                ->route('bookings.index')
                ->with('error', 'Missing payment response.');
        }

        // Decode Base64 response
        $decodedData = base64_decode($encodedData);

        // Convert JSON to array
        $paymentData = json_decode($decodedData, true);

        if (!$paymentData) {

            return redirect()
                ->route('bookings.index')
                ->with('error', 'Invalid payment response.');
        }

        Log::info('Decoded eSewa response', $paymentData);

        // Verify signature
        $signedFields = explode(',', $paymentData['signed_field_names']);

        $messageParts = [];

        foreach ($signedFields as $field) {

            if (isset($paymentData[$field])) {

                $messageParts[] =
                    $field . '=' . $paymentData[$field];
            }
        }

        $message = implode(',', $messageParts);

        $secret = "8gBm/:&EnhH.1/q";

        $generatedSignature = base64_encode(
            hash_hmac('sha256', $message, $secret, true)
        );

        if ($generatedSignature !== $paymentData['signature']) {

            Log::error('eSewa signature verification failed', [
                'expected' => $generatedSignature,
                'received' => $paymentData['signature'],
            ]);

            return redirect()
                ->route('bookings.index')
                ->with('error', 'Invalid payment signature.');
        }

        // Verify payment status
        if (($paymentData['status'] ?? null) !== 'COMPLETE') {

            return redirect()
                ->route('bookings.index')
                ->with('error', 'Payment not completed.');
        }

        // Extract booking ID
        $transactionUuid = $paymentData['transaction_uuid'];

        $parts = explode('-', $transactionUuid);

        $bookingId = $parts[1] ?? null;

        if (!$bookingId) {

            return redirect()
                ->route('bookings.index')
                ->with('error', 'Invalid transaction reference.');
        }

        // Find booking
        $booking = TourBooking::find($bookingId);

        if (!$booking) {

            return redirect()
                ->route('bookings.index')
                ->with('error', 'Booking not found.');
        }

        // Verify amount
        if ((float)$paymentData['total_amount'] !== (float)$booking->total_amount) {

            Log::error('eSewa amount mismatch', [
                'booking_id' => $booking->id,
                'expected' => $booking->total_amount,
                'received' => $paymentData['total_amount'],
            ]);

            return redirect()
                ->route('bookings.show', $booking)
                ->with('error', 'Payment amount mismatch.');
        }

        // Prevent duplicate confirmation
        if ($booking->status !== 'confirmed') {

            $booking->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
                'payment_status' => 'paid',
                'payment_method' => 'esewa',
                'payment_reference' => $paymentData['transaction_code'],
            ]);

            Log::info('eSewa payment confirmed', [
                'booking_id' => $booking->id,
                'transaction_code' => $paymentData['transaction_code'],
            ]);
        }

        return redirect(route('bookings.show', $booking))
            ->with('success', 'Payment successful via eSewa!');

    } catch (\Exception $e) {

        Log::error('eSewa success error', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ]);

        return redirect()
            ->route('bookings.index')
            ->with('error', 'Something went wrong during payment verification.');
    }
}
    public function esewaFailure(Request $request)
    {
        Log::warning('eSewa payment failed', $request->all());

        $oid = $request->input('oid');
        $booking = TourBooking::where('booking_number', $oid)->first();

        if ($booking && $booking->isPending()) {
            return redirect()
                ->route('payment.esewa', $booking)
                ->with('error', 'Payment failed. Please try again.');
        }

        return redirect()
            ->route('bookings.index')
            ->with('error', 'Payment failed.');
    }

    
    
   public function khalti(TourBooking $booking)
{
    $this->authorize('view', $booking);

    if (!$booking->isPending()) {
        return redirect()
            ->route('bookings.show', $booking)
            ->with('info', 'This booking has already been processed.');
    }

    // Initiate payment from backend
    $response = Http::withHeaders([
        'Authorization' => 'Key ' . config('services.khalti.secret_key'),
        'Content-Type'  => 'application/json',
    ])->post('https://a.khalti.com/api/v2/epayment/initiate/', [
        'return_url'       => route('payment.khalti.verify', $booking),
        'website_url'      => url('/'),
        'amount'           => $booking->total_amount * 100,
        'purchase_order_id'=> $booking->booking_number,
        'purchase_order_name' => $booking->tourPackage->name,
    ]);

    if ($response->successful()) {
        // Redirect user to Khalti payment page
        return redirect($response->json()['payment_url']);
    }

    Log::error('Khalti initiation failed', $response->json());

    return redirect()
        ->route('bookings.show', $booking)
        ->with('error', 'Could not initiate Khalti payment. Please try again.');
}

public function khaltiVerify(Request $request, TourBooking $booking)
{
    $this->authorize('view', $booking);

    $pidx = $request->input('pidx');

    // Lookup the payment status
    $response = Http::withHeaders([
        'Authorization' => 'Key ' . config('services.khalti.secret_key'),
        'Content-Type'  => 'application/json',
    ])->post('https://a.khalti.com/api/v2/epayment/lookup/', [
        'pidx' => $pidx,
    ]);

    Log::info('Khalti lookup response', $response->json());

    if ($response->successful() && $response->json()['status'] === 'Completed') {
        $booking->markAsConfirmed();

        return redirect()
            ->route('bookings.show', $booking)
            ->with('success', 'Payment successful! Your booking is confirmed.');
    }

    return redirect()
        ->route('bookings.show', $booking)
        ->with('error', 'Payment verification failed. Please contact support.');
}

    
    
   public function stripe(TourBooking $booking)
{
    $this->authorize('view', $booking);

    if (!$booking->isPending()) {
        return redirect()
            ->route('bookings.show', $booking)
            ->with('info', 'This booking has already been processed.');
    }

    return view('payment.stripe', [
        'booking'   => $booking,
        'publicKey' => config('services.stripe.public_key'),
    ]);
}

public function stripeVerify(Request $request, TourBooking $booking)
{
    $this->authorize('view', $booking);

    \Stripe\Stripe::setApiKey(config('services.stripe.secret_key'));

    try {
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount'         => $booking->total_amount * 100,
            'currency'       => 'usd',
            'payment_method' => $request->input('payment_method_id'),
            'confirm'        => true,
            'return_url'     => route('bookings.show', $booking),
        ]);

        if ($paymentIntent->status === 'succeeded') {
            $booking->markAsConfirmed();

            session()->flash('success', 'Payment successful! Your booking is confirmed.');

            return response()->json([
                'success'  => true,
                'redirect' => route('bookings.show', $booking),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment was not completed.',
        ], 400);

    } catch (\Stripe\Exception\CardException $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 400);
    } catch (\Exception $e) {
        Log::error('Stripe payment failed', ['error' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'Payment failed. Please try again.',
        ], 500);
    }
}
    
    
    private function authorize($ability, $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
    }
}