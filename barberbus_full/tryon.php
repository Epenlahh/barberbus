<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>BarberBus – AR Hair Try-On</title>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --gold: #c9a84c; --gold-dark: #8a5c0a;
    --black: #0a0a0a; --dark: #111; --dark2: #1a1a1a; --dark3: #252525;
    --gray: #555; --light-gray: #888; --white: #f5f0e8;
    --font-display: 'Bebas Neue', sans-serif;
    --font-serif: 'Playfair Display', serif;
    --font-body: 'DM Sans', sans-serif;
    --r: 0.3s cubic-bezier(0.4,0,0.2,1);
  }
  html, body { height: 100%; overflow: hidden; background: #000; font-family: var(--font-body); color: var(--white); }

  /* ─── NAVBAR ─── */
  .navbar {
    position: fixed; top: 0; left: 0; right: 0; z-index: 200;
    display: flex; align-items: center; justify-content: space-between;
    padding: 0.9rem 1.8rem;
    background: linear-gradient(to bottom, rgba(0,0,0,0.85), transparent);
  }
  .logo { font-family: var(--font-display); font-size: 1.4rem; letter-spacing: 0.05em; }
  .logo span { color: var(--gold); }
  .nav-badge {
    display: inline-flex; align-items: center; gap: 0.4rem;
    padding: 0.3rem 0.9rem; border-radius: 20px;
    background: rgba(201,168,76,0.18); border: 1px solid rgba(201,168,76,0.35);
    font-size: 0.72rem; letter-spacing: 0.1em; text-transform: uppercase; color: var(--gold);
  }
  .nav-right { display: flex; gap: 0.6rem; align-items: center; }
  .nav-btn {
    padding: 0.42rem 1rem; border-radius: 6px; font-size: 0.78rem; font-weight: 600;
    cursor: pointer; border: none; transition: var(--r);
    letter-spacing: 0.05em; text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem;
  }
  .nav-btn.outline { background: transparent; border: 1px solid rgba(255,255,255,0.2); color: var(--white); }
  .nav-btn.outline:hover { border-color: var(--gold); color: var(--gold); }
  .nav-btn.gold { background: var(--gold); color: #000; }
  .nav-btn.gold:hover { background: #d4b05e; }

  /* ─── CAMERA STAGE ─── */
  .stage {
    position: fixed; inset: 0;
    display: flex; align-items: center; justify-content: center;
    background: #000;
  }
  #video {
    width: 100%; height: 100%; object-fit: cover;
    transform: scaleX(-1); /* mirror like TikTok */
    display: block;
  }
  #overlay-canvas {
    position: absolute; inset: 0;
    width: 100%; height: 100%;
    pointer-events: none;
    transform: scaleX(-1);
  }
  #debug-canvas { display: none; }

  /* ─── PERMISSION SCREEN ─── */
  .permission-screen {
    position: fixed; inset: 0; z-index: 300;
    background: var(--dark); display: flex; align-items: center; justify-content: center;
    flex-direction: column; gap: 1.5rem; text-align: center; padding: 2rem;
  }
  .permission-screen .icon {
    width: 90px; height: 90px; border-radius: 50%;
    background: rgba(201,168,76,0.12); border: 1px solid rgba(201,168,76,0.3);
    display: flex; align-items: center; justify-content: center;
    font-size: 2.5rem; color: var(--gold);
    animation: pulse 2s ease infinite;
  }
  @keyframes pulse { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.06);opacity:0.8} }
  .permission-screen h2 { font-family: var(--font-serif); font-size: 1.8rem; }
  .permission-screen p { color: var(--light-gray); font-size: 0.9rem; max-width: 300px; }
  .btn-start {
    padding: 0.9rem 2.4rem; background: var(--gold); color: #000;
    font-weight: 700; font-size: 0.9rem; letter-spacing: 0.1em;
    text-transform: uppercase; border: none; border-radius: 6px; cursor: pointer;
    transition: var(--r);
  }
  .btn-start:hover { background: #d4b05e; transform: translateY(-2px); }

  /* ─── LOADING OVERLAY ─── */
  .loading-overlay {
    position: fixed; inset: 0; z-index: 250; background: rgba(0,0,0,0.85);
    display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 1rem;
  }
  .loading-overlay.hidden { display: none; }
  .spinner {
    width: 48px; height: 48px; border: 3px solid rgba(201,168,76,0.2);
    border-top-color: var(--gold); border-radius: 50%;
    animation: spin 0.8s linear infinite;
  }
  @keyframes spin { to { transform: rotate(360deg); } }
  .loading-overlay p { color: var(--light-gray); font-size: 0.88rem; }
  .loading-overlay strong { color: var(--gold); font-size: 1rem; }

  /* ─── BOTTOM PANEL (TikTok style) ─── */
  .bottom-panel {
    position: fixed; bottom: 0; left: 0; right: 0; z-index: 150;
    background: linear-gradient(to top, rgba(0,0,0,0.95) 60%, transparent);
    padding: 1.5rem 1rem 1.8rem;
  }

  /* Style tabs */
  .style-label {
    font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.15em;
    color: var(--light-gray); margin-bottom: 0.7rem; padding: 0 0.5rem;
  }
  .style-scroll {
    display: flex; gap: 0.7rem; overflow-x: auto; padding-bottom: 0.5rem;
    scrollbar-width: none;
  }
  .style-scroll::-webkit-scrollbar { display: none; }

  .style-chip {
    flex-shrink: 0; display: flex; flex-direction: column; align-items: center; gap: 0.4rem;
    cursor: pointer; transition: var(--r);
  }
  .style-chip:hover { transform: translateY(-3px); }
  .chip-img {
    width: 64px; height: 64px; border-radius: 14px;
    background: var(--dark3); border: 2px solid transparent;
    transition: all 0.25s; overflow: hidden;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.6rem; position: relative;
  }
  .style-chip.active .chip-img {
    border-color: var(--gold);
    box-shadow: 0 0 16px rgba(201,168,76,0.5);
  }
  .chip-label {
    font-size: 0.68rem; color: var(--light-gray); text-align: center;
    max-width: 64px; line-height: 1.2; transition: color 0.2s;
  }
  .style-chip.active .chip-label { color: var(--gold); }
  .chip-trend {
    position: absolute; top: 3px; right: 3px;
    background: var(--gold); color: #000;
    font-size: 0.5rem; font-weight: 700; padding: 1px 4px; border-radius: 4px;
    text-transform: uppercase;
  }

  /* Controls bar */
  .controls-bar {
    display: flex; align-items: center; justify-content: space-between;
    margin-top: 1rem; padding: 0 0.3rem;
  }
  .ctrl-group { display: flex; gap: 0.7rem; align-items: center; }
  .ctrl-btn {
    width: 46px; height: 46px; border-radius: 50%;
    background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.12);
    color: var(--white); font-size: 1rem; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: var(--r); backdrop-filter: blur(8px);
  }
  .ctrl-btn:hover { background: rgba(201,168,76,0.2); border-color: var(--gold); color: var(--gold); }
  .ctrl-btn.active { background: var(--gold); color: #000; border-color: var(--gold); }
  .capture-btn {
    width: 68px; height: 68px; border-radius: 50%;
    background: transparent; border: 3px solid var(--white);
    cursor: pointer; position: relative; transition: var(--r);
    display: flex; align-items: center; justify-content: center;
  }
  .capture-btn::before {
    content: ''; width: 54px; height: 54px; border-radius: 50%;
    background: var(--white); transition: var(--r);
  }
  .capture-btn:hover::before { background: var(--gold); }
  .capture-btn:active { transform: scale(0.93); }

  /* ─── COLOUR PICKER ─── */
  .color-row {
    display: flex; gap: 0.5rem; align-items: center; margin-top: 0.8rem; padding: 0 0.3rem;
    overflow-x: auto; scrollbar-width: none;
  }
  .color-row::-webkit-scrollbar { display: none; }
  .color-dot {
    width: 26px; height: 26px; border-radius: 50%; cursor: pointer; flex-shrink: 0;
    border: 2px solid transparent; transition: var(--r);
  }
  .color-dot:hover, .color-dot.active { border-color: var(--white); transform: scale(1.2); }
  .color-label { font-size: 0.68rem; color: var(--light-gray); margin-right: 0.2rem; flex-shrink:0; }

  /* ─── RIGHT SIDE ACTIONS (TikTok-style vertical) ─── */
  .side-actions {
    position: fixed; right: 1rem; bottom: 220px; z-index: 160;
    display: flex; flex-direction: column; gap: 1.2rem; align-items: center;
  }
  .side-btn {
    display: flex; flex-direction: column; align-items: center; gap: 0.3rem;
    cursor: pointer; color: var(--white);
  }
  .side-btn .icon-wrap {
    width: 44px; height: 44px; border-radius: 50%;
    background: rgba(255,255,255,0.1); backdrop-filter: blur(8px);
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; transition: var(--r);
    border: 1px solid rgba(255,255,255,0.1);
  }
  .side-btn:hover .icon-wrap { background: rgba(201,168,76,0.3); border-color: var(--gold); color: var(--gold); }
  .side-btn span { font-size: 0.62rem; color: var(--light-gray); }

  /* ─── OPACITY SLIDER ─── */
  .opacity-row {
    display: flex; align-items: center; gap: 0.7rem; margin-top: 0.7rem; padding: 0 0.3rem;
  }
  .opacity-row label { font-size: 0.68rem; color: var(--light-gray); flex-shrink: 0; }
  .opacity-row input[type=range] {
    flex: 1; -webkit-appearance: none; height: 3px; background: rgba(255,255,255,0.15); border-radius: 3px; outline: none;
  }
  .opacity-row input[type=range]::-webkit-slider-thumb {
    -webkit-appearance: none; width: 16px; height: 16px; border-radius: 50%;
    background: var(--gold); cursor: pointer;
  }
  .opacity-val { font-size: 0.72rem; color: var(--gold); min-width: 28px; text-align: right; }

  /* ─── SNAPSHOT FLASH ─── */
  .flash {
    position: fixed; inset: 0; background: #fff; z-index: 400;
    opacity: 0; pointer-events: none; transition: opacity 0.05s;
  }
  .flash.go { opacity: 0.85; }

  /* ─── TOAST ─── */
  .toast {
    position: fixed; bottom: 220px; left: 50%; transform: translateX(-50%) translateY(20px);
    background: rgba(201,168,76,0.9); color: #000; padding: 0.6rem 1.4rem;
    border-radius: 20px; font-size: 0.82rem; font-weight: 600;
    opacity: 0; transition: all 0.3s; z-index: 500; white-space: nowrap;
  }
  .toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }

  /* ─── NO FACE ─── */
  .no-face-hint {
    position: fixed; top: 50%; left: 50%; transform: translate(-50%,-50%);
    background: rgba(0,0,0,0.6); backdrop-filter: blur(8px);
    border: 1px solid rgba(201,168,76,0.2); border-radius: 12px;
    padding: 0.8rem 1.4rem; font-size: 0.82rem; color: var(--light-gray);
    display: none; z-index: 140; text-align: center; pointer-events: none;
  }
  .no-face-hint.show { display: block; }

  /* ─── FACE RING ─── */
  .face-ring-canvas {
    position: fixed; inset: 0; pointer-events: none; z-index: 130;
    width: 100%; height: 100%; transform: scaleX(-1);
  }

  /* ─── CATEGORY TABS ─── */
  .cat-tabs {
    display: flex; gap: 0.5rem; margin-bottom: 0.8rem; overflow-x: auto;
    padding-bottom: 0.3rem; scrollbar-width: none;
  }
  .cat-tabs::-webkit-scrollbar { display: none; }
  .cat-tab {
    padding: 0.3rem 0.9rem; border-radius: 20px; font-size: 0.72rem;
    font-weight: 600; cursor: pointer; border: none; flex-shrink: 0;
    background: rgba(255,255,255,0.08); color: var(--light-gray);
    transition: var(--r); letter-spacing: 0.05em; text-transform: uppercase;
  }
  .cat-tab.active { background: var(--gold); color: #000; }

  @media (max-width: 480px) {
    .navbar { padding: 0.7rem 1rem; }
    .logo { font-size: 1.2rem; }
    .nav-badge { display: none; }
    .chip-img { width: 54px; height: 54px; }
    .chip-label { font-size: 0.62rem; max-width: 54px; }
  }
  </style>
</head>
<body>

<!-- ── PERMISSION SCREEN ── -->
<div class="permission-screen" id="permScreen">
  <div class="icon"><i class="fas fa-camera"></i></div>
  <h2>AR Hair Try-On</h2>
  <p>See the hottest 2025 hairstyles on your face in real time — like TikTok filters.</p>
  <button class="btn-start" onclick="startCamera()">
    <i class="fas fa-play"></i> &nbsp;Enable Camera
  </button>
  <p style="font-size:0.75rem;color:var(--gray)">Camera stays on your device. Nothing is uploaded.</p>
  <div id="permError" style="display:none;color:#f07f7f;font-size:0.85rem;max-width:320px;line-height:1.5;margin-top:0.5rem"></div>
</div>

<!-- ── LOADING ── -->
<div class="loading-overlay hidden" id="loadingOverlay">
  <div class="spinner"></div>
  <strong>Loading AR Engine</strong>
  <p id="loadingMsg">Initialising face detection...</p>
</div>

<!-- ── CAMERA STAGE ── -->
<div class="stage">
  <video id="video" autoplay playsinline muted></video>
  <canvas id="overlay-canvas"></canvas>
  <canvas id="debug-canvas"></canvas>
</div>

<!-- ── FLASH ── -->
<div class="flash" id="flash"></div>

<!-- ── NAVBAR ── -->
<nav class="navbar">
  <a href="index.php" class="logo">BARBER<span>BUS</span></a>
  <div class="nav-badge"><i class="fas fa-magic"></i> &nbsp;AR Try-On</div>
  <div class="nav-right">
    <a href="booking.php" class="nav-btn gold"><i class="fas fa-scissors"></i> Book Style</a>
    <a href="index.php" class="nav-btn outline"><i class="fas fa-home"></i></a>
  </div>
</nav>

<!-- ── NO FACE HINT ── -->
<div class="no-face-hint" id="noFaceHint">
  <i class="fas fa-face-smile" style="color:var(--gold);margin-right:0.5rem"></i>
  Point camera at your face to try styles
</div>

<!-- ── SIDE ACTIONS ── -->
<div class="side-actions">
  <div class="side-btn" onclick="toggleMirror()">
    <div class="icon-wrap" id="mirrorBtn"><i class="fas fa-left-right"></i></div>
    <span>Flip</span>
  </div>
  <div class="side-btn" onclick="savePhoto()">
    <div class="icon-wrap"><i class="fas fa-download"></i></div>
    <span>Save</span>
  </div>
  <div class="side-btn" onclick="shareStyle()">
    <div class="icon-wrap"><i class="fas fa-share-nodes"></i></div>
    <span>Share</span>
  </div>
  <div class="side-btn" onclick="toggleInfo()">
    <div class="icon-wrap" id="infoBtn"><i class="fas fa-info"></i></div>
    <span>Info</span>
  </div>
</div>

<!-- ── BOTTOM PANEL ── -->
<div class="bottom-panel">

  <!-- Category Tabs -->
  <div class="cat-tabs" id="catTabs">
    <button class="cat-tab active" onclick="filterCat('all',this)">All 2025</button>
    <button class="cat-tab" onclick="filterCat('fade',this)">Fade</button>
    <button class="cat-tab" onclick="filterCat('classic',this)">Classic</button>
    <button class="cat-tab" onclick="filterCat('textured',this)">Textured</button>
    <button class="cat-tab" onclick="filterCat('long',this)">Long</button>
    <button class="cat-tab" onclick="filterCat('buzz',this)">Buzz</button>
  </div>

  <!-- Style label -->
  <div class="style-label" id="styleLabel">🔥 Trending Hairstyles 2025</div>

  <!-- Style chips -->
  <div class="style-scroll" id="styleScroll"></div>

  <!-- Colour row -->
  <div class="color-row" id="colorRow">
    <span class="color-label">Colour:</span>
  </div>

  <!-- Opacity slider -->
  <div class="opacity-row">
    <label><i class="fas fa-sliders"></i>&nbsp; Blend</label>
    <input type="range" min="40" max="100" value="85" id="opacitySlider" oninput="setOpacity(this.value)"/>
    <span class="opacity-val" id="opacityVal">85%</span>
  </div>

  <!-- Controls bar -->
  <div class="controls-bar">
    <div class="ctrl-group">
      <button class="ctrl-btn" onclick="scaleHair(-0.05)" title="Smaller"><i class="fas fa-minus"></i></button>
      <button class="ctrl-btn" onclick="scaleHair(0.05)"  title="Bigger"><i class="fas fa-plus"></i></button>
    </div>

    <button class="capture-btn" onclick="capturePhoto()" title="Capture"></button>

    <div class="ctrl-group">
      <button class="ctrl-btn" onclick="shiftHair(-5)"  title="Move Left"><i class="fas fa-arrow-left"></i></button>
      <button class="ctrl-btn" onclick="shiftHair(5)"   title="Move Right"><i class="fas fa-arrow-right"></i></button>
    </div>
  </div>
</div>

<!-- ── TOAST ── -->
<div class="toast" id="toast"></div>

<!-- ── SCRIPTS ── -->
<!-- TensorFlow.js + BlazeFace for real face detection -->
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.15.0/dist/tf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/blazeface@0.1.0/dist/blazeface.min.js"></script>

<script>
// ═══════════════════════════════════════════
// BARBERBUS – AR HAIR TRY-ON ENGINE
// Real face detection via TensorFlow BlazeFace
// ═══════════════════════════════════════════

// ── HAIRSTYLE DATA ──
// Each style uses SVG path data drawn relative to face bounding box
// or is rendered via Canvas 2D primitives keyed to face landmarks
const STYLES = [
  {
    id: 'none', label: 'Remove', emoji: '🚫', cat: 'all',
    colors: [],
    render: () => {} // no hair
  },
  {
    id: 'skin-fade',     label: 'Skin Fade',      emoji: '💈', cat: 'fade',    trending: true,
    colors: ['#1a0d00','#3b1f0a','#6b3a1f','#8B6543','#c8a882','#f5d090','#e8c99a'],
    render: renderSkinFade
  },
  {
    id: 'quiff',         label: 'Modern Quiff',   emoji: '✨', cat: 'textured', trending: true,
    colors: ['#1a0d00','#3b1f0a','#5a3014','#8B6543','#c8a882','#d4a857','#f5d090'],
    render: renderQuiff
  },
  {
    id: 'textured-crop', label: 'Textured Crop',  emoji: '🔥', cat: 'textured', trending: true,
    colors: ['#1a0d00','#3b1f0a','#6b3a1f','#8B6543','#c8a882','#ffeaa0','#f0f0f0'],
    render: renderTexturedCrop
  },
  {
    id: 'buzz',          label: 'Buzz Cut',        emoji: '⚡', cat: 'buzz',
    colors: ['#1a0d00','#3b1f0a','#6b3a1f','#8B6543','#c8a882'],
    render: renderBuzzCut
  },
  {
    id: 'mid-fade',      label: 'Mid Fade',        emoji: '💎', cat: 'fade',    trending: true,
    colors: ['#1a0d00','#3b1f0a','#4a2510','#8B6543','#c8a882','#f5d090','#e0e0e0'],
    render: renderMidFade
  },
  {
    id: 'pompadour',     label: 'Pompadour',       emoji: '👑', cat: 'classic',
    colors: ['#1a0d00','#3b1f0a','#5a3014','#8B6543','#c8a882','#f5d090'],
    render: renderPompadour
  },
  {
    id: 'curtains',      label: 'Curtains 90s',    emoji: '🎵', cat: 'classic',  trending: true,
    colors: ['#1a0d00','#3b1f0a','#5a3014','#8B6543','#c8a882','#d4a857','#f5d090','#f0f0f0'],
    render: renderCurtains
  },
  {
    id: 'slick-back',    label: 'Slick Back',      emoji: '🕴️', cat: 'classic',
    colors: ['#1a0d00','#3b1f0a','#5a3014','#8B6543','#c8a882'],
    render: renderSlickBack
  },
  {
    id: 'flow',          label: 'The Flow',         emoji: '🌊', cat: 'long',    trending: true,
    colors: ['#1a0d00','#3b1f0a','#5a3014','#8B6543','#c8a882','#d4a857','#f5d090'],
    render: renderFlow
  },
  {
    id: 'bro-flow',      label: 'Bro Flow',         emoji: '🏄', cat: 'long',
    colors: ['#1a0d00','#3b1f0a','#5a3014','#8B6543','#c8a882','#d4a857'],
    render: renderBroFlow
  },
  {
    id: 'edgar',         label: 'Edgar Cut',        emoji: '🔪', cat: 'fade',    trending: true,
    colors: ['#1a0d00','#3b1f0a','#5a3014','#8B6543','#c8a882','#f5d090','#e0e0e0'],
    render: renderEdgar
  },
];

// ── STATE ──
let model       = null;
let stream      = null;
let rafId       = null;
let faceBoxes   = [];
let currentStyle= STYLES[1];
let hairColor   = '#1a0d00';
let opacity     = 0.85;
let hairScale   = 1.0;
let hairOffsetX = 0;
let mirrored    = true;
let showGuide   = true;
let noFaceTimer = null;
let filteredCat = 'all';

const video   = document.getElementById('video');
const canvas  = document.getElementById('overlay-canvas');
const ctx     = canvas.getContext('2d');

if (!CanvasRenderingContext2D.prototype.roundRect) {
  CanvasRenderingContext2D.prototype.roundRect = function(x, y, w, h, r) {
    if (typeof r === 'undefined') r = 5;
    if (typeof r === 'number') r = { tl: r, tr: r, br: r, bl: r };
    this.beginPath();
    this.moveTo(x + r.tl, y);
    this.lineTo(x + w - r.tr, y);
    this.quadraticCurveTo(x + w, y, x + w, y + r.tr);
    this.lineTo(x + w, y + h - r.br);
    this.quadraticCurveTo(x + w, y + h, x + w - r.br, y + h);
    this.lineTo(x + r.bl, y + h);
    this.quadraticCurveTo(x, y + h, x, y + h - r.bl);
    this.lineTo(x, y + r.tl);
    this.quadraticCurveTo(x, y, x + r.tl, y);
    this.closePath();
  };
}

function showPermError(message) {
  const err = document.getElementById('permError');
  if (err) {
    err.textContent = message;
    err.style.display = 'block';
  }
}

function clearPermError() {
  const err = document.getElementById('permError');
  if (err) {
    err.style.display = 'none';
    err.textContent = '';
  }
}

// ── UI BUILD ──
function buildUI() {
  buildStyleChips(STYLES);
  buildColorDots(currentStyle.colors);
}

function buildStyleChips(list) {
  const scroll = document.getElementById('styleScroll');
  scroll.innerHTML = list.map(s => `
    <div class="style-chip ${s === currentStyle ? 'active' : ''}" id="chip-${s.id}" onclick="selectStyle('${s.id}')">
      <div class="chip-img">
        ${s.emoji}
        ${s.trending ? '<div class="chip-trend">HOT</div>' : ''}
      </div>
      <div class="chip-label">${s.label}</div>
    </div>
  `).join('');
}

function buildColorDots(colors) {
  const row = document.getElementById('colorRow');
  row.innerHTML = '<span class="color-label">Colour:</span>';
  if (!colors || !colors.length) {
    row.innerHTML += '<span style="font-size:0.72rem;color:var(--gray)">–</span>';
    return;
  }
  const colorNames = ['Jet Black','Dark Brown','Brown','Medium Brown','Light Brown','Dirty Blonde','Blonde','Platinum','Silver','Red','Auburn','Grey'];
  colors.forEach((c, i) => {
    const dot = document.createElement('div');
    dot.className = 'color-dot' + (c === hairColor ? ' active' : '');
    dot.style.background = c;
    dot.title = colorNames[i] || c;
    dot.onclick = () => {
      hairColor = c;
      document.querySelectorAll('.color-dot').forEach(d => d.classList.remove('active'));
      dot.classList.add('active');
    };
    row.appendChild(dot);
  });
}

function selectStyle(id) {
  currentStyle = STYLES.find(s => s.id === id) || STYLES[0];
  document.querySelectorAll('.style-chip').forEach(c => c.classList.remove('active'));
  const chip = document.getElementById('chip-' + id);
  if (chip) chip.classList.add('active');
  buildColorDots(currentStyle.colors);
  if (currentStyle.colors?.length) hairColor = currentStyle.colors[0];
  document.getElementById('styleLabel').textContent = currentStyle.label + ' — Selected';
}

function filterCat(cat, btn) {
  filteredCat = cat;
  document.querySelectorAll('.cat-tab').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  const list = cat === 'all' ? STYLES : STYLES.filter(s => s.cat === cat || s.id === 'none');
  buildStyleChips(list);
}

// ── CAMERA START ──
async function startCamera() {
  clearPermError();
  document.getElementById('permScreen').style.display = 'none';
  document.getElementById('loadingOverlay').classList.remove('hidden');
  setLoadingMsg('Starting camera...');

  try {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      throw new Error('Camera access is not supported by this browser.');
    }
    if (!window.isSecureContext && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
      throw new Error('Camera access requires HTTPS or localhost. Open the page using HTTPS or localhost.');
    }

    stream = await navigator.mediaDevices.getUserMedia({
      video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } },
      audio: false
    });
    video.srcObject = stream;
    await new Promise(r => video.onloadedmetadata = r);
    await video.play();

    setLoadingMsg('Loading AI face model...');
    model = await blazeface.load();

    setLoadingMsg('Ready!');
    setTimeout(() => {
      document.getElementById('loadingOverlay').classList.add('hidden');
      buildUI();
      startDetection();
    }, 500);
  } catch(e) {
    let message = 'Camera error: ' + (e.message || 'Unable to access camera.');
    if (e.name === 'NotAllowedError' || e.name === 'PermissionDeniedError') {
      message = 'Camera permission denied. Please allow access in your browser settings and reload the page.';
    } else if (e.name === 'NotFoundError' || e.name === 'OverconstrainedError') {
      message = 'No camera found or no compatible camera is available.';
    } else if (e.name === 'NotReadableError') {
      message = 'Camera is already in use by another app. Close it and try again.';
    } else if (e.name === 'SecurityError') {
      message = 'Camera access requires a secure connection (HTTPS or localhost).';
    }

    setLoadingMsg(message);
    showPermError(message);
    setTimeout(() => {
      document.getElementById('loadingOverlay').classList.add('hidden');
      document.getElementById('permScreen').style.display = 'flex';
    }, 2500);
  }
}

