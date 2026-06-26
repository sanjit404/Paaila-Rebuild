@php
use Illuminate\Support\Str;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: Arial, Helvetica, sans-serif; background: #f0f4f0; color: #333; }
  .wrapper { max-width: 620px; margin: 32px auto; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }

  .header { background: #2E7D32; padding: 36px 40px; text-align: center; }
  .header .brand { font-size: 22px; font-weight: 800; color: #fff; letter-spacing: -0.3px; }
  .header .tagline { color: rgba(255,255,255,0.75); font-size: 13px; margin-top: 4px; }

  .status-bar { background: #E8F5E9; border-bottom: 2px solid #C8E6C9; padding: 14px 40px; display: flex; align-items: center; gap: 10px; }
  .status-dot { width: 10px; height: 10px; background: #2E7D32; border-radius: 50%; flex-shrink: 0; }
  .status-text { font-size: 13px; font-weight: 600; color: #555; }

  .body { padding: 32px 40px; }
  .greeting { font-size: 20px; font-weight: 700; margin-bottom: 8px; }
  .intro { font-size: 14px; color: #666; line-height: 1.6; margin-bottom: 28px; }

  .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #2E7D32; margin-bottom: 12px; }

  .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 28px; }
  .detail-table td { padding: 10px 0; border-bottom: 1px solid #F0F0F0; font-size: 14px; vertical-align: top; }
  .detail-table td:first-child { color: #888; width: 45%; }
  .detail-table td:last-child { font-weight: 600; color: #222; text-align: right; }
  .detail-table tr:last-child td { border-bottom: none; }

  .total-box { background: #E8F5E9; border: 2px solid #2E7D32; border-radius: 8px; padding: 20px 24px; margin-bottom: 28px; display: flex; justify-content: space-between; align-items: center; }
  .total-label { font-size: 15px; font-weight: 700; color: #333; }
  .total-amount { font-size: 30px; font-weight: 800; color: #2E7D32; }

  .route-box { margin-bottom: 28px; }
  .route-point { display: flex; gap: 14px; align-items: flex-start; margin-bottom: 0; }
  .route-line-wrap { display: flex; flex-direction: column; align-items: center; flex-shrink: 0; }
  .route-dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; margin-top: 3px; }
  .route-dot.start { background: #2E7D32; }
  .route-dot.checkpoint { background: #81C784; border: 2px solid #2E7D32; }
  .route-dot.end { background: #1B5E20; }
  .route-connector { width: 2px; background: #C8E6C9; flex-grow: 1; min-height: 20px; margin: 2px 0; }
  .route-label { font-size: 13px; font-weight: 600; color: #333; }
  .route-sub { font-size: 12px; color: #888; margin-top: 2px; margin-bottom: 16px; }

  .info-note { background: #FFF8E1; border-left: 4px solid #FFA726; padding: 14px 16px; border-radius: 0 6px 6px 0; margin-bottom: 28px; font-size: 13px; color: #5D4037; line-height: 1.6; }

  .cta { text-align: center; margin-bottom: 28px; }
  .cta a { display: inline-block; background: #2E7D32; color: #fff; text-decoration: none; padding: 14px 40px; border-radius: 8px; font-weight: 700; font-size: 15px; }

  .footer { background: #F9F9F9; padding: 20px 40px; text-align: center; font-size: 12px; color: #AAA; border-top: 1px solid #EEE; line-height: 1.8; }
  .footer a { color: #2E7D32; text-decoration: none; }

  .tag { display: inline-block; background: #E8F5E9; color: #2E7D32; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; margin-right: 6px; margin-bottom: 4px; text-transform: capitalize; }

  .divider { border: none; border-top: 1px solid #F0F0F0; margin: 24px 0; }

  @media (max-width: 600px) {
    .wrapper { margin: 0; border-radius: 0; }
    .header, .status-bar, .body, .footer { padding-left: 20px; padding-right: 20px; }
    .total-box { flex-direction: column; align-items: flex-start; gap: 12px; }
    .total-amount { font-size: 26px; }
    .detail-table td:last-child { text-align: left; }
  }
</style>
</head>
<body>
<div class="wrapper">

  <div class="header">
    <div class="brand">Paaila - Every Step Matters</div>
    <div class="tagline">www.paaila.me</div>
  </div>

  <div class="status-bar">
    <div class="status-dot"></div>
    <div class="status-text" style="margin-left:5px;">Booking Completed</div>
  </div>

  <div class="body">

    <p class="greeting">Namaste, {{ $user->name }}</p>
    <p class="intro">
      Your booking for <strong>{{ $package->name }}</strong> has been completed.
      Your can now book the next adventure.
    </p>

    <div class="section-title">Booking Details</div>
    <table class="detail-table">
      <tr>
        <td>Booking Number</td>
        <td>#{{ $booking->booking_number }}</td>
      </tr>
      <tr>
        <td>Trek Package</td>
        <td>{{ $package->name }}</td>
      </tr>
      <tr>
        <td>Start Date</td>
        <td>{{ \Carbon\Carbon::parse($booking->tour_date)->format('l, F j, Y') }}</td>
      </tr>
      <tr>
        <td>End Date (est.)</td>
        <td>{{ \Carbon\Carbon::parse($booking->tour_date)->addDays($package->duration_days)->format('l, F j, Y') }}</td>
      </tr>
      <tr>
        <td>Duration</td>
        <td>{{ $package->duration_days }} {{ Str::plural('Day', $package->duration_days) }}</td>
      </tr>
      <tr>
        <td>Trekkers</td>
        <td>{{ $booking->participants }} {{ Str::plural('Person', $booking->participants) }}</td>
      </tr>
      <tr>
        <td>Payment Method</td>
        <td style="text-transform: uppercase;">{{ $booking->payment_method }}</td>
      </tr>
    </table>


    <div class="total-box">
      <div>
        <div class="total-label">Total Paid</div>
        <div style="font-size:12px; color:#777; margin-top:3px;">Rs. {{ number_format($package->price, 0) }} × {{ $booking->participants }} {{ Str::plural('person', $booking->participants) }}</div>
      </div>
      <div class="total-amount">Rs. {{ number_format($booking->total_amount, 0) }}</div>
    </div>

    <hr class="divider">

    <div class="info-note">
      <strong>Live GPS Tracking</strong><br>
      Once your trek begins, you will receive a 6-digit tracking PIN. Share it with family or friends so they can monitor your checkpoint progress in real time through Paaila's parent monitoring view.
    </div>

    <div class="cta">
      <a href="{{ route('home') }}">Explore Treks</a>
    </div>

    <p style="font-size:13px; color:#888; text-align:center;">
      Questions? Email us at <a href="mailto:support@paaila.me" style="color:#2E7D32;">support@paaila.me</a>
    </p>

  </div>

  <div class="footer">
    © {{ date('Y') }} Paaila - Every step matters · Nepal<br>
    You received this because you made a booking on <a href="https://paaila.me">paaila.me</a>
  </div>

  
<center>
    <div>
    <img src="https://paaila.me/images/paailaLogo.png" width="120">
    <br>
</div>
<div>
    Paaila - Every Step Matters
    <br>
</div>
<div>
    <a target="_blank" href="https://paaila.me">
        https://paaila.me
    </a>
    <br>
</div>
</center>


</div>
</body>
</html>