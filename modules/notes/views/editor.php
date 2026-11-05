<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= htmlspecialchars($title) ?> — Coretan</title>
    <meta name="theme-color" content="#7c3aed">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/icons/icon-192.png') ?>">
    <link rel="icon" type="image/png" href="<?= base_url('assets/logo.png') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="<?= base_url('assets/js/tailwindcss.js') ?>"></script>
    <script src="<?= base_url('assets/js/sweetalert2.min.js') ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
    <!-- Tesseract.js v4 (OCR offline fallback) -->
    <script src="https://unpkg.com/tesseract.js@4/dist/tesseract.min.js"></script>
    <script>tailwind.config = { theme: { extend: { fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] } } } }</script>
    <style>
        * { -webkit-tap-highlight-color: transparent; box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; margin: 0; overflow: hidden; }
        .tool-btn { display:flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:9px;border:none;background:transparent;cursor:pointer;color:#64748b;transition:all 0.15s;font-size:13px; }
        .tool-btn:hover { background:#f1f5f9;color:#1e293b; }
        .tool-btn.active { background:#fef3c7;color:#d97706; }
        .divider-v { width:1px;height:20px;background:#e2e8f0;margin:0 2px; }
        #canvas-hw { cursor: crosshair; touch-action: none; }
        /* Lined paper background */
        #hw-wrapper {
            background-color: #fef9f0;
            background-image: repeating-linear-gradient(transparent, transparent 27px, #fed7aa 27px, #fed7aa 28px);
            background-position: 0 4px;
        }
        /* Tab styling */
        .tab-btn { padding: 7px 14px; border-radius: 12px; font-weight: 700; font-size: 12px; cursor: pointer; transition: all 0.15s; border: none; }
        .tab-btn.active { background: #f97316; color: white; box-shadow: 0 4px 12px rgba(249,115,22,0.3); }
        .tab-btn:not(.active) { background: #f1f5f9; color: #64748b; }
        .tab-btn:not(.active):hover { background: #e2e8f0; }
        /* Typed textarea */
        #typed-area { resize: none; outline: none; border: none; background: #ffffff; width: 100%; height: 100%; padding: 16px; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15px; line-height: 1.8; color: #1e293b; }
        #typed-area:focus { background: #ffffff; }
        /* OCR result */
        #ocr-result { background: #f8faff; border: 1.5px dashed #c4b5fd; border-radius: 14px; padding: 12px; font-size: 13px; line-height: 1.6; color: #374151; }
        input[type=range] { accent-color: #f97316; }
        #title-input { background: transparent; border: none; outline: none; font-weight: 700; }
        #title-input:focus { background: white; border-radius: 6px; }

        /* Candidate chip */
        .candidate-chip { display: inline-flex; align-items: center; padding: 3px 8px; border-radius: 8px; background: #ede9fe; color: #6d28d9; font-weight: 600; font-size: 11px; cursor: pointer; transition: all 0.15s; }
        .candidate-chip:hover { background: #ddd6fe; transform: scale(1.05); }

        /* Animation for auto badge */
        @keyframes pulseSlow {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(0.92); }
        }
        .pulse-slow { animation: pulseSlow 2s infinite ease-in-out; }
    </style>
</head>
<body class="bg-slate-100 h-screen flex flex-col select-none">

<!-- ========== TOP HEADER ========== -->
<header class="bg-white border-b border-slate-200 px-3 py-2 flex items-center gap-2 shadow-sm z-10 flex-shrink-0">
    <a href="<?= site_url('notes') ?>" class="tool-btn mr-1" title="Kembali">
        <i class="fa fa-arrow-left text-xs"></i>
    </a>
    <input type="text" id="title-input" value="<?= htmlspecialchars($note->title) ?>"
        class="text-sm font-bold text-slate-800 px-2 py-1 w-32 sm:w-60 truncate"
        onchange="markDirty()">
    <div class="divider-v"></div>

    <!-- Tab switcher -->
    <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl">
        <button class="tab-btn active" id="tab-write" onclick="switchTab('write')">
            <i class="fa fa-pen-nib mr-1"></i>Tulis Tangan
        </button>
        <button class="tab-btn" id="tab-type" onclick="switchTab('type')">
            <i class="fa fa-keyboard mr-1"></i>Ketik
        </button>
    </div>

    <div class="ml-auto flex items-center gap-2">
        <div id="save-status" class="text-xs text-slate-400 hidden sm:block">
            <i class="fa fa-cloud-arrow-up mr-1"></i><span id="save-text">Siap</span>
        </div>
        <button onclick="saveNote(true)"
            class="px-3 sm:px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-400 hover:to-orange-500 text-white font-bold rounded-xl text-xs shadow-lg shadow-orange-500/30 transition-all hover:scale-105 active:scale-95 flex items-center gap-1">
            <i class="fa fa-floppy-disk"></i> <span class="hidden sm:inline">Simpan</span>
        </button>
    </div>
</header>

<!-- ========== MAIN EDITOR (Split layout on desktop) ========== -->
<div class="flex-1 flex overflow-hidden gap-0 relative">

    <!-- ===== PANEL KIRI: Tulis Tangan ===== -->
    <div id="panel-write" class="flex flex-col flex-1 overflow-hidden relative">

        <!-- Drawing Toolbar -->
        <div class="bg-white border-b border-slate-200 px-3 py-1.5 flex items-center gap-1.5 flex-shrink-0 flex-wrap">
            <button class="tool-btn active" id="hw-pen" onclick="setHwTool('pen')" title="Pena"><i class="fa fa-pen-nib"></i></button>
            <button class="tool-btn" id="hw-eraser" onclick="setHwTool('eraser')" title="Hapus"><i class="fa fa-eraser"></i></button>
            <div class="divider-v"></div>

            <!-- Colors -->
            <?php foreach (['#1a1a2e','#7c3aed','#f97316','#ef4444','#10b981','#3b82f6'] as $c): ?>
            <div onclick="setHwColor('<?= $c ?>', this)"
                class="hw-color w-5 h-5 rounded-full cursor-pointer border-2 border-transparent hover:scale-125 transition-all <?= $c === '#1a1a2e' ? 'border-orange-400 scale-110' : '' ?>"
                style="background:<?= $c ?>"></div>
            <?php endforeach; ?>
            <div class="divider-v"></div>

            <input type="range" min="1" max="20" value="3" class="w-14" id="hw-size" oninput="setHwSize(this.value)">
            <span class="text-xs text-slate-400 w-3 text-center" id="hw-size-label">3</span>
            <div class="divider-v"></div>

            <button class="tool-btn" onclick="hwUndo()" title="Undo"><i class="fa fa-rotate-left"></i></button>
            <button class="tool-btn" onclick="clearHw()" title="Bersihkan kanvas" style="color:#ef4444"><i class="fa fa-trash"></i></button>

            <!-- Mode Otomatis & Recognition Controls -->
            <div class="ml-auto flex items-center gap-1.5 flex-wrap">
                <!-- Auto Convert Toggle Button -->
                <button id="btn-auto-toggle" onclick="toggleAutoConvert()"
                    class="flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-bold transition-all shadow-sm bg-emerald-500 text-white shadow-emerald-500/20 hover:bg-emerald-600 active:scale-95"
                    title="Ubah otomatis tulisan tangan menjadi ketikan saat selesai menulis">
                    <span id="auto-dot" class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                    <span id="auto-btn-label">Auto: ON</span>
                </button>

                <!-- Settings Modal Trigger -->
                <button onclick="toggleSettingsModal()" class="tool-btn" title="Pengaturan Otomatis">
                    <i class="fa fa-sliders text-slate-500 hover:text-slate-800"></i>
                </button>

                <!-- OCR Language -->
                <select id="ocr-lang" class="text-xs bg-slate-100 border-0 rounded-lg px-2 py-1.5 text-slate-600 font-semibold cursor-pointer">
                    <option value="ind">🇮🇩 ID</option>
                    <option value="eng">🇬🇧 EN</option>
                </select>

                <!-- Manual OCR Button -->
                <button id="ocr-btn" onclick="runManualOCR()"
                    class="flex items-center gap-1 px-2.5 py-1.5 bg-gradient-to-r from-violet-600 to-violet-700 hover:from-violet-500 hover:to-violet-600 text-white font-bold rounded-xl text-xs shadow-md shadow-violet-500/20 transition-all hover:scale-105 active:scale-95">
                    <i class="fa fa-wand-magic-sparkles text-xs"></i>
                    <span class="hidden sm:inline">Kenali Tulisan</span>
                </button>
            </div>
        </div>

        <!-- Kanvas tulis tangan -->
        <div id="hw-wrapper" class="flex-1 overflow-hidden relative">
            <canvas id="canvas-hw"></canvas>

            <!-- Floating Live Status Pill (Notification at Top) -->
            <div id="live-indicator" class="absolute top-3 left-1/2 -translate-x-1/2 z-30 pointer-events-none transition-all duration-300 opacity-0 transform -translate-y-2">
                <div id="live-indicator-badge" class="px-4 py-2 rounded-2xl shadow-xl text-xs font-bold flex items-center gap-2.5 bg-slate-900/90 text-white backdrop-blur-md border border-slate-700">
                    <i id="live-indicator-icon" class="fa fa-bolt text-amber-400"></i>
                    <span id="live-indicator-text">Mengubah ke ketikan...</span>
                </div>
            </div>

            <!-- Mini Live Preview Bar at Bottom of Canvas -->
            <div id="hw-preview-bar" class="absolute bottom-3 left-3 right-3 z-20 bg-white/95 backdrop-blur-md border border-slate-200/80 rounded-2xl px-3.5 py-2 shadow-lg flex items-center justify-between gap-3 transition-all">
                <div class="flex items-center gap-2 overflow-hidden flex-1">
                    <span class="px-2 py-0.5 rounded-lg bg-orange-100 text-orange-700 font-extrabold text-[10px] uppercase tracking-wide flex-shrink-0 flex items-center gap-1">
                        <i class="fa fa-keyboard text-[9px]"></i> Ketikan
                    </span>
                    <span id="hw-preview-text" class="text-xs text-slate-700 font-semibold truncate italic">Belum ada teks ketikan...</span>
                </div>
                <div class="flex items-center gap-1.5 flex-shrink-0">
                    <button onclick="toggleOcrPanel()" class="text-[11px] font-bold px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors" title="Lihat detail pengenalan">
                        <i class="fa fa-wand-magic-sparkles text-violet-500"></i> Detail
                    </button>
                    <button onclick="switchTab('type')" class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-orange-500 hover:bg-orange-600 text-white transition-colors">
                        Buka Ketik &rarr;
                    </button>
                </div>
            </div>
        </div>

        <!-- OCR Result & Candidates Panel (expandable) -->
        <div id="ocr-panel" class="bg-white border-t border-violet-100 p-3.5 flex-shrink-0 max-h-48 overflow-y-auto hidden z-20">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <i class="fa fa-wand-magic-sparkles text-violet-500"></i>
                    <span class="text-xs font-bold text-slate-800">Hasil Pengenalan Tulisan Terakhir</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <button onclick="appendCurrentOcrToTyped()" class="text-xs px-2.5 py-1 bg-orange-100 text-orange-700 font-bold rounded-lg hover:bg-orange-200 transition-colors flex items-center gap-1">
                        <i class="fa fa-arrow-down text-[10px]"></i> Masukkan ke Ketikan
                    </button>
                    <button onclick="toggleOcrPanel()" class="text-xs px-2 py-1 bg-slate-100 text-slate-500 rounded-lg hover:bg-slate-200">
                        <i class="fa fa-xmark"></i>
                    </button>
                </div>
            </div>
            <div id="ocr-result" contenteditable="true" spellcheck="true" class="min-h-10 text-xs"></div>
            <!-- Candidates Chips -->
            <div id="ocr-candidates-container" class="mt-2 hidden">
                <div class="text-[11px] text-slate-400 font-semibold mb-1">Klik untuk mengganti kata jika ada yang kurang pas:</div>
                <div id="ocr-candidates-list" class="flex flex-wrap gap-1.5"></div>
            </div>
        </div>
    </div>

    <!-- ===== PANEL KANAN: Ketik (Desktop only, visible always as sidebar) ===== -->
    <div id="panel-type" class="hidden lg:flex flex-col w-80 xl:w-96 bg-white border-l border-slate-200 overflow-hidden flex-shrink-0">
        <div class="px-4 py-2.5 border-b border-slate-100 bg-orange-50 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fa fa-keyboard text-orange-500 text-sm"></i>
                <span class="text-xs font-bold text-orange-700 uppercase tracking-wider">Area Ketik</span>
            </div>
            <span class="text-[10px] text-slate-400 font-medium">Sinkron otomatis</span>
        </div>
        <textarea id="typed-area" placeholder="Ketik catatan Anda di sini...&#10;&#10;Tulisan tangan yang Anda buat di kanvas akan langsung muncul di sini secara otomatis." oninput="syncFromDesktop()"><?= htmlspecialchars($note->typed_text ?? '') ?></textarea>
    </div>
</div>

<!-- Mobile Ketik Tab Panel (full screen, active when tab 'Ketik' is chosen) -->
<div id="panel-type-mobile" class="hidden flex-col absolute inset-0 top-[53px] bg-white z-20">
    <div class="px-4 py-2.5 border-b border-slate-100 bg-orange-50 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fa fa-keyboard text-orange-500 text-sm"></i>
            <span class="text-xs font-bold text-orange-700 uppercase tracking-wider">Area Ketik</span>
        </div>
        <button onclick="switchTab('write')" class="text-xs font-bold px-2.5 py-1 rounded-lg bg-orange-100 text-orange-700">
            <i class="fa fa-pen-nib mr-1"></i> Kembali ke Kanvas
        </button>
    </div>
    <textarea id="typed-area-mobile" placeholder="Ketik catatan Anda di sini... Tulisan tangan otomatis masuk ke sini." class="flex-1 p-4 outline-none border-none text-slate-800 text-sm leading-relaxed resize-none bg-[#fef9f0]" oninput="syncFromMobile()"><?= htmlspecialchars($note->typed_text ?? '') ?></textarea>
</div>

<!-- Settings Modal for Auto Recognition -->
<div id="settings-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-5 shadow-2xl border border-slate-100 animate-in fade-in zoom-in duration-200">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold">
                    <i class="fa fa-wand-magic-sparkles text-sm"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-sm">Pengaturan Konversi Otomatis</h3>
                    <p class="text-[11px] text-slate-400">Sesuaikan cara kerja pengenalan tulisan</p>
                </div>
            </div>
            <button onclick="toggleSettingsModal()" class="w-7 h-7 rounded-full bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center text-xs">
                <i class="fa fa-xmark"></i>
            </button>
        </div>

        <div class="space-y-4 text-xs">
            <!-- Scribble Mode -->
            <label class="flex items-start gap-3 p-3 bg-slate-50 hover:bg-slate-100 rounded-2xl cursor-pointer border border-slate-200/80 transition-all">
                <input type="checkbox" id="cfg-scribble" class="mt-0.5 rounded text-orange-500 focus:ring-orange-400" onchange="saveConfig()">
                <div>
                    <span class="font-bold text-slate-800 block text-xs">Mode Scribble (Pena Ajaib)</span>
                    <span class="text-[11px] text-slate-500 leading-normal block mt-0.5">
                        Otomatis membersihkan kanvas setelah tulisan berhasil diubah menjadi ketikan. Sangat nyaman agar kanvas tidak penuh di HP!
                    </span>
                </div>
            </label>

            <!-- Delay Time -->
            <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200/80">
                <label class="font-bold text-slate-800 block mb-1">Jeda Waktu Selesai Menulis</label>
                <p class="text-[11px] text-slate-500 mb-2">Berapa lama sistem menunggu setelah pena/jari diangkat sebelum mengubah ke teks:</p>
                <select id="cfg-delay" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700" onchange="saveConfig()">
                    <option value="900">0.9 Detik (Cepat / Responsif)</option>
                    <option value="1300">1.3 Detik (Normal — Disarankan)</option>
                    <option value="1800">1.8 Detik (Santai)</option>
                </select>
            </div>

            <!-- Auto-space -->
            <label class="flex items-center gap-3 p-3 bg-slate-50 hover:bg-slate-100 rounded-2xl cursor-pointer border border-slate-200/80 transition-all">
                <input type="checkbox" id="cfg-autospace" checked class="rounded text-orange-500 focus:ring-orange-400" onchange="saveConfig()">
                <span class="font-bold text-slate-800 text-xs">Pisahkan kata dengan spasi otomatis</span>
            </label>
        </div>

        <div class="mt-5">
            <button onclick="toggleSettingsModal()" class="w-full py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold rounded-xl text-xs shadow-md shadow-orange-500/20 hover:from-orange-600 hover:to-orange-700">
                Selesai & Terapkan
            </button>
        </div>
    </div>
</div>

<script>
const NOTE_ID       = <?= (int)$note->id ?>;
const SAVE_URL      = '<?= site_url('notes/save') ?>';
const RECOGNIZE_URL = '<?= site_url('notes/recognize') ?>';
const INIT_CANVAS   = <?= $note->canvas_data  ? $note->canvas_data  : 'null' ?>;
const INIT_OCR_TEXT = <?= $note->ocr_text     ? json_encode($note->ocr_text)  : '""' ?>;

// ===== Settings & State =====
let autoConvertEnabled = localStorage.getItem('coretan_auto_convert') !== 'false'; // Default: ON!
let scribbleMode       = localStorage.getItem('coretan_scribble_mode') !== 'false'; // Default: ON!
let autoDelay          = parseInt(localStorage.getItem('coretan_auto_delay') || '1300'); // 1.3s
let autoSpace          = localStorage.getItem('coretan_auto_space') !== 'false';

let inkStrokes         = [];
let currentStroke      = { x: [], y: [], t: [] };
let strokeSessionStart = 0;
let isDrawingStroke    = false;
let autoConvertTimer   = null;
let lastInsertedText   = '';
let currentTool        = 'pen';
let isConverting       = false;

// Sync config inputs
document.getElementById('cfg-scribble').checked = scribbleMode;
document.getElementById('cfg-delay').value = autoDelay.toString();
document.getElementById('cfg-autospace').checked = autoSpace;

function updateAutoButtonUI() {
    const btn = document.getElementById('btn-auto-toggle');
    const dot = document.getElementById('auto-dot');
    const label = document.getElementById('auto-btn-label');

    if (autoConvertEnabled) {
        btn.className = 'flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-bold transition-all shadow-sm bg-emerald-500 text-white shadow-emerald-500/20 hover:bg-emerald-600 active:scale-95';
        dot.className = 'w-2 h-2 rounded-full bg-white animate-pulse';
        label.textContent = 'Auto: ON';
        btn.title = 'Mode Otomatis AKTIF: Menulis tangan akan langsung otomatis diubah ke ketikan';
    } else {
        btn.className = 'flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-bold transition-all shadow-sm bg-slate-200 text-slate-600 hover:bg-slate-300 active:scale-95';
        dot.className = 'w-2 h-2 rounded-full bg-slate-400';
        label.textContent = 'Auto: OFF';
        btn.title = 'Mode Otomatis MATI: Klik untuk mengaktifkan pengenalan otomatis';
    }
}
updateAutoButtonUI();

function toggleAutoConvert() {
    autoConvertEnabled = !autoConvertEnabled;
    localStorage.setItem('coretan_auto_convert', autoConvertEnabled);
    updateAutoButtonUI();

    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: autoConvertEnabled ? 'success' : 'info',
        title: autoConvertEnabled ? '⚡ Mode Otomatis AKTIF! Tulis tangan langsung jadi ketikan.' : '⏸️ Mode Otomatis MATI.',
        showConfirmButton: false,
        timer: 2000,
        customClass: { popup: 'rounded-2xl' }
    });
}

function toggleSettingsModal() {
    const modal = document.getElementById('settings-modal');
    modal.classList.toggle('hidden');
}

function saveConfig() {
    scribbleMode = document.getElementById('cfg-scribble').checked;
    autoDelay    = parseInt(document.getElementById('cfg-delay').value);
    autoSpace    = document.getElementById('cfg-autospace').checked;

    localStorage.setItem('coretan_scribble_mode', scribbleMode);
    localStorage.setItem('coretan_auto_delay', autoDelay);
    localStorage.setItem('coretan_auto_space', autoSpace);
}

// ===== Fabric.js canvas for handwriting =====
const hwWrapper = document.getElementById('hw-wrapper');
const hwCanvas  = new fabric.Canvas('canvas-hw', {
    isDrawingMode: true,
    backgroundColor: 'transparent',
});

function resizeHw() {
    hwCanvas.setWidth(hwWrapper.clientWidth);
    hwCanvas.setHeight(hwWrapper.clientHeight);
    hwCanvas.renderAll();
}
resizeHw();
window.addEventListener('resize', resizeHw);

if (INIT_CANVAS) hwCanvas.loadFromJSON(INIT_CANVAS, () => hwCanvas.renderAll());
if (INIT_OCR_TEXT) {
    document.getElementById('ocr-result').textContent = INIT_OCR_TEXT;
}
updatePreviewBar(document.getElementById('typed-area').value);

// History
let hwHistory = [], hwRedo = [], hwBusy = false;
hwCanvas.on('object:added', () => {
    if (!hwBusy) {
        hwHistory.push(JSON.stringify(hwCanvas));
        hwRedo = [];
        markDirty();
    }
});

function hwUndo() {
    if (!hwHistory.length) return;
    hwBusy = true;
    hwRedo.push(JSON.stringify(hwCanvas));
    hwCanvas.loadFromJSON(hwHistory.pop(), () => {
        hwCanvas.renderAll();
        hwBusy = false;
        inkStrokes = [];
        markDirty();
    });
}

let hwColor = '#1a1a2e', hwSize = 3;
hwCanvas.freeDrawingBrush.color = hwColor;
hwCanvas.freeDrawingBrush.width = hwSize;

let isHwErasing = false;
let hwErasedSet = new Set();

function setHwTool(t) {
    currentTool = t;
    document.querySelectorAll('.tool-btn[id^="hw-"]').forEach(b => b.classList.remove('active'));
    document.getElementById('hw-' + t)?.classList.add('active');
    if (t === 'eraser') {
        hwCanvas.isDrawingMode = false;
        hwCanvas.selection = false;
        hwCanvas.defaultCursor = 'crosshair';
    } else {
        hwCanvas.isDrawingMode = true;
        hwCanvas.selection = false;
        hwCanvas.defaultCursor = 'crosshair';
        hwCanvas.freeDrawingBrush = new fabric.PencilBrush(hwCanvas);
        hwCanvas.freeDrawingBrush.color = hwColor;
        hwCanvas.freeDrawingBrush.width = hwSize;
    }
}

function setHwColor(c, el) {
    hwColor = c;
    if (currentTool !== 'eraser') {
        hwCanvas.freeDrawingBrush.color = c;
    }
    document.querySelectorAll('.hw-color').forEach(d => d.classList.remove('border-orange-400','scale-110'));
    if (el) { el.classList.add('border-orange-400','scale-110'); }
}

function setHwSize(v) {
    hwSize = parseInt(v);
    if (currentTool !== 'eraser') {
        hwCanvas.freeDrawingBrush.width = hwSize;
    }
    document.getElementById('hw-size-label').textContent = v;
}

function clearHw(triggerDirty = true) {
    hwCanvas.clear();
    hwCanvas.setBackgroundColor('transparent', hwCanvas.renderAll.bind(hwCanvas));
    inkStrokes = [];
    currentStroke = { x: [], y: [], t: [] };
    clearTimeout(autoConvertTimer);
    hideLiveIndicator();
    if (triggerDirty) markDirty();
}

function eraseHwAtPointer(e) {
    const ptr = hwCanvas.getPointer(e);
    const radius = Math.max(14, hwSize * 3);
    const objects = hwCanvas.getObjects();
    let removedAny = false;

    for (let i = objects.length - 1; i >= 0; i--) {
        const obj = objects[i];
        if (!obj || hwErasedSet.has(obj)) continue;

        const b = obj.getBoundingRect(true);
        if (ptr.x < b.left - radius || ptr.x > b.left + b.width + radius ||
            ptr.y < b.top - radius || ptr.y > b.top + b.height + radius) {
            continue;
        }

        let hit = obj.containsPoint(ptr);
        if (!hit && obj.type === 'path' && obj.path) {
            const matrix = obj.calcTransformMatrix();
            const inverted = fabric.util.invertTransform(matrix);
            const localPoint = fabric.util.transformPoint(ptr, inverted);

            for (let j = 0; j < obj.path.length; j++) {
                const cmd = obj.path[j];
                for (let k = 1; k < cmd.length; k += 2) {
                    const px = cmd[k];
                    const py = cmd[k + 1];
                    if (px !== undefined && py !== undefined) {
                        if (Math.hypot(localPoint.x - px, localPoint.y - py) <= radius) {
                            hit = true;
                            break;
                        }
                    }
                }
                if (hit) break;
            }
        }

        if (hit) {
            hwErasedSet.add(obj);
            hwCanvas.remove(obj);
            removedAny = true;
        }
    }

    if (removedAny) {
        hwCanvas.requestRenderAll();
        markDirty();
    }
}

// ===== Live Stroke Tracking for Handwriting AI =====
hwCanvas.on('mouse:down', function(opt) {
    if (currentTool === 'eraser') {
        isHwErasing = true;
        hwErasedSet.clear();
        eraseHwAtPointer(opt.e);
        return;
    }

    if (!hwCanvas.isDrawingMode) return;
    isDrawingStroke = true;
    if (inkStrokes.length === 0) strokeSessionStart = Date.now();
    const ptr = hwCanvas.getPointer(opt.e);
    currentStroke = {
        x: [Math.round(ptr.x)],
        y: [Math.round(ptr.y)],
        t: [Date.now() - strokeSessionStart]
    };
    clearTimeout(autoConvertTimer);
    hideLiveIndicator();
});

hwCanvas.on('mouse:move', function(opt) {
    if (currentTool === 'eraser') {
        if (isHwErasing) eraseHwAtPointer(opt.e);
        return;
    }

    if (!isDrawingStroke) return;
    const ptr = hwCanvas.getPointer(opt.e);
    const px = Math.round(ptr.x);
    const py = Math.round(ptr.y);
    const lastX = currentStroke.x[currentStroke.x.length - 1];
    const lastY = currentStroke.y[currentStroke.y.length - 1];
    if (lastX === px && lastY === py) return;
    currentStroke.x.push(px);
    currentStroke.y.push(py);
    currentStroke.t.push(Date.now() - strokeSessionStart);
});

hwCanvas.on('mouse:up', function(opt) {
    if (currentTool === 'eraser') {
        if (isHwErasing && hwErasedSet.size > 0) {
            hwHistory.push(JSON.stringify(hwCanvas));
            hwRedo = [];
            markDirty();
        }
        isHwErasing = false;
        hwErasedSet.clear();
        return;
    }

    if (!isDrawingStroke) return;
    isDrawingStroke = false;
    if (currentStroke.x.length > 0) {
        inkStrokes.push([currentStroke.x, currentStroke.y, currentStroke.t]);
    }

    if (autoConvertEnabled && currentTool !== 'eraser' && inkStrokes.length > 0) {
        showLiveIndicator('waiting', 'Menunggu jeda menulis...', 'fa-pen text-amber-400');
        clearTimeout(autoConvertTimer);
        autoConvertTimer = setTimeout(() => {
            executeRecognition(true);
        }, autoDelay);
    }
});

// ===== Floating Live Indicator Helpers =====
function showLiveIndicator(state, message, iconClass = 'fa-bolt text-amber-400') {
    const ind = document.getElementById('live-indicator');
    const icon = document.getElementById('live-indicator-icon');
    const text = document.getElementById('live-indicator-text');

    icon.className = 'fa ' + iconClass;
    text.textContent = message;

    ind.classList.remove('opacity-0', '-translate-y-2');
    ind.classList.add('opacity-100', 'translate-y-0');
}

function hideLiveIndicator() {
    const ind = document.getElementById('live-indicator');
    ind.classList.remove('opacity-100', 'translate-y-0');
    ind.classList.add('opacity-0', '-translate-y-2');
}

function updatePreviewBar(text) {
    const el = document.getElementById('hw-preview-text');
    if (!text || !text.trim()) {
        el.textContent = 'Belum ada teks ketikan...';
        el.className = 'text-xs text-slate-400 font-normal truncate italic';
    } else {
        el.textContent = text;
        el.className = 'text-xs text-slate-800 font-bold truncate';
    }
}

function toggleOcrPanel() {
    const panel = document.getElementById('ocr-panel');
    panel.classList.toggle('hidden');
}

// ===== Execution of Recognition (Google AI + Tesseract Fallback) =====
async function executeRecognition(isAuto = false) {
    if (isConverting) return;
    if (inkStrokes.length === 0 && hwCanvas.getObjects().length === 0) {
        if (!isAuto) {
            Swal.fire({ icon:'warning', title:'Kanvas kosong', text:'Tulis sesuatu dulu sebelum mengenali tulisan!', customClass:{popup:'rounded-3xl'} });
        }
        hideLiveIndicator();
        return;
    }

    isConverting = true;
    showLiveIndicator('converting', '⚡ Mengubah ke teks ketikan...', 'fa-circle-notch fa-spin text-emerald-400');

    const lang = document.getElementById('ocr-lang').value;
    let recognizedText = '';
    let candidates = [];

    // 1. Coba Google Handwriting API (Super Cepat & Akurat)
    if (inkStrokes.length > 0) {
        try {
            const res = await fetch(RECOGNIZE_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    ink: inkStrokes,
                    width: Math.round(hwCanvas.getWidth()),
                    height: Math.round(hwCanvas.getHeight()),
                    lang: lang
                })
            });
            const data = await res.json();
            if (data.success && data.text) {
                recognizedText = data.text.trim();
                candidates = data.candidates || [];
            }
        } catch (err) {
            console.warn('Backend recognize error:', err);
        }
    }

    // 2. Fallback ke Tesseract OCR jika Google API gagal atau tidak terhubung
    if (!recognizedText && hwCanvas.getObjects().length > 0) {
        try {
            showLiveIndicator('converting', 'Membaca via OCR...', 'fa-brain fa-spin text-violet-400');
            const imgData = getOcrReadyImageData();
            const ocrLang = lang === 'ind' ? 'ind' : 'eng';
            const result = await Tesseract.recognize(imgData, ocrLang);
            recognizedText = (result.data.text || '').trim();
        } catch (e) {
            console.warn('Tesseract fallback error:', e);
        }
    }

    isConverting = false;

    if (recognizedText) {
        // Terapkan hasil teks ke area ketikan
        insertTextToNote(recognizedText);
        showLiveIndicator('success', `✓ Terbaca: "${recognizedText}"`, 'fa-check text-emerald-400');
        renderCandidateChips(recognizedText, candidates);
        setTimeout(hideLiveIndicator, 2500);

        // Jika mode Scribble (Pena Ajaib) aktif: bersihkan kanvas agar langsung siap untuk kata berikutnya
        if (scribbleMode) {
            setTimeout(() => {
                hwCanvas.clear();
                hwCanvas.setBackgroundColor('transparent', hwCanvas.renderAll.bind(hwCanvas));
                inkStrokes = [];
                markDirty();
            }, 300);
        } else {
            // Jika mode bukan scribble, inkStrokes dikosongkan agar kata sebelumnya tidak terkirim ganda
            inkStrokes = [];
        }
    } else {
        if (!isAuto) {
            Swal.fire({
                icon: 'info',
                title: 'Belum Terbaca',
                text: 'Tulisan tangan belum dapat dikenali dengan jelas. Coba tulis lebih tebal atau jelas.',
                customClass: { popup: 'rounded-3xl' }
            });
        }
        hideLiveIndicator();
    }
}

// Generate solid white background image for Tesseract OCR
function getOcrReadyImageData() {
    const offCanvas = document.createElement('canvas');
    const w = hwCanvas.getWidth();
    const h = hwCanvas.getHeight();
    offCanvas.width = w * 2;
    offCanvas.height = h * 2;
    const ctx = offCanvas.getContext('2d');

    // Fill white
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, offCanvas.width, offCanvas.height);

    // Draw fabric canvas
    ctx.drawImage(hwCanvas.getElement(), 0, 0, offCanvas.width, offCanvas.height);
    return offCanvas.toDataURL('image/png');
}

// Insert recognized text into typed textarea
function insertTextToNote(text) {
    lastInsertedText = text;
    const typedArea = document.getElementById('typed-area');
    const typedAreaMobile = document.getElementById('typed-area-mobile');

    let current = typedArea.value;
    if (autoSpace && current.length > 0 && !current.endsWith(' ') && !current.endsWith('\n')) {
        current += ' ';
    }
    current += text;

    typedArea.value = current;
    if (typedAreaMobile) typedAreaMobile.value = current;

    updatePreviewBar(current);
    markDirty();

    // Auto-save after successful conversion
    saveNote(false);
}

// Render candidate chips in OCR panel
function renderCandidateChips(bestText, candidates) {
    const ocrResult = document.getElementById('ocr-result');
    ocrResult.textContent = bestText;

    const candContainer = document.getElementById('ocr-candidates-container');
    const candList = document.getElementById('ocr-candidates-list');

    if (candidates && candidates.length > 1) {
        candList.innerHTML = '';
        candidates.forEach(cand => {
            const chip = document.createElement('button');
            chip.className = 'candidate-chip' + (cand === bestText ? ' ring-2 ring-violet-500' : '');
            chip.textContent = cand;
            chip.onclick = () => replaceLastInserted(cand);
            candList.appendChild(chip);
        });
        candContainer.classList.remove('hidden');
    } else {
        candContainer.classList.add('hidden');
    }
}

// Replace last recognized word with user-selected candidate
function replaceLastInserted(newText) {
    if (!lastInsertedText) return;
    const typedArea = document.getElementById('typed-area');
    const typedAreaMobile = document.getElementById('typed-area-mobile');

    let cur = typedArea.value;
    if (cur.endsWith(lastInsertedText)) {
        cur = cur.slice(0, -lastInsertedText.length) + newText;
        typedArea.value = cur;
        if (typedAreaMobile) typedAreaMobile.value = cur;
        lastInsertedText = newText;
        updatePreviewBar(cur);
        markDirty();

        showLiveIndicator('success', `✓ Diganti jadi: "${newText}"`, 'fa-check text-emerald-400');
        setTimeout(hideLiveIndicator, 2000);
    }
}

function appendCurrentOcrToTyped() {
    const ocrText = document.getElementById('ocr-result').textContent || '';
    if (ocrText.trim()) {
        insertTextToNote(ocrText.trim());
        Swal.fire({ toast:true, position:'top-end', icon:'success', title:'Teks dimasukkan!', showConfirmButton:false, timer:1500, customClass:{popup:'rounded-2xl'} });
    }
}

// Manual OCR button click handler
function runManualOCR() {
    executeRecognition(false);
}

// ===== Tab Switcher (Mobile) =====
let activeTab = 'write';
function switchTab(tab) {
    activeTab = tab;
    document.getElementById('tab-write').classList.toggle('active', tab === 'write');
    document.getElementById('tab-type').classList.toggle('active', tab === 'type');

    const isDesktop = window.innerWidth >= 1024;
    if (isDesktop) return;

    const panelMobile = document.getElementById('panel-type-mobile');
    if (tab === 'type') {
        panelMobile.style.display = 'flex';
        // Auto sync text to mobile textarea
        document.getElementById('typed-area-mobile').value = document.getElementById('typed-area').value;
    } else {
        panelMobile.style.display = 'none';
    }
}

function syncFromMobile() {
    const val = document.getElementById('typed-area-mobile').value;
    document.getElementById('typed-area').value = val;
    updatePreviewBar(val);
    markDirty();
}

function syncFromDesktop() {
    const val = document.getElementById('typed-area').value;
    if (document.getElementById('typed-area-mobile')) {
        document.getElementById('typed-area-mobile').value = val;
    }
    updatePreviewBar(val);
    markDirty();
}

// ===== Auto-save Note =====
let isDirty = false, autoSaveTimer = null;
function markDirty() {
    isDirty = true;
    setSaveStatus('Belum disimpan...', 'text-amber-500');
    clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(() => saveNote(), 15000);
}

function setSaveStatus(text, cls = 'text-slate-400') {
    const el = document.getElementById('save-status');
    if (el) {
        el.className = 'text-xs hidden sm:block ' + cls;
        document.getElementById('save-text').textContent = text;
    }
}

async function saveNote(showToast = false) {
    if (!isDirty && !showToast) return;
    setSaveStatus('Menyimpan...', 'text-orange-500');

    const thumbnail = hwCanvas.getObjects().length > 0
        ? hwCanvas.toDataURL({ format:'png', quality:0.5, multiplier:0.4 })
        : '';
    const typedVal = document.getElementById('typed-area').value;

    const body = new URLSearchParams({
        id:              NOTE_ID,
        title:           document.getElementById('title-input').value,
        typed_text:      typedVal,
        canvas_data:     JSON.stringify(hwCanvas),
        handwriting_img: thumbnail,
        ocr_text:        document.getElementById('ocr-result').textContent || '',
        thumbnail:       thumbnail || '',
    });

    try {
        const res  = await fetch(SAVE_URL, { method:'POST', body });
        const data = await res.json();
        if (data.success) {
            isDirty = false;
            setSaveStatus('Tersimpan ' + data.time, 'text-emerald-500');
            if (showToast) Swal.fire({ toast:true, position:'top-end', icon:'success', title:'Tersimpan!', showConfirmButton:false, timer:2000, customClass:{popup:'rounded-2xl'} });
        }
    } catch(e) {
        setSaveStatus('Gagal simpan', 'text-rose-500');
    }
}

document.addEventListener('keydown', e => {
    if (e.ctrlKey && e.key === 's') { e.preventDefault(); saveNote(true); }
});
window.addEventListener('beforeunload', e => { if (isDirty) { e.preventDefault(); e.returnValue = ''; } });

// Responsive resize
window.addEventListener('resize', () => {
    if (window.innerWidth >= 1024) {
        document.getElementById('panel-type-mobile').style.display = 'none';
    }
});
</script>
</body>
</html>