function setLoadingMsg(msg) {
  document.getElementById('loadingMsg').textContent = msg;
}

// ── DETECTION LOOP ──
async function startDetection() {
  resizeCanvas();
  window.addEventListener('resize', resizeCanvas);
  detect();
}

function resizeCanvas() {
  canvas.width  = video.videoWidth  || window.innerWidth;
  canvas.height = video.videoHeight || window.innerHeight;
}

async function detect() {
  if (!model) { rafId = requestAnimationFrame(detect); return; }
  try {
    const preds = await model.estimateFaces(video, false);
    faceBoxes = preds;

    ctx.clearRect(0, 0, canvas.width, canvas.height);

    if (preds.length === 0) {
      showNoFace(true);
    } else {
      showNoFace(false);
      preds.forEach(face => drawHair(ctx, face, canvas.width, canvas.height));
    }
  } catch(e) {}
  rafId = requestAnimationFrame(detect);
}

function showNoFace(show) {
  const hint = document.getElementById('noFaceHint');
  if (show) {
    clearTimeout(noFaceTimer);
    noFaceTimer = setTimeout(() => hint.classList.add('show'), 1500);
  } else {
    clearTimeout(noFaceTimer);
    hint.classList.remove('show');
  }
}

// ── DRAW HAIR ──
function drawHair(ctx, face, cw, ch) {
  if (!currentStyle || currentStyle.id === 'none') return;

  // BlazeFace returns topLeft, bottomRight, landmarks
  const [x1, y1] = face.topLeft;
  const [x2, y2] = face.bottomRight;

  // Scale canvas coords to actual canvas size
  const scaleX = cw / video.videoWidth;
  const scaleY = ch / video.videoHeight;

  const fx = x1 * scaleX;
  const fy = y1 * scaleY;
  const fw = (x2 - x1) * scaleX * hairScale;
  const fh = (y2 - y1) * scaleY * hairScale;

  // Face centre top (forehead)
  const cx = (fx + (x2 - x1) * scaleX / 2) + hairOffsetX;
  const topY = fy;

  ctx.save();
  ctx.globalAlpha = opacity;

  // Pass bounding box to style renderer
  currentStyle.render(ctx, { cx, topY, fw, fh, fx, fy, x1: fx, x2: x2*scaleX, y1: fy, y2: y2*scaleY, color: hairColor });

  ctx.restore();
}

