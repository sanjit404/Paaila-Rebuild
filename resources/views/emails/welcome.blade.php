<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: Arial, Helvetica, sans-serif; background: #f4f7f4; color: #2b2b2b; }
  .wrapper { max-width: 640px; margin: 32px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 6px 24px rgba(0,0,0,0.08); }
  .header { background: linear-gradient(135deg, #2E7D32, #256b2a); padding: 36px 40px; text-align: center; }
  .brand { font-size: 26px; font-weight: 800; color: #ffffff; letter-spacing: 0.2px; }
  .tagline { color: rgba(255,255,255,0.82); font-size: 13px; margin-top: 8px; }
  .body { padding: 38px 40px; }
  .greeting { font-size: 22px; font-weight: 800; color: #1f1f1f; margin-bottom: 12px; }
  .intro { font-size: 14px; color: #5a5a5a; line-height: 1.8; margin-bottom: 30px; }
  .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.2px; color: #2E7D32; margin-bottom: 16px; }
  .step { display: flex; gap: 16px; align-items: flex-start; margin-bottom: 20px; }
  .step-num { width: 34px; height: 34px; border-radius: 50%; background: #E8F5E9; color: #2E7D32; font-weight: 800; font-size: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid #CFE5D0; }
  .step-body { padding-top: 3px; }
  .step-title { font-size: 14px; font-weight: 700; color: #1f1f1f; margin-bottom: 4px; }
  .step-sub { font-size: 13px; color: #666; line-height: 1.6; }
  .highlight-box { background: #F3F8F3; border: 1px solid #DDEBDD; border-radius: 10px; padding: 20px 22px; margin: 28px 0; }
  .hl-title { font-size: 14px; font-weight: 700; color: #2E7D32; margin-bottom: 8px; }
  .hl-body { font-size: 13px; color: #4d4d4d; line-height: 1.7; }
  .cta { text-align: center; margin: 30px 0 26px; }
  .cta a { display: inline-block; background: #2E7D32; color: #ffffff; text-decoration: none; padding: 14px 42px; border-radius: 8px; font-weight: 700; font-size: 15px; }
  .cta a:hover { background: #256b2a; }
  .divider { border: none; border-top: 1px solid #ececec; margin: 28px 0; }
  .footer { background: #fafafa; padding: 20px 40px; text-align: center; font-size: 12px; color: #8a8a8a; border-top: 1px solid #eeeeee; line-height: 1.8; }
  .footer a { color: #2E7D32; text-decoration: none; }
  @media (max-width: 600px) {
    .wrapper { margin: 0; border-radius: 0; }
    .header, .body, .footer { padding-left: 22px; padding-right: 22px; }
    .greeting { font-size: 20px; }
  }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <div class="brand">Paaila - Every step matters</div>
    <div class="tagline">www.paaila.me</div>
  </div>

  <div class="body">
    <p class="greeting">Namaste, {{ $user->name }}</p>
    <p class="intro">
      Welcome to Paaila. Your account is now active, and you are ready to begin planning safe, personalised trekking experiences across Nepal.
    </p>

    <hr class="divider">

    <div class="highlight-box">
      <div class="hl-title">What makes Paaila different</div>
      <div class="hl-body">
        Paaila uses real-time GPS geofencing to automatically mark checkpoints as you move through the route, giving you and your family greater visibility and peace of mind.
      </div>
    </div>

    <div class="cta">
      <a href="https://www.paaila.me">Explore Treks Now</a>
    </div>

    <p style="font-size:13px; color:#7a7a7a; text-align:center; line-height:1.7;">
      Need assistance? Contact <a href="mailto:support@paaila.me" style="color:#2E7D32;">support@paaila.me</a>.
    </p>
  </div>

  <div class="footer">
    © {{ date('Y') }} Paaila Treks · Kathmandu, Nepal<br>
    You received this email because you created an account on <a href="https://www.paaila.me">Paaila.me</a>

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

</div>
</body>
</html>