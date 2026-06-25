<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - JENIUS</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Inter:wght@400;600&display=swap&subset=latin" rel="stylesheet">
<style>
:root{
  --navy-deep:#0b2b22;--navy-mid:#123d30;--navy-light:#1c5544;
  --gold:#c9a857;--gold-soft:#e3cd8c;--cream:#f6f3ea;
  --ink:#101a16;--muted:#6b7a72;--danger:#c0564a;
}
*{margin:0;padding:0;box-sizing:border-box;}
html,body{height:100%;font-family:'Inter',-apple-system,sans-serif;background:var(--navy-deep);color:var(--ink);overflow:hidden;}
.sr-only{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;}
@media(prefers-reduced-motion:reduce){*,*::before,*::after{animation-duration:0.001ms!important;animation-iteration-count:1!important;transition-duration:0.001ms!important;}}
html.tab-hidden *{animation-play-state:paused!important;}

/* SCENE */
.scene{position:fixed;inset:0;z-index:0;background:linear-gradient(180deg,#c6fcff 0%,#c6fcff 12%,#84ecf1 25%,#c6fcff 42%,#c6fcff 60%,#c6fcff 82%,#c6fcff 100%);overflow:hidden;}

/* LANGIT */
.sky-layer{position:absolute;inset:0;pointer-events:none;}
.sky-layer::before{
  content:"";position:absolute;inset:0;
  background:
    radial-gradient(ellipse 800px 350px at 30% 5%, rgba(205, 255, 254, 0.6) 0%, transparent 65%),
    radial-gradient(ellipse 500px 200px at 30% 8%, rgba(40,90,60,0.3) 0%, transparent 55%),
    radial-gradient(ellipse 300px 120px at 30% 10%, rgba(201,168,87,0.08) 0%, transparent 50%);
}

/* BULAN */
.moon{
  position:absolute;top:6%;left:28%;
  width:60px;height:60px;border-radius:50%;
  background:radial-gradient(circle at 38% 38%, #fffde8, #f5e88a 40%, #c9a857 75%, transparent 100%);
  box-shadow:0 0 40px 16px rgba(255, 183, 0, 0.18),0 0 90px 40px rgb(255, 234, 0);
  pointer-events:none;
  animation:moonGlow 8s ease-in-out infinite;
}
@keyframes moonGlow{
  0%,100%{box-shadow:0 0 40px 16px rgba(201,168,87,0.18),0 0 90px 40px rgba(201,168,87,0.08);}
  50%{box-shadow:0 0 55px 22px rgba(201,168,87,0.26),0 0 120px 55px rgba(201,168,87,0.13);}
}

/* CAHAYA BULAN DI AIR */
.moonpath{
  position:absolute;pointer-events:none;
  left:27%;top:48%;width:6%;height:52%;
  background:linear-gradient(180deg,rgba(240,220,130,0.18) 0%,rgba(201,168,87,0.08) 40%,transparent 100%);
  filter:blur(8px);
  animation:moonpathShimmer 5s ease-in-out infinite;
}
.moonpath::after{
  content:"";position:absolute;left:20%;right:20%;top:0;bottom:0;
  background:linear-gradient(180deg,rgba(255,248,190,0.22) 0%,transparent 80%);
  filter:blur(4px);
  animation:moonpathShimmer 3.5s ease-in-out infinite reverse;
}
@keyframes moonpathShimmer{0%,100%{opacity:0.7;transform:scaleX(1);}50%{opacity:1;transform:scaleX(1.18);}}

/* CAKRAWALA */
.horizon{
  position:absolute;left:0;right:0;top:48%;height:3px;pointer-events:none;
  background:linear-gradient(90deg,transparent 0%,rgba(201,168,87,0.12) 15%,rgba(255,248,210,0.32) 30%,rgba(201,168,87,0.15) 52%,transparent 75%);
}
.horizon::before{
  content:"";position:absolute;left:0;right:0;top:-1px;height:1px;
  background:linear-gradient(90deg,transparent 0%,rgba(255,255,255,0.08) 25%,rgba(255,255,255,0.18) 30%,rgba(255,255,255,0.08) 50%,transparent 70%);
}

/* WATERMARK */
.watermark{
  position:absolute;left:-2%;top:50%;transform:translateY(-50%);
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(140px,20vw,220px);font-weight:700;letter-spacing:0.08em;
  color:transparent;-webkit-text-stroke:1px rgba(201,168,87,0.06);
  line-height:1;pointer-events:none;white-space:nowrap;user-select:none;
}

/* OMBAK — 6 lapisan parallax */
.wave-container{position:absolute;left:0;right:0;bottom:0;pointer-events:none;overflow:hidden;}
.wave-svg{position:absolute;left:0;right:0;width:200%;pointer-events:none;}
.wl1{bottom:52%;height:28px;opacity:0.35;animation:waveDrift1 22s linear infinite;}
.wl2{bottom:50%;height:35px;opacity:0.45;animation:waveDrift2 18s linear infinite;}
.wl3{bottom:48.5%;height:40px;opacity:0.55;animation:waveDrift1 14s linear infinite reverse;}
.wl4{bottom:47%;height:48px;opacity:0.65;animation:waveDrift2 10s linear infinite;}
.wl5{bottom:44.5%;height:55px;opacity:0.7;animation:waveDrift1 7.5s linear infinite reverse;}
.wl6{bottom:42%;height:65px;opacity:0.75;animation:waveDrift2 5.5s linear infinite;}
@keyframes waveDrift1{from{transform:translateX(0);}to{transform:translateX(-50%);}}
@keyframes waveDrift2{from{transform:translateX(-50%);}to{transform:translateX(0);}}

/* OCEAN BODY */
.ocean-body{
  position:absolute;left:0;right:0;top:48%;bottom:0;pointer-events:none;
  background:linear-gradient(180deg,#3ea19b 0%,#108482 30%,#13407f 100%);
}

/* RIAK HORIZONTAL */
.ripple-layer{position:absolute;left:0;right:0;pointer-events:none;top:48%;height:25%;overflow:hidden;}
.ripple-line{
  position:absolute;left:0;right:0;height:1px;
  background:linear-gradient(90deg,transparent 0%,rgba(180,240,200,0.08) 20%,rgba(200,255,220,0.15) 50%,rgba(180,240,200,0.08) 80%,transparent 100%);
  animation:rippleMove 8s ease-in-out infinite;
}
.ripple-line:nth-child(1){top:8%;animation-duration:7s;animation-delay:0s;opacity:0.9;}
.ripple-line:nth-child(2){top:16%;animation-duration:9s;animation-delay:-1.5s;opacity:0.6;}
.ripple-line:nth-child(3){top:26%;animation-duration:6s;animation-delay:-3s;opacity:0.8;}
.ripple-line:nth-child(4){top:36%;animation-duration:11s;animation-delay:-2s;opacity:0.5;}
.ripple-line:nth-child(5){top:48%;animation-duration:8s;animation-delay:-4s;opacity:0.7;}
.ripple-line:nth-child(6){top:62%;animation-duration:7.5s;animation-delay:-0.5s;opacity:0.55;}
.ripple-line:nth-child(7){top:75%;animation-duration:10s;animation-delay:-2.5s;opacity:0.4;}
@keyframes rippleMove{0%,100%{transform:scaleX(0.85);opacity:0.4;}50%{transform:scaleX(1.15);opacity:1;}}

/* GLITTER */
.glitter-canvas{position:absolute;left:0;right:0;top:48%;bottom:0;pointer-events:none;overflow:hidden;}
.glitter{
  position:absolute;width:2px;height:2px;border-radius:50%;
  background:#e3cd8c;
  animation:glitterFlash var(--dur,3s) ease-in-out var(--delay,0s) infinite;
  opacity:0;
}
@keyframes glitterFlash{0%,100%{opacity:0;transform:scale(0.5);}50%{opacity:var(--peak,0.8);transform:scale(1.8);}}

/* BUIH MENGAMBANG */
.foam-layer{position:absolute;left:0;right:0;top:47.5%;height:6%;pointer-events:none;overflow:hidden;}
.foam-blob{
  position:absolute;border-radius:50%;
  background:rgba(255,255,255,0.12);
  animation:foamDrift var(--fd,12s) linear var(--fdelay,0s) infinite;
}
@keyframes foamDrift{
  0%{transform:translateX(110vw) scaleX(1);opacity:0;}
  5%{opacity:1;}95%{opacity:0.6;}
  100%{transform:translateX(-10vw) scaleX(0.8);opacity:0;}
}

/* KABUT LAUT */
.sea-mist{
  position:absolute;left:0;right:0;pointer-events:none;
  top:44%;height:12%;
  background:linear-gradient(180deg,transparent 0%,rgba(20,50,35,0.18) 50%,transparent 100%);
  filter:blur(12px);
  animation:mistFloat 20s ease-in-out infinite;
}
.sea-mist:nth-child(2){animation-delay:-7s;animation-duration:25s;opacity:0.7;}
.sea-mist:nth-child(3){animation-delay:-13s;animation-duration:18s;opacity:0.5;}
@keyframes mistFloat{0%,100%{transform:translateX(-3%);opacity:0.5;}50%{transform:translateX(3%);opacity:1;}}

/* KILAT JAUH */
.lightning{
  position:absolute;pointer-events:none;
  top:30%;left:72%;width:2px;height:50px;
  background:linear-gradient(180deg,rgba(255,255,255,0),rgba(255,255,255,0.6),rgba(255,255,255,0));
  opacity:0;animation:lightningFlash 15s ease-in-out 4s infinite;filter:blur(1px);
}
.lightning::after{
  content:"";position:absolute;top:60%;left:-4px;width:10px;height:30px;
  background:linear-gradient(180deg,rgba(255,255,255,0.4),transparent);
  transform:rotate(15deg);filter:blur(1px);
}
@keyframes lightningFlash{
  0%,96%,100%{opacity:0;}97%{opacity:0.9;}98%{opacity:0.2;}99%{opacity:0.7;}
}
.lightning-glow{
  position:absolute;top:25%;left:65%;width:160px;height:120px;
  background:radial-gradient(ellipse,rgba(200,220,255,0.12) 0%,transparent 70%);
  opacity:0;pointer-events:none;
  animation:lightningFlash 15s ease-in-out 4s infinite;filter:blur(8px);
}

/* AWAN MALAM */
.cloud{
  position:absolute;pointer-events:none;border-radius:50px;
  background:rgb(255, 255, 255);filter:blur(18px);
  animation:cloudDrift var(--cspeed,80s) linear var(--cdelay,0s) infinite;
}
@keyframes cloudDrift{from{transform:translateX(110vw);}to{transform:translateX(-110vw);}}

/* KAPAL */
.ship-stage{
  position:absolute;top:50%;left:35%;
  width:900px;height:900px;
  transform:translate(-50%,-50%);z-index:2;
}
.fleet-group{
  transform-origin:500px 500px;will-change:transform;
  animation:fleetFloat 12s ease-in-out infinite;
}
@keyframes fleetFloat{0%,100%{transform:translateY(0px);}50%{transform:translateY(-10px);}}
.ship-light{animation:lightBlink 3s ease-in-out infinite;}
.ship-light.slow{animation-duration:4.5s;}
.ship-light.green{animation-delay:1.2s;}
@keyframes lightBlink{0%,100%{opacity:0.6;}50%{opacity:1;}}
.wake{opacity:0.3;animation:fadePulse 6s ease-in-out infinite;}
.foam{animation:fadePulse 4s ease-in-out infinite;}
@keyframes fadePulse{0%,100%{opacity:0.4;}50%{opacity:0.8;}}
.bow-wave{
  stroke-dasharray:120 240;stroke-dashoffset:0;
  animation:bowWaveScroll 3s linear infinite;
}
@keyframes bowWaveScroll{to{stroke-dashoffset:-360;}}

/* BRAND */
.brand-lockup{position:fixed;top:32px;left:40px;z-index:20;display:flex;align-items:flex-start;gap:14px;}
.emblem{width:50px;height:50px;flex-shrink:0;}
.emblem-ring{stroke-dasharray:100;stroke-dashoffset:100;animation:ringDraw 1s ease-out 0.15s forwards;}
.emblem-icon{stroke-dasharray:100;stroke-dashoffset:100;opacity:0;animation:iconDraw 0.9s ease-out 0.55s forwards;}
@keyframes ringDraw{to{stroke-dashoffset:0;}}
@keyframes iconDraw{to{stroke-dashoffset:0;opacity:1;}}
.brand-copy{padding-top:2px;}
.brand-word{position:relative;display:inline-block;overflow:hidden;font-family:'Cormorant Garamond',serif;font-size:60px;font-weight:700;letter-spacing:4px;line-height:1;color:rgb(1, 89, 77);}
.brand-word .letters span{display:inline-block;opacity:0;transform:translateY(12px);animation:letterIn 0.55s cubic-bezier(.2,.8,.2,1) forwards;animation-delay:calc(0.55s + var(--d) * 70ms);}
@keyframes letterIn{to{opacity:1;transform:translateY(0);}}
.brand-word .shine{position:absolute;top:0;left:-50px;width:40px;height:100%;background:linear-gradient(115deg,transparent,rgba(255,255,255,0.55),transparent);transform:translateX(0) skewX(-12deg);opacity:0;animation:shineSweep 8s ease-in-out 1.7s infinite;}
@keyframes shineSweep{0%{transform:translateX(0) skewX(-12deg);opacity:0;}8%{opacity:0.85;}32%{transform:translateX(280px) skewX(-12deg);opacity:0;}100%{transform:translateX(280px) skewX(-12deg);opacity:0;}}
.brand-tagline{margin-top:8px;font-size:18px;font-weight:600;letter-spacing:0.9px;text-transform:uppercase;color:rgb(0, 102, 88);line-height:1.45;opacity:0;transform:translateY(6px);animation:taglineIn 0.6s ease-out 1.05s forwards;}
@keyframes taglineIn{to{opacity:1;transform:translateY(0);}}

/* LOGIN CARD */
.login-wrapper{position:fixed;inset:0;z-index:10;display:flex;align-items:center;justify-content:flex-end;padding:24px 8% 24px 24px;}
.login-card{width:400px;background:linear-gradient(180deg,rgba(246,243,234,0.99),rgba(236, 246, 234, 0.508));border-radius:4px;padding:46px 42px 38px;box-shadow:0 24px 60px rgba(0,0,0,0.38),0 0 0 1px rgba(201,168,87,0.22);position:relative;}
.login-card::before,.login-card::after{content:"";position:absolute;width:22px;height:22px;border:1.5px solid var(--gold);pointer-events:none;}
.login-card::before{top:10px;left:10px;border-right:none;border-bottom:none;}
.login-card::after{bottom:10px;right:10px;border-left:none;border-top:none;}
.login-header{margin-bottom:24px;}
.login-header h1{font-family:'Cormorant Garamond',serif;font-size:27px;font-weight:600;color:var(--navy-deep);margin-bottom:6px;letter-spacing:0.3px;}
.login-header p{font-size:13px;color:var(--muted);}
.divider-gold{height:1px;margin-bottom:26px;background:linear-gradient(90deg,var(--gold) 0%,rgba(201,168,87,0.12) 60%,transparent 100%);}
.form-group{margin-bottom:19px;}
.form-group label{display:block;font-size:11px;font-weight:600;color:var(--navy-mid);margin-bottom:7px;letter-spacing:0.8px;text-transform:uppercase;}
.form-control{width:100%;padding:12px 14px;border:1.5px solid #d8d2c2;border-radius:3px;font-size:14px;font-family:'Inter',sans-serif;background:#fbfaf6;color:var(--ink);transition:border-color 0.18s,box-shadow 0.18s;}
.form-control::placeholder{color:#a8a190;}
.form-control:focus{outline:none;border-color:var(--gold);background:#fff;box-shadow:0 0 0 3px rgba(201,168,87,0.16);}
.form-control.is-invalid{border-color:var(--danger);background:#fbf1ef;}
.invalid-feedback{display:block;color:var(--danger);font-size:12px;margin-top:5px;font-weight:500;}
.btn-login{width:100%;padding:13px;background:linear-gradient(135deg,#1c5544 0%,#0b2b22 100%);color:var(--cream);border:1px solid var(--gold);border-radius:3px;font-size:13.5px;font-weight:600;cursor:pointer;letter-spacing:1.4px;text-transform:uppercase;transition:transform 0.15s,box-shadow 0.18s;margin-top:10px;}
.btn-login:hover{transform:translateY(-1px);box-shadow:0 8px 20px rgba(11,43,34,0.35);}
.btn-login:active{transform:translateY(0);}
.btn-login:focus-visible{outline:2px solid var(--gold-soft);outline-offset:2px;}
.spinner{display:none;width:16px;height:16px;border:2px solid rgba(246,243,234,0.3);border-top-color:var(--gold);border-radius:50%;animation:spin 0.8s linear infinite;margin:0 auto;}
@keyframes spin{to{transform:rotate(360deg);}}
.btn-login.loading .spinner{display:block;}
.btn-login.loading .btn-text{display:none;}
.card-footer-row{margin-top:24px;display:flex;align-items:center;justify-content:space-between;gap:10px;}
.clock-display{font-family:'Inter',monospace;font-size:12px;color:var(--muted);font-weight:600;letter-spacing:1px;font-variant-numeric:tabular-nums;}
.card-footer{font-size:9.5px;color:#a8a190;font-weight:500;letter-spacing:0.5px;}

@media(max-width:600px){
  .login-wrapper{justify-content:center;padding:20px;}
  .login-card{width:100%;max-width:380px;padding:36px 26px 30px;}
  .ship-stage{left:50%;opacity:0.45;top:32%;width:720px;height:720px;}
  .brand-lockup{top:20px;left:20px;gap:10px;}
  .emblem{width:38px;height:38px;}
  .brand-word{font-size:21px;letter-spacing:2px;}
  .brand-tagline{font-size:8.5px;}
}
a:focus-visible,button:focus-visible,input:focus-visible{outline:2px solid var(--gold);outline-offset:2px;}
</style>
</head>
<body>

<div class="scene">
  <div class="sky-layer"></div>

  <!-- Bulan -->
  <div class="moon" aria-hidden="true"></div>
  <!-- Jalan cahaya bulan di air -->
  <div class="moonpath" aria-hidden="true"></div>

  <!-- Bintang-bintang -->
  <svg style="position:absolute;inset:0;width:100%;height:100%;pointer-events:none;" viewBox="0 0 1400 800" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
    <g fill="rgba(255,248,220,0.55)">
      <circle cx="82" cy="42" r="0.8"/><circle cx="155" cy="28" r="1.0"/>
      <circle cx="220" cy="68" r="0.7"/><circle cx="310" cy="18" r="0.9"/>
      <circle cx="390" cy="52" r="1.1"/><circle cx="455" cy="32" r="0.8"/>
      <circle cx="540" cy="14" r="0.7"/><circle cx="612" cy="58" r="1.0"/>
      <circle cx="680" cy="35" r="0.8"/><circle cx="740" cy="72" r="0.9"/>
      <circle cx="820" cy="22" r="1.0"/><circle cx="900" cy="48" r="0.7"/>
      <circle cx="960" cy="18" r="1.1"/><circle cx="42" cy="85" r="0.7"/>
      <circle cx="128" cy="105" r="0.9"/><circle cx="194" cy="92" r="0.8"/>
      <circle cx="275" cy="78" r="1.0"/><circle cx="348" cy="112" r="0.7"/>
      <circle cx="432" cy="88" r="0.9"/><circle cx="510" cy="98" r="1.0"/>
      <circle cx="590" cy="76" r="0.8"/><circle cx="660" cy="115" r="0.7"/>
      <circle cx="728" cy="95" r="1.1"/><circle cx="796" cy="62" r="0.8"/>
      <circle cx="870" cy="108" r="0.9"/><circle cx="940" cy="82" r="0.7"/>
      <circle cx="1050" cy="38" r="0.8"/><circle cx="1120" cy="60" r="1.0"/>
      <circle cx="1200" cy="25" r="0.7"/><circle cx="1280" cy="52" r="0.9"/>
      <circle cx="1350" cy="38" r="1.1"/><circle cx="1050" cy="90" r="0.7"/>
      <circle cx="1150" cy="110" r="0.9"/><circle cx="1250" cy="78" r="0.8"/>
    </g>
    <g fill="rgba(255,248,220,0.85)">
      <circle cx="188" cy="44" r="1.4"/><circle cx="476" cy="22" r="1.6"/>
      <circle cx="720" cy="16" r="1.3"/><circle cx="860" cy="38" r="1.5"/>
      <circle cx="105" cy="120" r="1.4"/><circle cx="640" cy="44" r="1.3"/>
      <circle cx="1100" cy="28" r="1.5"/><circle cx="1320" cy="18" r="1.4"/>
    </g>
    <!-- Bintang berkedip (4 bintang) -->
    <circle cx="340" cy="35" r="1.5" fill="rgba(255,248,220,0.9)">
      <animate attributeName="opacity" values="0.3;1;0.3" dur="4s" repeatCount="indefinite"/>
    </circle>
    <circle cx="780" cy="28" r="1.3" fill="rgba(255,248,220,0.9)">
      <animate attributeName="opacity" values="1;0.2;1" dur="5.5s" repeatCount="indefinite"/>
    </circle>
    <circle cx="1180" cy="42" r="1.4" fill="rgba(255,248,220,0.9)">
      <animate attributeName="opacity" values="0.5;1;0.2;0.8;0.5" dur="3.8s" repeatCount="indefinite"/>
    </circle>
    <circle cx="520" cy="15" r="1.2" fill="rgba(255,248,220,0.9)">
      <animate attributeName="opacity" values="0.8;0.1;0.9;0.4;0.8" dur="6s" repeatCount="indefinite"/>
    </circle>
    <!-- Rasi bintang tipis -->
    <g stroke="rgba(255,248,220,0.12)" stroke-width="0.5" fill="none">
      <polyline points="155,28 188,44 220,68"/>
      <polyline points="476,22 540,14 612,58 680,35"/>
      <polyline points="1100,28 1120,60 1050,90"/>
    </g>
  </svg>

  <!-- Awan malam -->
  <div class="cloud" style="top:8%;width:280px;height:35px;--cspeed:95s;--cdelay:0s;"></div>
  <div class="cloud" style="top:14%;width:180px;height:22px;--cspeed:130s;--cdelay:-40s;opacity:0.6;"></div>
  <div class="cloud" style="top:5%;width:350px;height:40px;--cspeed:110s;--cdelay:-65s;opacity:0.8;"></div>
  <div class="cloud" style="top:18%;width:220px;height:28px;--cspeed:75s;--cdelay:-20s;opacity:0.5;"></div>

  <!-- Kilat jauh -->
  <div class="lightning" aria-hidden="true"></div>
  <div class="lightning-glow" aria-hidden="true"></div>

  <!-- Watermark tipografi -->
  <div class="watermark" aria-hidden="true">JENIUS</div>

  <!-- Cakrawala -->
  <div class="horizon" aria-hidden="true"></div>

  <!-- Kabut laut -->
  <div class="sea-mist" aria-hidden="true"></div>
  <div class="sea-mist" aria-hidden="true"></div>
  <div class="sea-mist" aria-hidden="true"></div>

  <!-- Riak horizontal di laut -->
  <div class="ripple-layer" aria-hidden="true">
    <div class="ripple-line"></div>
    <div class="ripple-line"></div>
    <div class="ripple-line"></div>
    <div class="ripple-line"></div>
    <div class="ripple-line"></div>
    <div class="ripple-line"></div>
    <div class="ripple-line"></div>
  </div>

  <!-- Kilatan cahaya di air -->
  <div class="glitter-canvas" id="glitterCanvas" aria-hidden="true"></div>

  <!-- Masa air laut -->
  <div class="ocean-body" aria-hidden="true"></div>

  <!-- OMBAK 6 LAPISAN -->
  <div class="wave-container" aria-hidden="true">
    <!-- Layer 1 — paling jauh -->
    <svg class="wave-svg wl1" viewBox="0 0 1440 28" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M0,14 C180,6 360,22 540,14 C720,6 900,22 1080,14 C1260,6 1440,22 1440,14 L1440,28 L0,28Z" fill="rgba(15,45,30,0.6)"/>
      <path d="M0,18 C200,10 400,24 600,18 C800,10 1000,24 1200,18 C1400,10 1440,22 1440,18 L1440,28 L0,28Z" fill="rgba(20,55,35,0.4)"/>
    </svg>
    <!-- Layer 2 -->
    <svg class="wave-svg wl2" viewBox="0 0 1440 35" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M0,18 C160,8 320,28 480,18 C640,8 800,28 960,18 C1120,8 1280,28 1440,18 L1440,35 L0,35Z" fill="rgba(12,38,25,0.7)"/>
      <path d="M0,22 C240,12 480,30 720,22 C960,12 1200,30 1440,22 L1440,35 L0,35Z" fill="rgba(8,28,18,0.5)"/>
      <path d="M0,18 C160,8 320,28 480,18 C640,8 800,28 960,18 C1120,8 1280,28 1440,18" fill="none" stroke="rgba(255,255,255,0.08)" stroke-width="1.5"/>
    </svg>
    <!-- Layer 3 -->
    <svg class="wave-svg wl3" viewBox="0 0 1440 40" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M0,20 C120,8 240,32 360,20 C480,8 600,32 720,20 C840,8 960,32 1080,20 C1200,8 1320,32 1440,20 L1440,40 L0,40Z" fill="rgba(10,32,20,0.75)"/>
      <path d="M0,20 C120,8 240,32 360,20 C480,8 600,32 720,20 C840,8 960,32 1080,20 C1200,8 1320,32 1440,20" fill="none" stroke="rgba(200,240,220,0.1)" stroke-width="1"/>
    </svg>
    <!-- Layer 4 -->
    <svg class="wave-svg wl4" viewBox="0 0 1440 48" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M0,24 C100,10 200,38 300,24 C400,10 500,38 600,24 C700,10 800,38 900,24 C1000,10 1100,38 1200,24 C1300,10 1400,38 1440,24 L1440,48 L0,48Z" fill="rgba(8,26,16,0.82)"/>
      <path d="M0,24 C100,10 200,38 300,24 C400,10 500,38 600,24 C700,10 800,38 900,24 C1000,10 1100,38 1200,24 C1300,10 1400,38 1440,24" fill="none" stroke="rgba(220,255,235,0.14)" stroke-width="1.5"/>
    </svg>
    <!-- Layer 5 -->
    <svg class="wave-svg wl5" viewBox="0 0 1440 55" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M0,28 C90,12 180,44 270,28 C360,12 450,44 540,28 C630,12 720,44 810,28 C900,12 990,44 1080,28 C1170,12 1260,44 1350,28 C1400,14 1440,38 1440,28 L1440,55 L0,55Z" fill="rgba(6,22,13,0.88)"/>
      <path d="M0,28 C90,12 180,44 270,28 C360,12 450,44 540,28 C630,12 720,44 810,28 C900,12 990,44 1080,28 C1170,12 1260,44 1350,28 C1400,14 1440,38 1440,28" fill="none" stroke="rgba(240,255,248,0.18)" stroke-width="2"/>
    </svg>
    <!-- Layer 6 — paling dekat -->
    <svg class="wave-svg wl6" viewBox="0 0 1440 65" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M0,32 C72,12 144,52 216,32 C288,12 360,52 432,32 C504,12 576,52 648,32 C720,12 792,52 864,32 C936,12 1008,52 1080,32 C1152,12 1224,52 1296,32 C1368,12 1440,52 1440,32 L1440,65 L0,65Z" fill="rgba(5,18,11,0.92)"/>
      <path d="M0,32 C72,12 144,52 216,32 C288,12 360,52 432,32 C504,12 576,52 648,32 C720,12 792,52 864,32 C936,12 1008,52 1080,32 C1152,12 1224,52 1296,32 C1368,12 1440,52 1440,32" fill="none" stroke="rgba(255,255,255,0.22)" stroke-width="2.5"/>
      <ellipse cx="72" cy="12" rx="18" ry="4" fill="rgba(255,255,255,0.06)"/>
      <ellipse cx="216" cy="52" rx="14" ry="3" fill="rgba(255,255,255,0.07)"/>
      <ellipse cx="360" cy="12" rx="20" ry="4" fill="rgba(255,255,255,0.06)"/>
      <ellipse cx="504" cy="52" rx="16" ry="3" fill="rgba(255,255,255,0.07)"/>
      <ellipse cx="648" cy="12" rx="18" ry="4" fill="rgba(255,255,255,0.06)"/>
      <ellipse cx="792" cy="52" rx="14" ry="3" fill="rgba(255,255,255,0.07)"/>
      <ellipse cx="936" cy="12" rx="20" ry="4" fill="rgba(255,255,255,0.06)"/>
    </svg>
  </div>

  <!-- Buih mengambang -->
  <div class="foam-layer" aria-hidden="true">
    <div class="foam-blob" style="width:80px;height:8px;top:20%;--fd:14s;--fdelay:0s;opacity:0.5;"></div>
    <div class="foam-blob" style="width:50px;height:6px;top:60%;--fd:18s;--fdelay:-5s;opacity:0.4;"></div>
    <div class="foam-blob" style="width:110px;height:10px;top:40%;--fd:22s;--fdelay:-10s;opacity:0.35;"></div>
    <div class="foam-blob" style="width:65px;height:7px;top:75%;--fd:12s;--fdelay:-7s;opacity:0.45;"></div>
    <div class="foam-blob" style="width:90px;height:9px;top:10%;--fd:16s;--fdelay:-3s;opacity:0.38;"></div>
    <div class="foam-blob" style="width:40px;height:5px;top:55%;--fd:20s;--fdelay:-12s;opacity:0.42;"></div>
  </div>

  <!-- SVG armada kapal -->
  <svg class="ship-stage" viewBox="0 0 1000 1000" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <defs>
      <linearGradient id="hullMain" x1="0" y1="0" x2="1" y2="0">
        <stop offset="0%" stop-color="#4a5560"/>
        <stop offset="12%" stop-color="#c8cdd2"/>
        <stop offset="50%" stop-color="#e8eaec"/>
        <stop offset="88%" stop-color="#c8cdd2"/>
        <stop offset="100%" stop-color="#4a5560"/>
      </linearGradient>
      <linearGradient id="hullTug" x1="0" y1="0" x2="1" y2="0">
        <stop offset="0%" stop-color="#0a2e1c"/>
        <stop offset="18%" stop-color="#1e7048"/>
        <stop offset="50%" stop-color="#28905c"/>
        <stop offset="82%" stop-color="#1e7048"/>
        <stop offset="100%" stop-color="#0a2e1c"/>
      </linearGradient>
      <linearGradient id="goldTrim" x1="0" y1="0" x2="1" y2="0">
        <stop offset="0%" stop-color="#7a6228"/>
        <stop offset="50%" stop-color="#e3cd8c"/>
        <stop offset="100%" stop-color="#7a6228"/>
      </linearGradient>
      <linearGradient id="ropeGrad" x1="0" y1="0" x2="1" y2="0">
        <stop offset="0%" stop-color="rgba(180,148,70,0)"/>
        <stop offset="30%" stop-color="rgba(200,168,80,.55)"/>
        <stop offset="70%" stop-color="rgba(200,168,80,.55)"/>
        <stop offset="100%" stop-color="rgba(180,148,70,0)"/>
      </linearGradient>
      <radialGradient id="foamGrad" cx="50%" cy="50%" r="50%">
        <stop offset="0%" stop-color="#ffffff" stop-opacity=".85"/>
        <stop offset="100%" stop-color="#ffffff" stop-opacity="0"/>
      </radialGradient>
      <linearGradient id="wakeGrad" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0%" stop-color="#a0f0d0" stop-opacity=".42"/>
        <stop offset="100%" stop-color="#a0f0d0" stop-opacity="0"/>
      </linearGradient>
      <radialGradient id="glowR" cx="50%" cy="50%" r="50%">
        <stop offset="0%" stop-color="#ff5555" stop-opacity=".9"/>
        <stop offset="100%" stop-color="#ff2020" stop-opacity="0"/>
      </radialGradient>
      <radialGradient id="glowG" cx="50%" cy="50%" r="50%">
        <stop offset="0%" stop-color="#55ff88" stop-opacity=".9"/>
        <stop offset="100%" stop-color="#20cc55" stop-opacity="0"/>
      </radialGradient>
      <radialGradient id="glowW" cx="50%" cy="50%" r="50%">
        <stop offset="0%" stop-color="#fffbe0" stop-opacity=".95"/>
        <stop offset="100%" stop-color="#ffd870" stop-opacity="0"/>
      </radialGradient>
      <linearGradient id="hullBottom" x1="0" y1="0" x2="1" y2="0">
        <stop offset="0%" stop-color="#3a1008"/>
        <stop offset="15%" stop-color="#8a2a1a"/>
        <stop offset="50%" stop-color="#a83520"/>
        <stop offset="85%" stop-color="#8a2a1a"/>
        <stop offset="100%" stop-color="#3a1008"/>
      </linearGradient>
      <linearGradient id="tugBottom" x1="0" y1="0" x2="1" y2="0">
        <stop offset="0%" stop-color="#2a0c04"/>
        <stop offset="20%" stop-color="#7a2212"/>
        <stop offset="50%" stop-color="#962c18"/>
        <stop offset="80%" stop-color="#7a2212"/>
        <stop offset="100%" stop-color="#2a0c04"/>
      </linearGradient>
      <linearGradient id="bridgeGrad" x1="0" y1="0" x2="1" y2="0">
        <stop offset="0%" stop-color="#0c2e1d"/>
        <stop offset="50%" stop-color="#1f5e3f"/>
        <stop offset="100%" stop-color="#0c2e1d"/>
      </linearGradient>
      <radialGradient id="bowFoam" cx="50%" cy="50%" r="50%">
        <stop offset="0%" stop-color="#c8ffe0" stop-opacity=".7"/>
        <stop offset="100%" stop-color="#c8ffe0" stop-opacity="0"/>
      </radialGradient>
    </defs>

    <!-- REFLEKSI LAMPU DI AIR -->
    <ellipse cx="500" cy="745" rx="10" ry="3" fill="rgba(220,185,80,.2)"/>
    <ellipse cx="278" cy="660" rx="6" ry="2" fill="rgba(100,220,150,.15)"/>
    <ellipse cx="722" cy="660" rx="6" ry="2" fill="rgba(255,100,100,.12)"/>
    <ellipse cx="500" cy="748" rx="78" ry="14" fill="#020e07" opacity=".3"/>
    <ellipse cx="278" cy="648" rx="40" ry="8" fill="#020e07" opacity=".22"/>
    <ellipse cx="722" cy="648" rx="40" ry="8" fill="#020e07" opacity=".22"/>

    <!-- Refleksi lampu merah di air -->
    <ellipse cx="436" cy="640" rx="5" ry="18" fill="rgba(255,80,80,0.08)">
      <animate attributeName="rx" values="5;7;5" dur="3s" repeatCount="indefinite"/>
    </ellipse>
    <!-- Refleksi lampu hijau di air -->
    <ellipse cx="564" cy="640" rx="5" ry="18" fill="rgba(80,255,140,0.08)">
      <animate attributeName="rx" values="5;7;5" dur="3.5s" repeatCount="indefinite"/>
    </ellipse>
    <!-- Refleksi lampu putih masthead -->
    <ellipse cx="500" cy="760" rx="8" ry="30" fill="rgba(255,248,190,0.07)">
      <animate attributeName="opacity" values="0.5;1;0.5" dur="4.5s" repeatCount="indefinite"/>
    </ellipse>

    <!-- Riak konsentris dari kapal -->
    <ellipse cx="500" cy="755" rx="60" ry="8" fill="none" stroke="rgba(180,255,220,0.08)" stroke-width="1">
      <animate attributeName="rx" values="60;110;160" dur="4s" repeatCount="indefinite"/>
      <animate attributeName="opacity" values="0.6;0.2;0" dur="4s" repeatCount="indefinite"/>
    </ellipse>
    <ellipse cx="500" cy="755" rx="45" ry="6" fill="none" stroke="rgba(180,255,220,0.06)" stroke-width="1">
      <animate attributeName="rx" values="45;90;135" dur="4s" begin="1.3s" repeatCount="indefinite"/>
      <animate attributeName="opacity" values="0.5;0.2;0" dur="4s" begin="1.3s" repeatCount="indefinite"/>
    </ellipse>
    <ellipse cx="278" cy="665" rx="30" ry="5" fill="none" stroke="rgba(180,255,220,0.07)" stroke-width="1">
      <animate attributeName="rx" values="30;60;90" dur="3.5s" repeatCount="indefinite"/>
      <animate attributeName="opacity" values="0.5;0.2;0" dur="3.5s" repeatCount="indefinite"/>
    </ellipse>
    <ellipse cx="722" cy="665" rx="30" ry="5" fill="none" stroke="rgba(180,255,220,0.07)" stroke-width="1">
      <animate attributeName="rx" values="30;60;90" dur="3.5s" begin="0.8s" repeatCount="indefinite"/>
      <animate attributeName="opacity" values="0.5;0.2;0" dur="3.5s" begin="0.8s" repeatCount="indefinite"/>
    </ellipse>

    <g class="fleet-group">

      <!-- TALI TAMBAT -->
      <path d="M316 560 Q375 540 442 548" stroke="url(#ropeGrad)" stroke-width="2" fill="none" stroke-linecap="round"/>
      <path d="M316 575 Q375 558 442 565" stroke="url(#ropeGrad)" stroke-width="1.2" fill="none" stroke-linecap="round" opacity=".5"/>
      <path d="M684 560 Q625 540 558 548" stroke="url(#ropeGrad)" stroke-width="2" fill="none" stroke-linecap="round"/>
      <path d="M684 575 Q625 558 558 565" stroke="url(#ropeGrad)" stroke-width="1.2" fill="none" stroke-linecap="round" opacity=".5"/>

      <!-- ═══ TUG KIRI — TB. KRESNA ═══ -->
      <g>
        <path class="wake" d="M278 635 Q260 695 268 775 Q278 750 288 775 Q296 695 278 635Z" fill="url(#wakeGrad)"/>
        <!-- Bow wave -->
        <path d="M242 560 Q230 600 235 640 Q250 625 260 640" fill="none" stroke="rgba(200,255,230,0.15)" stroke-width="2" stroke-linecap="round"/>
        <path d="M314 560 Q326 600 321 640 Q306 625 296 640" fill="none" stroke="rgba(200,255,230,0.15)" stroke-width="2" stroke-linecap="round"/>
        <ellipse class="foam" cx="278" cy="635" rx="22" ry="8" fill="url(#foamGrad)" opacity=".6"/>
        <!-- Hull bawah merah -->
        <path d="M248 630 Q242 640 240 650 L240 660 Q248 668 278 670 Q308 668 316 660 L316 650 Q314 640 308 630 Z" fill="url(#tugBottom)" stroke="#1a0805" stroke-width="1.5"/>
        <line x1="242" y1="650" x2="314" y2="650" stroke="rgba(255,255,255,.25)" stroke-width="1"/>
        <!-- Hull hijau utama -->
        <path d="M278 430 C 255 432 246 445 244 460 L 242 620 Q 242 632 248 638 Q 260 642 278 643 Q 296 642 308 638 Q 314 632 314 620 L 312 460 C 310 445 301 432 278 430 Z" fill="url(#hullTug)" stroke="#071a10" stroke-width="2.5"/>
        <line x1="278" y1="440" x2="278" y2="638" stroke="rgba(180,255,210,.2)" stroke-width="1"/>
        <path d="M247 462 L247 620 M309 462 L309 620" stroke="rgba(0,0,0,.4)" stroke-width="2" fill="none"/>
        <!-- Bulwark -->
        <rect x="247" y="460" width="62" height="8" rx="0" fill="#0a2618" stroke="#071510" stroke-width="1"/>
        <!-- Forecastle -->
        <rect x="254" y="432" width="48" height="35" rx="4" fill="#0e2e1c" stroke="#071510" stroke-width="1.5"/>
        <rect x="262" y="438" width="32" height="12" rx="2" fill="#1a4428"/>
        <circle cx="272" cy="444" r="4" fill="#0a2010"/><circle cx="284" cy="444" r="4" fill="#0a2010"/>
        <ellipse cx="266" cy="435" rx="4" ry="3" fill="#333a30"/><ellipse cx="290" cy="435" rx="4" ry="3" fill="#333a30"/>
        <!-- Superstruktur -->
        <rect x="252" y="500" width="52" height="72" rx="5" fill="#d8dde0" stroke="#9aa2a6" stroke-width="1.8"/>
        <rect x="256" y="498" width="44" height="16" rx="3" fill="#c2c8cc"/>
        <rect x="258" y="514" width="40" height="48" rx="3" fill="url(#bridgeGrad)" stroke="#071a10" stroke-width="1.5"/>
        <g fill="rgba(180,220,255,.25)" stroke="rgba(255,255,255,.2)" stroke-width=".8">
          <rect x="262" y="519" width="10" height="8" rx="1.5"/>
          <rect x="274" y="519" width="10" height="8" rx="1.5"/>
          <rect x="286" y="519" width="10" height="8" rx="1.5"/>
        </g>
        <rect x="258" y="530" width="40" height="4" rx="1" fill="#cfae5e" opacity=".8"/>
        <rect x="260" y="548" width="10" height="16" rx="1" fill="#0a2010" opacity=".8"/>
        <rect x="284" y="548" width="10" height="16" rx="1" fill="#0a2010" opacity=".8"/>
        <!-- Cerobong -->
        <rect x="268" y="478" width="20" height="28" rx="4" fill="#0c1e12" stroke="#142210" stroke-width="1"/>
        <ellipse cx="278" cy="478" rx="10" ry="5" fill="#0e2618"/>
        <rect x="268" y="490" width="20" height="4" rx="1" fill="#cfae5e" opacity=".7"/>
        <!-- Asap cerobong tug kiri -->
        <ellipse cx="278" cy="470" rx="8" ry="5" fill="rgba(40,40,35,0.35)">
          <animate attributeName="cy" values="470;450;430" dur="4s" repeatCount="indefinite"/>
          <animate attributeName="rx" values="8;14;20" dur="4s" repeatCount="indefinite"/>
          <animate attributeName="opacity" values="0.35;0.2;0" dur="4s" repeatCount="indefinite"/>
        </ellipse>
        <ellipse cx="278" cy="470" rx="5" ry="3" fill="rgba(40,40,35,0.3)">
          <animate attributeName="cy" values="470;445;420" dur="4s" begin="1.5s" repeatCount="indefinite"/>
          <animate attributeName="rx" values="5;12;18" dur="4s" begin="1.5s" repeatCount="indefinite"/>
          <animate attributeName="opacity" values="0.3;0.15;0" dur="4s" begin="1.5s" repeatCount="indefinite"/>
        </ellipse>
        <!-- Winch belakang -->
        <rect x="256" y="588" width="44" height="14" rx="2" fill="#1a3a22"/>
        <circle cx="270" cy="595" r="5" fill="#102818"/><circle cx="286" cy="595" r="5" fill="#102818"/>
        <!-- Lampu merah port -->
        <ellipse class="ship-light" cx="242" cy="530" rx="5" ry="5" fill="url(#glowR)"/>
        <circle cx="242" cy="530" r="2.5" fill="#ff4444"/>
        <!-- Mast -->
        <line x1="278" y1="430" x2="278" y2="382" stroke="#8a8878" stroke-width="1.8"/>
        <line x1="278" y1="400" x2="258" y2="410" stroke="#8a8878" stroke-width="1"/>
        <line x1="278" y1="400" x2="298" y2="410" stroke="#8a8878" stroke-width="1"/>
        <ellipse class="ship-light slow" cx="278" cy="382" rx="5" ry="5" fill="url(#glowW)"/>
        <circle cx="278" cy="382" r="2.2" fill="#fffbe0"/>
        <!-- Bollard -->
        <g fill="#1a1a1a" stroke="#000" stroke-width=".8">
          <ellipse cx="252" cy="580" rx="4.5" ry="3.5"/><ellipse cx="304" cy="580" rx="4.5" ry="3.5"/>
          <ellipse cx="252" cy="610" rx="4.5" ry="3.5"/><ellipse cx="304" cy="610" rx="4.5" ry="3.5"/>
        </g>
        <!-- Fender karet -->
        <g fill="#1a1a12" opacity=".8">
          <ellipse cx="242" cy="555" rx="4" ry="8"/><ellipse cx="242" cy="580" rx="4" ry="8"/>
          <ellipse cx="314" cy="555" rx="4" ry="8"/><ellipse cx="314" cy="580" rx="4" ry="8"/>
        </g>
        <text x="278" y="656" text-anchor="middle" font-family="'Cormorant Garamond',serif" font-size="10" font-weight="700" fill="#d4c28a" letter-spacing=".6">TB. KRESNA</text>
      </g>

      <!-- ═══ TUG KANAN — TB. TPS BETA ═══ -->
      <g>
        <path class="wake" d="M722 635 Q704 695 712 775 Q722 750 732 775 Q740 695 722 635Z" fill="url(#wakeGrad)"/>
        <!-- Bow wave -->
        <path d="M686 560 Q674 600 679 640 Q694 625 704 640" fill="none" stroke="rgba(200,255,230,0.15)" stroke-width="2" stroke-linecap="round"/>
        <path d="M758 560 Q770 600 765 640 Q750 625 740 640" fill="none" stroke="rgba(200,255,230,0.15)" stroke-width="2" stroke-linecap="round"/>
        <ellipse class="foam" cx="722" cy="635" rx="22" ry="8" fill="url(#foamGrad)" opacity=".6"/>
        <!-- Hull bawah merah -->
        <path d="M692 630 Q686 640 684 650 L684 660 Q692 668 722 670 Q752 668 760 660 L760 650 Q758 640 752 630 Z" fill="url(#tugBottom)" stroke="#1a0805" stroke-width="1.5"/>
        <line x1="686" y1="650" x2="758" y2="650" stroke="rgba(255,255,255,.25)" stroke-width="1"/>
        <!-- Hull hijau utama -->
        <path d="M722 430 C 699 432 690 445 688 460 L 686 620 Q 686 632 692 638 Q 704 642 722 643 Q 740 642 752 638 Q 758 632 758 620 L 756 460 C 754 445 745 432 722 430 Z" fill="url(#hullTug)" stroke="#071a10" stroke-width="2.5"/>
        <line x1="722" y1="440" x2="722" y2="638" stroke="rgba(180,255,210,.2)" stroke-width="1"/>
        <path d="M691 462 L691 620 M753 462 L753 620" stroke="rgba(0,0,0,.4)" stroke-width="2" fill="none"/>
        <rect x="691" y="460" width="62" height="8" rx="0" fill="#0a2618" stroke="#071510" stroke-width="1"/>
        <!-- Forecastle -->
        <rect x="698" y="432" width="48" height="35" rx="4" fill="#0e2e1c" stroke="#071510" stroke-width="1.5"/>
        <rect x="706" y="438" width="32" height="12" rx="2" fill="#1a4428"/>
        <circle cx="716" cy="444" r="4" fill="#0a2010"/><circle cx="728" cy="444" r="4" fill="#0a2010"/>
        <ellipse cx="710" cy="435" rx="4" ry="3" fill="#333a30"/><ellipse cx="734" cy="435" rx="4" ry="3" fill="#333a30"/>
        <!-- Superstruktur -->
        <rect x="696" y="500" width="52" height="72" rx="5" fill="#d8dde0" stroke="#9aa2a6" stroke-width="1.8"/>
        <rect x="700" y="498" width="44" height="16" rx="3" fill="#c2c8cc"/>
        <rect x="702" y="514" width="40" height="48" rx="3" fill="url(#bridgeGrad)" stroke="#071a10" stroke-width="1.5"/>
        <g fill="rgba(180,220,255,.25)" stroke="rgba(255,255,255,.2)" stroke-width=".8">
          <rect x="706" y="519" width="10" height="8" rx="1.5"/>
          <rect x="718" y="519" width="10" height="8" rx="1.5"/>
          <rect x="730" y="519" width="10" height="8" rx="1.5"/>
        </g>
        <rect x="702" y="530" width="40" height="4" rx="1" fill="#cfae5e" opacity=".8"/>
        <rect x="704" y="548" width="10" height="16" rx="1" fill="#0a2010" opacity=".8"/>
        <rect x="728" y="548" width="10" height="16" rx="1" fill="#0a2010" opacity=".8"/>
        <!-- Cerobong -->
        <rect x="712" y="478" width="20" height="28" rx="4" fill="#0c1e12" stroke="#142210" stroke-width="1"/>
        <ellipse cx="722" cy="478" rx="10" ry="5" fill="#0e2618"/>
        <rect x="712" y="490" width="20" height="4" rx="1" fill="#cfae5e" opacity=".7"/>
        <!-- Asap cerobong tug kanan -->
        <ellipse cx="722" cy="470" rx="8" ry="5" fill="rgba(40,40,35,0.35)">
          <animate attributeName="cy" values="470;450;430" dur="4.5s" repeatCount="indefinite"/>
          <animate attributeName="rx" values="8;14;20" dur="4.5s" repeatCount="indefinite"/>
          <animate attributeName="opacity" values="0.35;0.2;0" dur="4.5s" repeatCount="indefinite"/>
        </ellipse>
        <ellipse cx="722" cy="470" rx="5" ry="3" fill="rgba(40,40,35,0.3)">
          <animate attributeName="cy" values="470;445;420" dur="4.5s" begin="2s" repeatCount="indefinite"/>
          <animate attributeName="rx" values="5;12;18" dur="4.5s" begin="2s" repeatCount="indefinite"/>
          <animate attributeName="opacity" values="0.3;0.15;0" dur="4.5s" begin="2s" repeatCount="indefinite"/>
        </ellipse>
        <!-- Winch -->
        <rect x="700" y="588" width="44" height="14" rx="2" fill="#1a3a22"/>
        <circle cx="714" cy="595" r="5" fill="#102818"/><circle cx="730" cy="595" r="5" fill="#102818"/>
        <!-- Lampu hijau starboard -->
        <ellipse class="ship-light green" cx="758" cy="530" rx="5" ry="5" fill="url(#glowG)"/>
        <circle cx="758" cy="530" r="2.5" fill="#44ff88"/>
        <!-- Mast -->
        <line x1="722" y1="430" x2="722" y2="382" stroke="#8a8878" stroke-width="1.8"/>
        <line x1="722" y1="400" x2="702" y2="410" stroke="#8a8878" stroke-width="1"/>
        <line x1="722" y1="400" x2="742" y2="410" stroke="#8a8878" stroke-width="1"/>
        <ellipse class="ship-light slow" cx="722" cy="382" rx="5" ry="5" fill="url(#glowW)"/>
        <circle cx="722" cy="382" r="2.2" fill="#fffbe0"/>
        <!-- Bollard -->
        <g fill="#1a1a1a" stroke="#000" stroke-width=".8">
          <ellipse cx="696" cy="580" rx="4.5" ry="3.5"/><ellipse cx="748" cy="580" rx="4.5" ry="3.5"/>
          <ellipse cx="696" cy="610" rx="4.5" ry="3.5"/><ellipse cx="748" cy="610" rx="4.5" ry="3.5"/>
        </g>
        <!-- Fender karet -->
        <g fill="#1a1a12" opacity=".8">
          <ellipse cx="686" cy="555" rx="4" ry="8"/><ellipse cx="686" cy="580" rx="4" ry="8"/>
          <ellipse cx="758" cy="555" rx="4" ry="8"/><ellipse cx="758" cy="580" rx="4" ry="8"/>
        </g>
        <text x="722" y="656" text-anchor="middle" font-family="'Cormorant Garamond',serif" font-size="10" font-weight="700" fill="#d4c28a" letter-spacing=".6">TB. TPS BETA</text>
      </g>

      <!-- ═══ CONTAINER SHIP UTAMA — MV. JENIUS PRIMA ═══ -->
      <g>
        <!-- Wake panjang -->
        <path class="wake" d="M500 748 Q472 840 482 950 Q500 910 518 950 Q528 840 500 748Z" fill="url(#wakeGrad)"/>
        <!-- Bow waves -->
        <path d="M436 360 Q408 420 415 500 Q440 470 455 500" fill="none" stroke="rgba(200,255,230,0.12)" stroke-width="2.5" stroke-linecap="round"/>
        <path d="M564 360 Q592 420 585 500 Q560 470 545 500" fill="none" stroke="rgba(200,255,230,0.12)" stroke-width="2.5" stroke-linecap="round"/>
        <!-- Bow wave scrolling -->
        <path class="bow-wave" d="M430 380 Q412 430 418 490 Q445 460 460 490 Q470 460 480 490" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1.5" stroke-linecap="round"/>
        <path class="bow-wave" d="M570 380 Q588 430 582 490 Q555 460 540 490 Q530 460 520 490" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1.5" stroke-linecap="round" style="animation-delay:-1.5s"/>
        <ellipse class="foam" cx="500" cy="748" rx="36" ry="11" fill="url(#foamGrad)" opacity=".65"/>
        <ellipse class="foam" cx="437" cy="295" rx="12" ry="7" fill="url(#foamGrad)" opacity=".5"/>
        <ellipse class="foam" cx="563" cy="295" rx="12" ry="7" fill="url(#foamGrad)" opacity=".5"/>
        <!-- Buih tambahan di bow -->
        <ellipse cx="428" cy="320" rx="8" ry="4" fill="url(#bowFoam)" opacity=".4">
          <animate attributeName="opacity" values="0.4;0.7;0.4" dur="3s" repeatCount="indefinite"/>
        </ellipse>
        <ellipse cx="572" cy="320" rx="8" ry="4" fill="url(#bowFoam)" opacity=".4">
          <animate attributeName="opacity" values="0.4;0.7;0.4" dur="3s" begin="1.5s" repeatCount="indefinite"/>
        </ellipse>
        <!-- Hull bawah merah -->
        <path d="M448 720 Q434 735 434 748 L434 756 Q460 768 500 770 Q540 768 566 756 L566 748 Q566 735 552 720 Z" fill="url(#hullBottom)" stroke="#1a0805" stroke-width="1.5"/>
        <line x1="436" y1="748" x2="564" y2="748" stroke="rgba(255,255,255,.3)" stroke-width="1.2"/>
        <!-- Hull utama -->
        <path d="M500 258 C 492 260 475 270 466 292 L 438 370 L 436 700 Q 436 720 448 728 Q 466 736 500 738 Q 534 736 552 728 Q 564 720 564 700 L 562 370 L 534 292 C 525 270 508 260 500 258 Z" fill="url(#hullMain)" stroke="#5a6368" stroke-width="3"/>
        <line x1="500" y1="270" x2="500" y2="735" stroke="rgba(255,255,255,.3)" stroke-width="1.5"/>
        <path d="M466 292 L438 700" stroke="rgba(180,195,210,.35)" stroke-width="1" fill="none"/>
        <path d="M534 292 L562 700" stroke="rgba(180,195,210,.35)" stroke-width="1" fill="none"/>
        <!-- Strip gold waterline -->
        <path d="M500 260 C510 262 524 272 532 292 L558 700 Q560 716 554 724" fill="none" stroke="url(#goldTrim)" stroke-width="2" opacity=".65"/>
        <path d="M500 260 C490 262 476 272 468 292 L442 700 Q440 716 446 724" fill="none" stroke="url(#goldTrim)" stroke-width="2" opacity=".45"/>
        <!-- Bulwark -->
        <rect x="438" y="360" width="124" height="10" rx="0" fill="#8a9298" stroke="#5a6368" stroke-width="1"/>
        <line x1="439" y1="365" x2="561" y2="365" stroke="rgba(200,210,220,.45)" stroke-width=".8"/>
        <!-- Haluan detail -->
        <ellipse cx="472" cy="310" rx="7" ry="5" fill="#2a3238"/><ellipse cx="528" cy="310" rx="7" ry="5" fill="#2a3238"/>
        <line x1="472" y1="310" x2="472" y2="340" stroke="#1a2228" stroke-width="3"/>
        <line x1="528" y1="310" x2="528" y2="340" stroke="#1a2228" stroke-width="3"/>
        <rect x="468" y="340" width="64" height="16" rx="3" fill="#2a3840" stroke="#1a2830" stroke-width="1.5"/>
        <ellipse cx="482" cy="348" rx="6" ry="5" fill="#1a2830"/>
        <ellipse cx="500" cy="348" rx="6" ry="5" fill="#1a2830"/>
        <ellipse cx="518" cy="348" rx="6" ry="5" fill="#1a2830"/>
        <g fill="#1a1a1a"><ellipse cx="450" cy="375" rx="5" ry="4"/><ellipse cx="550" cy="375" rx="5" ry="4"/></g>
        <rect x="452" y="358" width="96" height="8" rx="1" fill="#b0b8bc" opacity=".6"/>
        <!-- Peti kemas / kontainer -->
        <g stroke="#0a2618" stroke-width="1.5">
          <rect x="452" y="376" width="30" height="35" fill="#1e6e40" rx="1"/>
          <rect x="484" y="376" width="32" height="35" fill="#e0e4e0" rx="1"/>
          <rect x="518" y="376" width="30" height="35" fill="#1e6e40" rx="1"/>
          <rect x="452" y="413" width="30" height="35" fill="#c0350f" rx="1"/>
          <rect x="484" y="413" width="32" height="35" fill="#1e6e40" rx="1"/>
          <rect x="518" y="413" width="30" height="35" fill="#c0350f" rx="1"/>
          <rect x="452" y="450" width="30" height="35" fill="#e0e4e0" rx="1"/>
          <rect x="484" y="450" width="32" height="35" fill="#c0350f" rx="1"/>
          <rect x="518" y="450" width="30" height="35" fill="#1e6e40" rx="1"/>
          <rect x="452" y="487" width="30" height="35" fill="#1e6e40" rx="1"/>
          <rect x="484" y="487" width="32" height="35" fill="#e0e4e0" rx="1"/>
          <rect x="518" y="487" width="30" height="35" fill="#c0350f" rx="1"/>
          <rect x="452" y="524" width="30" height="35" fill="#c0350f" rx="1"/>
          <rect x="484" y="524" width="32" height="35" fill="#1e6e40" rx="1"/>
          <rect x="518" y="524" width="30" height="35" fill="#e0e4e0" rx="1"/>
        </g>
        <!-- Shadow bawah tiap row -->
        <rect x="452" y="409" width="96" height="3" fill="rgba(0,0,0,.25)"/>
        <rect x="452" y="446" width="96" height="3" fill="rgba(0,0,0,.25)"/>
        <rect x="452" y="483" width="96" height="3" fill="rgba(0,0,0,.25)"/>
        <rect x="452" y="520" width="96" height="3" fill="rgba(0,0,0,.25)"/>
        <rect x="452" y="557" width="96" height="3" fill="rgba(0,0,0,.25)"/>
        <!-- Grid vertikal -->
        <g stroke="rgba(255,255,255,.1)" stroke-width=".8">
          <line x1="483" y1="376" x2="483" y2="558"/>
          <line x1="517" y1="376" x2="517" y2="558"/>
        </g>
        <!-- Crane arm -->
        <line x1="454" y1="375" x2="430" y2="350" stroke="#8a9298" stroke-width="2"/>
        <line x1="546" y1="375" x2="570" y2="350" stroke="#8a9298" stroke-width="2"/>
        <circle cx="430" cy="350" r="5" fill="#6a7278"/>
        <circle cx="570" cy="350" r="5" fill="#6a7278"/>
        <!-- Platform dek -->
        <rect x="436" y="560" width="128" height="16" rx="2" fill="#9aa2a6" stroke="#7a8288" stroke-width="1"/>
        <line x1="436" y1="568" x2="564" y2="568" stroke="rgba(255,255,255,.35)" stroke-width=".8"/>
        <g fill="#4a5560">
          <rect x="442" y="562" width="8" height="10" rx="1"/>
          <rect x="550" y="562" width="8" height="10" rx="1"/>
        </g>
        <!-- Superstruktur / anjungan -->
        <rect x="446" y="576" width="108" height="100" rx="4" fill="#d0d6da" stroke="#9aa2a6" stroke-width="2"/>
        <rect x="452" y="574" width="96" height="18" rx="3" fill="#bdc4c8"/>
        <rect x="454" y="592" width="92" height="54" rx="4" fill="url(#bridgeGrad)" stroke="#071a10" stroke-width="2"/>
        <!-- Jendela panoramik -->
        <g fill="rgba(180,225,255,.28)" stroke="rgba(255,255,255,.22)" stroke-width=".8">
          <rect x="460" y="598" width="14" height="10" rx="2"/>
          <rect x="476" y="598" width="14" height="10" rx="2"/>
          <rect x="492" y="598" width="16" height="10" rx="2"/>
          <rect x="510" y="598" width="14" height="10" rx="2"/>
          <rect x="526" y="598" width="14" height="10" rx="2"/>
        </g>
        <g fill="rgba(180,225,255,.18)" stroke="rgba(255,255,255,.15)" stroke-width=".8">
          <rect x="462" y="614" width="12" height="8" rx="1.5"/>
          <rect x="480" y="614" width="12" height="8" rx="1.5"/>
          <rect x="506" y="614" width="12" height="8" rx="1.5"/>
          <rect x="524" y="614" width="12" height="8" rx="1.5"/>
        </g>
        <rect x="454" y="610" width="92" height="5" rx="1" fill="#cfae5e" opacity=".78"/>
        <rect x="464" y="632" width="14" height="18" rx="1.5" fill="#071a10" opacity=".85"/>
        <rect x="522" y="632" width="14" height="18" rx="1.5" fill="#071a10" opacity=".85"/>
        <!-- Dek B chart room -->
        <rect x="462" y="648" width="76" height="26" rx="3" fill="#c0c8cc" stroke="#8a9298" stroke-width="1.5"/>
        <g fill="rgba(180,225,255,.2)" stroke="rgba(255,255,255,.18)" stroke-width=".8">
          <rect x="468" y="654" width="12" height="8" rx="1.5"/>
          <rect x="484" y="654" width="12" height="8" rx="1.5"/>
          <rect x="504" y="654" width="12" height="8" rx="1.5"/>
          <rect x="520" y="654" width="12" height="8" rx="1.5"/>
        </g>
        <!-- Monkey island -->
        <rect x="470" y="672" width="60" height="14" rx="3" fill="#b0b8bc" stroke="#7a8288" stroke-width="1"/>
        <circle cx="500" cy="679" r="5" fill="#8a9298"/>
        <circle cx="500" cy="679" r="3" fill="#5a6268"/>
        <line x1="474" y1="672" x2="474" y2="655" stroke="#8a8878" stroke-width="1.2"/>
        <line x1="526" y1="672" x2="526" y2="655" stroke="#8a8878" stroke-width="1.2"/>
        <!-- Cerobong utama -->
        <rect x="481" y="638" width="38" height="36" rx="5" fill="#0c1e12" stroke="#0e2010" stroke-width="1.5"/>
        <ellipse cx="500" cy="638" rx="19" ry="8" fill="#0e2618"/>
        <rect x="481" y="646" width="38" height="12" rx="2" fill="#cfae5e" opacity=".75"/>

        <!-- Asap cerobong utama -->
        <ellipse cx="500" cy="630" rx="14" ry="9" fill="rgba(38,38,33,0.45)">
          <animate attributeName="cy" values="630;600;570;540" dur="6s" repeatCount="indefinite"/>
          <animate attributeName="rx" values="14;22;30;38" dur="6s" repeatCount="indefinite"/>
          <animate attributeName="opacity" values="0.45;0.3;0.15;0" dur="6s" repeatCount="indefinite"/>
        </ellipse>
        <ellipse cx="498" cy="630" rx="10" ry="7" fill="rgba(38,38,33,0.4)">
          <animate attributeName="cy" values="630;597;564;531" dur="6s" begin="2s" repeatCount="indefinite"/>
          <animate attributeName="rx" values="10;18;26;34" dur="6s" begin="2s" repeatCount="indefinite"/>
          <animate attributeName="opacity" values="0.4;0.25;0.1;0" dur="6s" begin="2s" repeatCount="indefinite"/>
        </ellipse>
        <ellipse cx="502" cy="630" rx="8" ry="6" fill="rgba(38,38,33,0.35)">
          <animate attributeName="cy" values="630;594;558;522" dur="6s" begin="4s" repeatCount="indefinite"/>
          <animate attributeName="rx" values="8;16;24;30" dur="6s" begin="4s" repeatCount="indefinite"/>
          <animate attributeName="opacity" values="0.35;0.2;0.08;0" dur="6s" begin="4s" repeatCount="indefinite"/>
        </ellipse>
        <!-- Mast utama -->
        <line x1="500" y1="258" x2="500" y2="185" stroke="#8a8878" stroke-width="2.5"/>
        <line x1="500" y1="215" x2="465" y2="228" stroke="#8a8878" stroke-width="1.2"/>
        <line x1="500" y1="215" x2="535" y2="228" stroke="#8a8878" stroke-width="1.2"/>
        <line x1="500" y1="200" x2="446" y2="320" stroke="#8a8878" stroke-width=".8" opacity=".5"/>
        <line x1="500" y1="200" x2="554" y2="320" stroke="#8a8878" stroke-width=".8" opacity=".5"/>
        <ellipse class="ship-light slow" cx="500" cy="185" rx="8" ry="8" fill="url(#glowW)" opacity=".75"/>
        <circle cx="500" cy="185" r="3.5" fill="#fffbe0"/>
        <circle cx="465" cy="228" r="2.5" fill="#ffaa00" opacity=".8"/>
        <circle cx="535" cy="228" r="2.5" fill="#ffaa00" opacity=".8"/>
        <!-- Mast buritan -->
        <line x1="500" y1="672" x2="500" y2="625" stroke="#8a8878" stroke-width="2"/>
        <path d="M500 625 L516 628 L500 631 Z" fill="#e3cd8c" opacity=".7"/>
        <!-- Lampu navigasi -->
        <ellipse class="ship-light" cx="436" cy="610" rx="7" ry="7" fill="url(#glowR)" opacity=".85"/>
        <circle cx="436" cy="610" r="3" fill="#ff4444"/>
        <ellipse class="ship-light green" cx="564" cy="610" rx="7" ry="7" fill="url(#glowG)" opacity=".85"/>
        <circle cx="564" cy="610" r="3" fill="#44ff88"/>
        <ellipse class="ship-light slow" cx="500" cy="735" rx="6" ry="6" fill="url(#glowW)" opacity=".6"/>
        <circle cx="500" cy="735" r="2.5" fill="#fffbe0"/>
        <!-- Stern -->
        <rect x="460" y="700" width="80" height="20" rx="2" fill="#9aa2a6" stroke="#7a8288" stroke-width="1.5"/>
        <rect x="494" y="720" width="12" height="18" rx="1" fill="#2a3238"/>
        <ellipse cx="484" cy="730" rx="8" ry="6" fill="#3a4248" opacity=".8"/>
        <ellipse cx="516" cy="730" rx="8" ry="6" fill="#3a4248" opacity=".8"/>
        <g fill="#4a5560">
          <ellipse cx="448" cy="718" rx="5" ry="4"/>
          <ellipse cx="552" cy="718" rx="5" ry="4"/>
        </g>
        <!-- Nama kapal -->
      </g>

    </g><!-- end fleet-group -->
  </svg>
</div>

<!-- Brand lockup -->
<div class="brand-lockup">
  <div class="brand-copy">
    <h2 class="brand-word" style="height: 65px">
      <span class="sr-only">JENIUS</span>
      <span class="letters" aria-hidden="true">
        <span style="--d:0">J</span><span style="--d:1">E</span><span style="--d:2">N</span><span style="--d:3">I</span><span style="--d:4">U</span><span style="--d:5">S</span>
      </span>
      <span class="shine" aria-hidden="true"></span>
    </h2>
    <p class="brand-tagline">Jejaring Neraca Informasi<br>untuk Sistem Keuangan</p>
  </div>
</div>

<!-- Login Card -->
<div class="login-wrapper">
  <div class="login-card">
    <div class="login-header">
      <h1>WELCOME</h1>
      <p>
        Log in to proceed to your system</p>
    </div>
    <div class="divider-gold"></div>

    <form method="POST" action="{{ route('login') }}" id="loginForm">
      @csrf

      <div class="form-group">
        <label for="employee_id_number">NRK</label>
        <input id="employee_id_number" type="text"
          class="form-control @error('employee_id_number') is-invalid @enderror"
          name="employee_id_number"
          value="{{ old('employee_id_number') }}"
          required autofocus>
        @error('employee_id_number')
          <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
        @enderror
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input id="password" type="password"
          class="form-control @error('password') is-invalid @enderror"
          name="password" required
          autocomplete="current-password">
        @error('password')
          <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
        @enderror
      </div>

      <button type="submit" class="btn-login" id="loginBtn">
        <span class="btn-text">Login</span>
        <div class="spinner"></div>
      </button>
    </form>

    <div class="card-footer-row">
      <span class="clock-display" id="clock"></span>
      <span class="card-footer">JENIUS &copy; <span id="year"></span></span>
    </div>
  </div>
</div>

<script>
(function(){
  var clockEl=document.getElementById('clock'),yearEl=document.getElementById('year');
  yearEl.textContent=new Date().getFullYear();
  function tick(){
    var n=new Date();
    clockEl.textContent=String(n.getHours()).padStart(2,'0')+':'+String(n.getMinutes()).padStart(2,'0')+':'+String(n.getSeconds()).padStart(2,'0');
  }
  tick();setInterval(tick,1000);

  var form=document.getElementById('loginForm'),btn=document.getElementById('loginBtn');
  form.addEventListener('submit',function(){btn.classList.add('loading');btn.disabled=true;});

  document.addEventListener('visibilitychange',function(){
    document.documentElement.classList.toggle('tab-hidden',document.hidden);
  });

  /* Glitter — kilatan cahaya di permukaan air */
  var canvas=document.getElementById('glitterCanvas');
  var positions=[
    {l:'8%',t:'5%'},{l:'15%',t:'18%'},{l:'22%',t:'8%'},{l:'28%',t:'28%'},
    {l:'30%',t:'12%'},{l:'35%',t:'35%'},{l:'42%',t:'22%'},{l:'48%',t:'10%'},
    {l:'55%',t:'30%'},{l:'60%',t:'15%'},{l:'65%',t:'40%'},{l:'70%',t:'8%'},
    {l:'75%',t:'25%'},{l:'80%',t:'18%'},{l:'85%',t:'38%'},{l:'90%',t:'12%'},
    {l:'12%',t:'55%'},{l:'25%',t:'65%'},{l:'38%',t:'48%'},{l:'52%',t:'70%'},
    {l:'63%',t:'52%'},{l:'78%',t:'62%'},{l:'88%',t:'45%'}
  ];
  positions.forEach(function(p){
    var el=document.createElement('div');
    el.className='glitter';
    var size=(Math.random()*2+1).toFixed(1);
    var dur=(Math.random()*4+2).toFixed(1)+'s';
    var delay='-'+(Math.random()*8).toFixed(1)+'s';
    var peak=(Math.random()*0.5+0.3).toFixed(2);
    el.style.cssText='left:'+p.l+';top:'+p.t+';width:'+size+'px;height:'+size+'px;--dur:'+dur+';--delay:'+delay+';--peak:'+peak+';';
    canvas.appendChild(el);
  });
})();
</script>
</body>
</html>