// ═══════════════════════════════════════════
// HAIRSTYLE RENDERERS
// All receive: ctx, {cx, topY, fw, fh, color}
// ═══════════════════════════════════════════

function setHairStyle(ctx, color, blur = 2) {
  ctx.fillStyle = color;
  ctx.shadowColor = 'rgba(0,0,0,0.5)';
  ctx.shadowBlur = blur;
  ctx.strokeStyle = shadeColor(color, -30);
  ctx.lineWidth = 1;
}

function shadeColor(hex, pct) {
  let r = parseInt(hex.slice(1,3),16);
  let g = parseInt(hex.slice(3,5),16);
  let b = parseInt(hex.slice(5,7),16);
  r = Math.max(0,Math.min(255,r+pct)); g = Math.max(0,Math.min(255,g+pct)); b = Math.max(0,Math.min(255,b+pct));
  return `rgb(${r},${g},${b})`;
}

function renderSkinFade(ctx, f) {
  const { cx, topY, fw, fh, color } = f;
  const r = fw * 0.52;
  const top = topY - fh * 0.05;

  // Main hair mass on top
  setHairStyle(ctx, color, 6);
  ctx.beginPath();
  ctx.ellipse(cx, top, r * 0.7, fh * 0.28, 0, 0, Math.PI * 2);
  ctx.fill();

  // Side fades (gradient transparency)
  const leftGrad = ctx.createLinearGradient(cx - r, top, cx - r*0.3, top);
  leftGrad.addColorStop(0, 'transparent');
  leftGrad.addColorStop(1, color);
  ctx.fillStyle = leftGrad; ctx.globalAlpha = opacity * 0.6;
  ctx.beginPath();
  ctx.ellipse(cx - r*0.55, top + fh*0.12, r*0.35, fh*0.22, 0, 0, Math.PI*2);
  ctx.fill();

  const rightGrad = ctx.createLinearGradient(cx + r*0.3, top, cx + r, top);
  rightGrad.addColorStop(0, color); rightGrad.addColorStop(1, 'transparent');
  ctx.fillStyle = rightGrad;
  ctx.beginPath();
  ctx.ellipse(cx + r*0.55, top + fh*0.12, r*0.35, fh*0.22, 0, 0, Math.PI*2);
  ctx.fill();
}

