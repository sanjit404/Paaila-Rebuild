<?php

namespace App\Http\Controllers;

use App\Models\TourBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        'status' => 'cancelled',
        'cancelled_at' => now(),
    ]);

    return redirect()->route('bookings.index')
        ->with('error', 'Payment failed.');
}
    }
   
    
    public function esewa(TourBooking $booking)
    {
        $this->authorize('view', $booking);

        if (!$booking->isPending()) {
            return redirect()
                ->route('bookings.show', $booking)
                ->with('info', 'This booking has already been processed.');
        }

        $esewaConfig = [
            'merchant_code' => config('services.esewa.merchant_code', 'EPAYTEST'),
            'success_url' => route('payment.esewa.success'),
            'failure_url' => route('payment.esewa.failure'),
        ];

        return view('payment.esewa', compact('booking', 'esewaConfig'));
    }

    public function esewaSuccess(Request $request)
    {
        Log::info('eSewa success callback', $request->all());

        $oid = $request->input('oid');
        $refId = $request->input('refId');
        $amt = $request->input('amt');

        $booking = TourBooking::where('booking_number', $oid)->first();

        if (!$booking) {
            Log::error('eSewa: Booking not found', ['oid' => $oid]);
            return redirect()
                ->route('bookings.index')
                ->with('error', 'Booking not found.');
        }

        if ((float)$amt !== (float)$booking->total_amount) {
            Log::error('eSewa: Amount mismatch', [
                'expected' => $booking->total_amount,
                'received' => $amt,
            ]);
            
            return redirect()
                ->route('bookings.show', $booking)
                ->with('error', 'Payment amount mismatch. Please contact support.');
        }

        $booking->markAsConfirmed();

        Log::info('eSewa payment confirmed', [
            'booking_id' => $booking->id,
            'refId' => $refId,
        ]);

        return redirect()
            ->route('bookings.show', $booking)
            ->with('success', 'Payment successful via eSewa! Your booking is confirmed.');
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

        $khaltiConfig = [
            'public_key' => config('services.khalti.public_key', 'test_public_key'),
            'amount' => $booking->total_amount * 100, 
            'product_identity' => $booking->booking_number,
            'product_name' => $booking->tourPackage->name,
        ];

        return view('payment.khalti', compact('booking', 'khaltiConfig'));
    }

    public function khaltiVerify(Request $request, TourBooking $booking)
    {
        $this->authorize('view', $booking);

        Log::info('Khalti verification request', [
            'booking_id' => $booking->id,
            'token' => $request->input('token'),
        ]);

        $token = $request->input('token');
        $amount = $request->input('amount');

      
        
        if ($amount == ($booking->total_amount * 100)) {
            $booking->markAsConfirmed();

            return response()->json([
                'success' => true,
                'redirect' => route('bookings.show', $booking),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment verification failed.',
        ], 400);
    }

    
    
    public function stripe(TourBooking $booking)
    {
        $this->authorize('view', $booking);

        if (!$booking->isPending()) {
            return redirect()
                ->route('bookings.show', $booking)
                ->with('info', 'This booking has already been processed.');
        }

        $stripeConfig = [
            'publishable_key' => config('services.stripe.key', 'pk_test_51SI1L3EyK7O5YZFDdxOuAPEgU26g4dtOygkh3ASOfa0B0xfTSrxTSirHtfdYn5hipZkrNqw6UcuoYVlD5X7QwWOh00H5TFAboD'),
            'amount' => $booking->total_amount,
            'currency' => 'NPR',
        ];

        return view('payment.stripe', compact('booking', 'stripeConfig'));
    }

    public function stripeProcess(Request $request, TourBooking $booking)
    {
        $this->authorize('view', $booking);

        Log::info('Stripe payment processing', [
            'booking_id' => $booking->id,
        ]);

       
        
        $request->validate([
            'stripeToken' => 'required',
        ]);

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $booking->markAsConfirmed();

            return redirect()
                ->route('bookings.show', $booking)
                ->with('success', 'Payment successful via Stripe! Your booking is confirmed.');

        } catch (\Exception $e) {
            Log::error('Stripe payment failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->with('error', 'Payment failed. Please try again.')
                ->withInput();
        }
    }

    
    
    private function authorize($ability, $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
    }
}