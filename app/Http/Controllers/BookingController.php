<?php

namespace App\Http\Controllers;

use App\Models\TourBooking;
use App\Models\TourPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    
    
     
    public function create(TourPackage $package)
    {
        if (!$package->is_active) {
            return redirect()
                ->route('home')
                ->with('error', 'This trek package is currently unavailable.');
        }

        $activeBooking = TourBooking::where('user_id', auth()->id())
            ->active()
            ->first();

        if ($activeBooking) {
            return redirect()
                ->route('bookings.show', $activeBooking)
                ->withErrors([
                    'booking' => sprintf(
                        'You have an active booking (#%s - %s). Please complete or cancel it before creating a new one.',
                        $activeBooking->booking_number,
                        $activeBooking->tourPackage->name
                    )
                ]);
        }

        return view('bookings.create', compact('package'));
    }

  
    public function store(Request $request)
    {
        Log::info('Booking creation started', [
            'user_id' => auth()->id(),
            'request_data' => $request->except('_token'),
        ]);

        try {
           
            $validated = $request->validate([
                'tour_package_id' => 'required|exists:tour_packages,id',
                'tour_date' => 'required|date|after:today',
                'participants' => 'required|integer|min:1',
                'payment_method' => 'required|in:esewa,khalti,stripe',
            ]);

            
            $package = TourPackage::findOrFail($validated['tour_package_id']);

            
            if (!$package->is_active) {
                throw ValidationException::withMessages([
                    'tour_package_id' => 'This trek package is not available.',
                ]);
            }

            
            if ($validated['participants'] > $package->max_participants) {
                throw ValidationException::withMessages([
                    'participants' => "Maximum {$package->max_participants} participants allowed.",
                ]);
            }

            
            $activeBooking = TourBooking::where('user_id', auth()->id())
                ->active()
                ->first();

            if ($activeBooking) {
                throw ValidationException::withMessages([
                    'booking' => 'You already have an active booking. Please complete or cancel it first.',
                ]);
            }

          
            $totalAmount = $package->price * $validated['participants'];

          
            DB::beginTransaction();

            $booking = TourBooking::create([
                'user_id' => auth()->id(),
                'tour_package_id' => $package->id,
                'tour_date' => $validated['tour_date'],
                'participants' => $validated['participants'],
                'total_amount' => $totalAmount,
                'payment_method' => $validated['payment_method'],
                'status' => TourBooking::STATUS_PENDING,
            ]);

            DB::commit();

            Log::info('Booking created successfully', [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'payment_method' => $booking->payment_method,
            ]);

            
            return $this->redirectToPayment($booking);

        } catch (ValidationException $e) {
            Log::warning('Booking validation failed', [
                'errors' => $e->errors(),
                'user_id' => auth()->id(),
            ]);

            return back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Booking creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            return back()
                ->with('error', 'Failed to create booking. Please try again.')
                ->withInput();
        }
    }

    
    private function redirectToPayment(TourBooking $booking)
    {
        $routes = [
            'esewa' => 'payment.esewa',
            'khalti' => 'payment.test',
            'stripe' => 'payment.stripe',
        ];

        $routeName = $routes[$booking->payment_method] ?? 'payment.test';

        Log::info('Redirecting to payment', [
            'booking_id' => $booking->id,
            'payment_method' => $booking->payment_method,
            'route' => $routeName,
        ]);

        return redirect()
            ->route($routeName, $booking)
            ->with('success', 'Booking created! Complete payment to confirm.');
    }

    
    public function index()
    {
        $bookings = TourBooking::where('user_id', auth()->id())
            ->with(['tourPackage', 'trackingPin'])
            ->latest()
            ->get();

        return view('bookings.index', compact('bookings'));
    }

 
  
    public function show(TourBooking $booking)
    {
        if ($booking->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        $booking->load([
            'tourPackage.checkpoints',
            'checkpointProgress',
            'trackingPin'
        ]);

        return view('bookings.show', compact('booking'));
    }

    
    public function start(TourBooking $booking)
    {
        
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

       
        if (!$booking->isConfirmed()) {
            return back()->with('error', 'Only confirmed bookings can be started.');
        }

        
        if ($booking->isActive()) {
            return redirect()
                ->route('tracking.traveler', $booking)
                ->with('info', 'Trek already in progress.');
        }

        try {
            DB::beginTransaction();

          
            $pin = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

           
            $booking->trackingPin()->create([
                'pin' => $pin,
                'expires_at' => now()->addHours(600),
            ]);

          
            $booking->markAsActive();

            DB::commit();

            Log::info('Trek started', [
                'booking_id' => $booking->id,
                'pin' => $pin,
            ]);

            return redirect()
                ->route('tracking.traveler', $booking)
                ->with('success', "Trek started! Your tracking PIN is: {$pin}");

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Trek start failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to start trek. Please try again.');
        }
    }

    
    public function cancel(TourBooking $booking)
    {
      
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        
        if ($booking->isCompleted()) {
            return back()->with('error', 'Completed bookings cannot be cancelled.');
        }

        if ($booking->isCancelled()) {
            return back()->with('info', 'Booking is already cancelled.');
        }

        $booking->markAsCancelled();

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Booking cancelled successfully.');
    }
}