function renderTexturedCrop(ctx, f) {
  const { cx, topY, fw, fh, color } = f;
  const w = fw * 0.52;
  const top = topY - fh * 0.08;
  setHairStyle(ctx, color, 5);

  // Base block
  ctx.beginPath();
  ctx.ellipse(cx, top, w * 0.75, fh * 0.22, 0, 0, Math.PI*2);
  ctx.fill();

  // Textured top strands
  const lighter = shadeColor(color, 20);
  ctx.strokeStyle = lighter; ctx.lineWidth = fw * 0.025; ctx.lineCap = 'round';
  ctx.globalAlpha = opacity * 0.7;
  for (let i = -3; i <= 3; i++) {
    const tx = cx + i * (w * 0.2);
    ctx.beginPath();
    ctx.moveTo(tx, top + fh*0.05);
    ctx.bezierCurveTo(tx + (Math.random()-0.5)*fw*0.1, top - fh*0.08, tx + (Math.random()-0.5)*fw*0.1, top - fh*0.18, tx + (Math.random()-0.5)*fw*0.15, top - fh*0.22);
    ctx.stroke();
  }

  // Straight front fringe line
  ctx.globalAlpha = opacity;
  ctx.fillStyle = color;
  ctx.beginPath();
  ctx.roundRect(cx - w*0.72, top + fh*0.06, w*1.44, fh*0.08, 4);
  ctx.fill();
}

function renderQuiff(ctx, f) {
  const { cx, topY, fw, fh, color } = f;
  const w = fw * 0.5;
  const top = topY - fh * 0.02;
  setHairStyle(ctx, color, 6);

  // Base sides
  ctx.beginPath();
  ctx.ellipse(cx, top + fh*0.1, w*0.8, fh*0.25, 0, 0, Math.PI*2);
  ctx.fill();

  // Quiff volume on top
  ctx.beginPath();
  ctx.moveTo(cx - w*0.55, top + fh*0.08);
  ctx.bezierCurveTo(cx - w*0.3, top - fh*0.35, cx + w*0.3, top - fh*0.38, cx + w*0.55, top + fh*0.08);
  ctx.bezierCurveTo(cx + w*0.3, top + fh*0.01, cx - w*0.3, top + fh*0.01, cx - w*0.55, top + fh*0.08);
  ctx.fill();

  // Highlight strand on quiff
  ctx.strokeStyle = shadeColor(color, 35);
  ctx.lineWidth = fw * 0.018; ctx.lineCap = 'round';
  ctx.globalAlpha = opacity * 0.5;
  ctx.beginPath();
  ctx.moveTo(cx - w*0.1, top - fh*0.32);
  ctx.bezierCurveTo(cx, top - fh*0.38, cx + w*0.15, top - fh*0.3, cx + w*0.2, top - fh*0.1);
  ctx.stroke();
}

function renderBuzzCut(ctx, f) {
  const { cx, topY, fw, fh, color } = f;
  const w = fw * 0.52;
  const top = topY + fh*0.02;

  // Very tight, close to scalp – uniform oval
  const grad = ctx.createRadialGradient(cx, top, 0, cx, top, w*0.8);
  grad.addColorStop(0, shadeColor(color, 20));
  grad.addColorStop(1, color);
  ctx.fillStyle = grad; ctx.globalAlpha = opacity;
  ctx.shadowColor = 'rgba(0,0,0,0.4)'; ctx.shadowBlur = 4;

  ctx.beginPath();
  ctx.ellipse(cx, top, w*0.73, fh*0.18, 0, 0, Math.PI*2);
  ctx.fill();

  // Slight texture dots
  ctx.fillStyle = shadeColor(color, -15);
  ctx.globalAlpha = opacity * 0.3;
  for (let i = 0; i < 60; i++) {
    const tx = cx + (Math.random()-0.5)*w*1.3;
    const ty = top + (Math.random()-0.5)*fh*0.28;
    ctx.beginPath();
    ctx.arc(tx, ty, 1.2, 0, Math.PI*2);
    ctx.fill();
  }
}

function renderMidFade(ctx, f) {
  const { cx, topY, fw, fh, color } = f;
  const w = fw * 0.52;
  const top = topY - fh * 0.06;
  setHairStyle(ctx, color, 5);

  // Top mass – slightly longer than skin fade
  ctx.beginPath();
  ctx.ellipse(cx, top, w * 0.72, fh * 0.3, 0, 0, Math.PI * 2);
  ctx.fill();

  // Left mid fade
  const lg = ctx.createLinearGradient(cx - w*0.8, top + fh*0.1, cx - w*0.2, top + fh*0.1);
  lg.addColorStop(0, 'transparent'); lg.addColorStop(0.4, shadeColor(color,-20)); lg.addColorStop(1, color);
  ctx.fillStyle = lg; ctx.globalAlpha = opacity * 0.75;
  ctx.beginPath();
  ctx.ellipse(cx - w*0.55, top + fh*0.18, w*0.38, fh*0.28, 0, 0, Math.PI*2);
  ctx.fill();

  // Right mid fade
  const rg = ctx.createLinearGradient(cx + w*0.2, top + fh*0.1, cx + w*0.8, top + fh*0.1);
  rg.addColorStop(0, color); rg.addColorStop(0.6, shadeColor(color,-20)); rg.addColorStop(1, 'transparent');
  ctx.fillStyle = rg;
  ctx.beginPath();
  ctx.ellipse(cx + w*0.55, top + fh*0.18, w*0.38, fh*0.28, 0, 0, Math.PI*2);
  ctx.fill();
}

function renderPompadour(ctx, f) {
  const { cx, topY, fw, fh, color } = f;
  const w = fw * 0.52;
  const top = topY - fh * 0.02;
  setHairStyle(ctx, color, 7);

  // Side volume
  ctx.beginPath();
  ctx.ellipse(cx, top + fh*0.15, w*0.8, fh*0.28, 0, 0, Math.PI*2);
  ctx.fill();

  // Pompadour wave – tall volume at front
  ctx.beginPath();
  ctx.moveTo(cx - w*0.6, top + fh*0.1);
  ctx.bezierCurveTo(cx - w*0.4, top - fh*0.42, cx + w*0.4, top - fh*0.45, cx + w*0.6, top + fh*0.1);
  ctx.bezierCurveTo(cx + w*0.35, top - fh*0.05, cx - w*0.35, top - fh*0.05, cx - w*0.6, top + fh*0.1);
  ctx.fill();

  // Shine highlight
  const sg = ctx.createLinearGradient(cx - w*0.2, top - fh*0.4, cx + w*0.05, top - fh*0.15);
  sg.addColorStop(0, 'rgba(255,255,255,0.28)'); sg.addColorStop(1, 'transparent');
  ctx.fillStyle = sg; ctx.globalAlpha = opacity * 0.6;
  ctx.beginPath();
  ctx.ellipse(cx - w*0.08, top - fh*0.28, w*0.18, fh*0.13, -0.4, 0, Math.PI*2);
  ctx.fill();
}

function renderCurtains(ctx, f) {
  const { cx, topY, fw, fh, color } = f;
  const w = fw * 0.52;
  const top = topY - fh * 0.04;
  setHairStyle(ctx, color, 5);

  // Base layer
  ctx.beginPath();
  ctx.ellipse(cx, top + fh*0.08, w*0.82, fh*0.27, 0, 0, Math.PI*2);
  ctx.fill();

  // Left curtain falling
  ctx.beginPath();
  ctx.moveTo(cx - w*0.05, top + fh*0.05);
  ctx.bezierCurveTo(cx - w*0.45, top - fh*0.12, cx - w*0.7, top + fh*0.08, cx - w*0.75, top + fh*0.38);
  ctx.bezierCurveTo(cx - w*0.6, top + fh*0.42, cx - w*0.3, top + fh*0.22, cx - w*0.05, top + fh*0.15);
  ctx.fill();

  // Right curtain
  ctx.beginPath();
  ctx.moveTo(cx + w*0.05, top + fh*0.05);
  ctx.bezierCurveTo(cx + w*0.45, top - fh*0.12, cx + w*0.7, top + fh*0.08, cx + w*0.75, top + fh*0.38);
  ctx.bezierCurveTo(cx + w*0.6, top + fh*0.42, cx + w*0.3, top + fh*0.22, cx + w*0.05, top + fh*0.15);
  ctx.fill();

  // Centre parting – lighter strip
  ctx.fillStyle = shadeColor(color, 25);
  ctx.globalAlpha = opacity * 0.4;
  ctx.beginPath();
  ctx.ellipse(cx, top + fh*0.06, w*0.06, fh*0.2, 0, 0, Math.PI*2);
  ctx.fill();
}

function renderSlickBack(ctx, f) {
  const { cx, topY, fw, fh, color } = f;
  const w = fw * 0.52;
  const top = topY - fh * 0.03;
  setHairStyle(ctx, color, 5);

  ctx.beginPath();
  ctx.ellipse(cx + w*0.1, top + fh*0.08, w*0.78, fh*0.25, -0.1, 0, Math.PI*2);
  ctx.fill();

  // Slick strands going back
  ctx.strokeStyle = shadeColor(color, 18); ctx.lineWidth = fw*0.015; ctx.lineCap = 'round';
  ctx.globalAlpha = opacity * 0.55;
  for (let i = -4; i <= 4; i++) {
    ctx.beginPath();
    ctx.moveTo(cx + i*w*0.12, top + fh*0.05);
    ctx.bezierCurveTo(cx + i*w*0.12 + w*0.15, top - fh*0.05, cx + i*w*0.12 + w*0.3, top, cx + i*w*0.12 + w*0.35, top + fh*0.1);
    ctx.stroke();
  }

  // Wet-look shine
  const sg = ctx.createLinearGradient(cx - w*0.4, top - fh*0.1, cx + w*0.6, top + fh*0.1);
  sg.addColorStop(0, 'transparent'); sg.addColorStop(0.4, 'rgba(255,255,255,0.2)'); sg.addColorStop(1, 'transparent');
  ctx.fillStyle = sg; ctx.globalAlpha = opacity * 0.5;
  ctx.beginPath();
  ctx.ellipse(cx + w*0.1, top + fh*0.04, w*0.5, fh*0.1, -0.1, 0, Math.PI*2);
  ctx.fill();
}

function renderFlow(ctx, f) {
  const { cx, topY, fw, fh, color } = f;
  const w = fw * 0.54;
  const top = topY - fh * 0.05;
  setHairStyle(ctx, color, 6);

  // Long flowing hair – goes below face
  // Left flow
  ctx.beginPath();
  ctx.moveTo(cx - w*0.1, top + fh*0.05);
  ctx.bezierCurveTo(cx - w*0.6, top - fh*0.05, cx - w*0.9, top + fh*0.2, cx - w*0.85, top + fh*0.85);
  ctx.bezierCurveTo(cx - w*0.75, top + fh*0.9, cx - w*0.55, top + fh*0.88, cx - w*0.45, top + fh*0.7);
  ctx.bezierCurveTo(cx - w*0.5, top + fh*0.5, cx - w*0.3, top + fh*0.3, cx - w*0.08, top + fh*0.15);
  ctx.fill();

  // Right flow
  ctx.beginPath();
  ctx.moveTo(cx + w*0.1, top + fh*0.05);
  ctx.bezierCurveTo(cx + w*0.6, top - fh*0.05, cx + w*0.9, top + fh*0.2, cx + w*0.85, top + fh*0.85);
  ctx.bezierCurveTo(cx + w*0.75, top + fh*0.9, cx + w*0.55, top + fh*0.88, cx + w*0.45, top + fh*0.7);
  ctx.bezierCurveTo(cx + w*0.5, top + fh*0.5, cx + w*0.3, top + fh*0.3, cx + w*0.08, top + fh*0.15);
  ctx.fill();

  // Top
  ctx.beginPath();
  ctx.ellipse(cx, top, w*0.72, fh*0.26, 0, 0, Math.PI*2);
  ctx.fill();
}

function renderBroFlow(ctx, f) {
  const { cx, topY, fw, fh, color } = f;
  const w = fw * 0.52;
  const top = topY - fh*0.03;
  setHairStyle(ctx, color, 5);

  // Medium length – touches ears
  ctx.beginPath();
  ctx.moveTo(cx - w*0.05, top + fh*0.05);
  ctx.bezierCurveTo(cx - w*0.5, top - fh*0.08, cx - w*0.8, top + fh*0.15, cx - w*0.78, top + fh*0.55);
  ctx.bezierCurveTo(cx - w*0.68, top + fh*0.62, cx - w*0.4, top + fh*0.55, cx - w*0.1, top + fh*0.25);
  ctx.fill();
  ctx.beginPath();
  ctx.moveTo(cx + w*0.05, top + fh*0.05);
  ctx.bezierCurveTo(cx + w*0.5, top - fh*0.08, cx + w*0.8, top + fh*0.15, cx + w*0.78, top + fh*0.55);
  ctx.bezierCurveTo(cx + w*0.68, top + fh*0.62, cx + w*0.4, top + fh*0.55, cx + w*0.1, top + fh*0.25);
  ctx.fill();
  ctx.beginPath();
  ctx.ellipse(cx, top, w*0.7, fh*0.28, 0, 0, Math.PI*2);
  ctx.fill();

  // Wavy strands
  ctx.strokeStyle = shadeColor(color, 22); ctx.lineWidth = fw*0.014; ctx.lineCap = 'round';
  ctx.globalAlpha = opacity * 0.45;
  [cx-w*0.3, cx, cx+w*0.3].forEach(tx => {
    ctx.beginPath();
    ctx.moveTo(tx, top - fh*0.22);
    ctx.bezierCurveTo(tx+w*0.06, top - fh*0.12, tx-w*0.06, top, tx+w*0.04, top + fh*0.1);
    ctx.stroke();
  });
}

function renderEdgar(ctx, f) {
  const { cx, topY, fw, fh, color } = f;
  const w = fw * 0.52;
  const top = topY - fh * 0.04;
  setHairStyle(ctx, color, 5);

  // Tight fade sides
  const lg = ctx.createLinearGradient(cx - w*0.85, top + fh*0.1, cx - w*0.2, top + fh*0.1);
  lg.addColorStop(0,'transparent'); lg.addColorStop(0.5, shadeColor(color,-25)); lg.addColorStop(1, color);
  ctx.fillStyle = lg; ctx.globalAlpha = opacity * 0.7;
  ctx.beginPath();
  ctx.ellipse(cx - w*0.52, top + fh*0.18, w*0.4, fh*0.25, 0, 0, Math.PI*2);
  ctx.fill();
  const rg = ctx.createLinearGradient(cx + w*0.2, top + fh*0.1, cx + w*0.85, top + fh*0.1);
  rg.addColorStop(0, color); rg.addColorStop(0.5, shadeColor(color,-25)); rg.addColorStop(1,'transparent');
  ctx.fillStyle = rg;
  ctx.beginPath();
  ctx.ellipse(cx + w*0.52, top + fh*0.18, w*0.4, fh*0.25, 0, 0, Math.PI*2);
  ctx.fill();

  // Blunt top – signature Edgar straight-across fringe
  ctx.fillStyle = color; ctx.globalAlpha = opacity; ctx.shadowBlur = 5;
  ctx.beginPath();
  ctx.ellipse(cx, top, w*0.7, fh*0.2, 0, 0, Math.PI*2);
  ctx.fill();

  // Straight fringe line – the Edgar signature
  ctx.fillStyle = color;
  ctx.beginPath();
  ctx.roundRect(cx - w*0.68, top + fh*0.06, w*1.36, fh*0.09, 3);
  ctx.fill();

  // Crisp edge – slightly darker stripe
  ctx.fillStyle = shadeColor(color, -35);
  ctx.globalAlpha = opacity * 0.6;
  ctx.beginPath();
  ctx.roundRect(cx - w*0.68, top + fh*0.1, w*1.36, fh*0.025, 2);
  ctx.fill();
}

// ── CONTROLS ──
function setOpacity(val) {
  opacity = val / 100;
  document.getElementById('opacityVal').textContent = val + '%';
}
function scaleHair(delta) {
  hairScale = Math.max(0.5, Math.min(2.0, hairScale + delta));
}
function shiftHair(px) {
  hairOffsetX += px;
}
function toggleMirror() {
  mirrored = !mirrored;
  video.style.transform = mirrored ? 'scaleX(-1)' : 'scaleX(1)';
  canvas.style.transform = mirrored ? 'scaleX(-1)' : 'scaleX(1)';
  document.getElementById('mirrorBtn').classList.toggle('active', !mirrored);
}
function toggleInfo() {
  const btn = document.getElementById('infoBtn');
  btn.classList.toggle('active');
  const style = currentStyle;
  toast(`${style.emoji} ${style.label} — Book this style at BarberBus!`);
}

// ── CAPTURE ──
function capturePhoto() {
  // Flash effect
  const flash = document.getElementById('flash');
  flash.classList.add('go');
  setTimeout(() => flash.classList.remove('go'), 150);

  // Composite video + canvas overlay
  const comp = document.createElement('canvas');
  comp.width  = video.videoWidth;
  comp.height = video.videoHeight;
  const cc = comp.getContext('2d');

  // Draw mirrored video
  cc.save();
  if (mirrored) { cc.translate(comp.width, 0); cc.scale(-1, 1); }
  cc.drawImage(video, 0, 0, comp.width, comp.height);
  cc.restore();

  // Draw hair overlay
  cc.save();
  if (mirrored) { cc.translate(comp.width, 0); cc.scale(-1, 1); }
  cc.drawImage(canvas, 0, 0, comp.width, comp.height);
  cc.restore();

  // Watermark
  cc.font = 'bold 20px "Bebas Neue", sans-serif';
  cc.fillStyle = 'rgba(201,168,76,0.85)';
  cc.fillText('BARBERBUS  ✂  AR TRY-ON', 20, comp.height - 20);

  window._capturedCanvas = comp;
  toast('📸 Photo captured! Tap Save to download.');
}

function savePhoto() {
  const comp = window._capturedCanvas;
  if (!comp) { capturePhoto(); setTimeout(savePhoto, 200); return; }
  const link = document.createElement('a');
  link.download = 'barberbus-hairstyle-' + currentStyle.id + '.png';
  link.href = comp.toDataURL('image/png');
  link.click();
  toast('✅ Saved to your device!');
}

function shareStyle() {
  const msg = `Check out the ${currentStyle.label} style I tried at BarberBus! 💈✂`;
  if (navigator.share) {
    navigator.share({ title: 'BarberBus AR Try-On', text: msg, url: window.location.href });
  } else {
    navigator.clipboard?.writeText(msg + '\n' + window.location.href);
    toast('🔗 Link copied to clipboard!');
  }
}

// ── TOAST ──
function toast(msg) {
  const el = document.getElementById('toast');
  el.textContent = msg;
  el.classList.add('show');
  setTimeout(() => el.classList.remove('show'), 3000);
}

// Auto-init: check if camera already permitted
(async () => {
  try {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      showPermError('Camera access is not supported by this browser.');
      return;
    }
    if (!window.isSecureContext && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
      showPermError('Camera access requires HTTPS or localhost. Please open the page using HTTPS or localhost.');
      return;
    }
    const perm = await navigator.permissions.query({ name: 'camera' });
    if (perm.state === 'granted') startCamera();
  } catch(e) {
    // permission API may not be available in all browsers
  }
})();
</script>
</body>
</html>
