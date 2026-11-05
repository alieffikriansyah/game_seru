<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, maximum-scale=1.0, user-scalable=no">
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
    <!-- Fabric.js from CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] } } }
        }
    </script>
    <style>
        * { -webkit-tap-highlight-color: transparent; box-sizing: border-box; }
        html, body { font-family: 'Plus Jakarta Sans', sans-serif; margin: 0; padding: 0; width: 100%; height: 100%; overflow: hidden; }
        #canvas-wrapper canvas { touch-action: none; }
        .tool-btn {
            display: flex; align-items: center; justify-content: center;
            width: 34px; height: 34px; border-radius: 9px;
            border: none; background: transparent; cursor: pointer;
            color: #64748b; transition: all 0.15s ease; font-size: 13px;
        }
        .tool-btn:hover { background: #f1f5f9; color: #1e293b; }
        .tool-btn.active { background: #ede9fe; color: #7c3aed; font-weight: bold; }
        .tool-btn.active-eraser { background: #fee2e2; color: #ef4444; }
        .tool-btn.active-danger:hover { background: #fee2e2; color: #ef4444; }
        .divider-v { width: 1px; height: 22px; background: #e2e8f0; margin: 0 3px; }
        .color-dot { width: 22px; height: 22px; border-radius: 50%; cursor: pointer; border: 2px solid transparent; transition: all 0.15s; flex-shrink: 0; }
        .color-dot.selected, .color-dot:hover { border-color: #7c3aed; transform: scale(1.2); }
        input[type=range] { accent-color: #7c3aed; }
        #title-input { background: transparent; border: none; outline: none; font-weight: 700; }
        #title-input:hover { background: #f8fafc; border-radius: 6px; }
        #title-input:focus { background: white; border-radius: 6px; }
        /* Custom scrollbar for toolbar */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-slate-100 h-screen flex flex-col select-none">

<!-- ========== TIER 1: TOP NAVIGATION & ACTION BAR ========== -->
<header class="bg-white border-b border-slate-200 px-3 py-2 flex items-center justify-between shadow-xs z-20 flex-shrink-0">
    <div class="flex items-center gap-1.5 sm:gap-2 min-w-0">
        <!-- Back to Dashboard -->
        <a href="<?= site_url('whiteboard') ?>" class="tool-btn flex-shrink-0" title="Kembali ke Daftar">
            <i class="fa fa-arrow-left text-xs"></i>
        </a>

        <!-- Title -->
        <div class="flex items-center gap-1 min-w-0">
            <input type="text" id="title-input" value="<?= htmlspecialchars($board->title) ?>"
                class="text-xs sm:text-sm font-bold text-slate-800 px-2 py-1 max-w-[130px] xs:max-w-[180px] sm:max-w-xs md:max-w-sm truncate rounded-lg"
                onchange="markDirty()">
        </div>
    </div>

    <!-- Actions (Undo / Redo / Trash / Save) -->
    <div class="flex items-center gap-1 sm:gap-2 flex-shrink-0">
        <!-- Undo / Redo -->
        <button class="tool-btn" onclick="undo()" title="Undo (Ctrl+Z)"><i class="fa fa-rotate-left text-xs"></i></button>
        <button class="tool-btn" onclick="redo()" title="Redo (Ctrl+Y)"><i class="fa fa-rotate-right text-xs"></i></button>
        <button class="tool-btn active-danger text-rose-500" onclick="clearCanvas()" title="Bersihkan Semua"><i class="fa fa-trash text-xs"></i></button>

        <div class="divider-v hidden md:block"></div>

        <!-- Export PNG -->
        <button class="tool-btn hidden md:flex" onclick="exportPNG()" title="Export PNG"><i class="fa fa-download text-xs"></i></button>

        <!-- Save Status Badge -->
        <div id="save-status" class="text-xs text-slate-400 hidden lg:block mr-1">
            <i class="fa fa-cloud-arrow-up mr-1"></i><span id="save-text">Siap</span>
        </div>

        <!-- Save Button -->
        <button onclick="saveCanvas(true)"
            class="px-3 sm:px-4 py-1.5 sm:py-2 bg-gradient-to-r from-violet-600 to-violet-700 hover:from-violet-500 hover:to-violet-600 text-white font-bold rounded-xl text-xs shadow-md shadow-violet-500/25 transition-all hover:scale-105 active:scale-95 flex items-center gap-1.5 flex-shrink-0">
            <i class="fa fa-floppy-disk text-xs"></i>
            <span class="hidden sm:inline">Simpan</span>
        </button>
    </div>
</header>

<!-- ========== TIER 2: DRAWING TOOLS & PALETTE TOOLBAR ========== -->
<div class="bg-white border-b border-slate-200 px-3 py-1.5 flex items-center justify-between gap-2 overflow-x-auto no-scrollbar flex-shrink-0 shadow-xs z-10">
    <div class="flex items-center gap-1 flex-shrink-0">
        <!-- Pen Tool -->
        <button class="tool-btn active" id="tool-pen" onclick="setTool('pen')" title="Pena (Menggambar bebas)">
            <i class="fa fa-pen-nib"></i>
        </button>

        <!-- Eraser Tool -->
        <button class="tool-btn" id="tool-eraser" onclick="setTool('eraser')" title="Penghapus (Sentuh/geser goresan untuk menghapus)">
            <i class="fa fa-eraser"></i>
        </button>

        <div class="divider-v"></div>

        <!-- Shapes -->
        <button class="tool-btn" id="tool-line" onclick="setTool('line')" title="Garis Lurus">
            <i class="fa fa-minus"></i>
        </button>
        <button class="tool-btn" id="tool-rect" onclick="setTool('rect')" title="Persegi Panjang">
            <i class="fa fa-square"></i>
        </button>
        <button class="tool-btn" id="tool-circle" onclick="setTool('circle')" title="Lingkaran">
            <i class="fa fa-circle"></i>
        </button>

        <div class="divider-v"></div>

        <!-- Text & Select -->
        <button class="tool-btn" id="tool-text" onclick="setTool('text')" title="Teks">
            <i class="fa fa-font"></i>
        </button>
        <button class="tool-btn" id="tool-select" onclick="setTool('select')" title="Pilih & Pindahkan Objek">
            <i class="fa fa-arrow-pointer"></i>
        </button>
    </div>

    <!-- Right side: Colors & Size -->
    <div class="flex items-center gap-2 flex-shrink-0">
        <!-- Palette Colors -->
        <div class="flex items-center gap-1.5">
            <?php
            $colors = ['#1a1a2e','#7c3aed','#f97316','#ef4444','#10b981','#3b82f6','#f59e0b','#ffffff'];
            foreach ($colors as $c): ?>
            <div class="color-dot <?= $c === '#1a1a2e' ? 'selected' : '' ?>"
                style="background:<?= $c ?>;<?= $c === '#ffffff' ? 'border:1.5px solid #cbd5e1;' : '' ?>"
                onclick="setColor('<?= $c ?>', this)" title="<?= $c ?>"></div>
            <?php endforeach; ?>
            <input type="color" id="custom-color" value="#1a1a2e" oninput="setColor(this.value)"
                class="w-6 h-6 rounded-full border border-slate-300 cursor-pointer p-0 overflow-hidden flex-shrink-0" title="Pilih Warna Kustom">
        </div>

        <div class="divider-v"></div>

        <!-- Brush Size -->
        <div class="flex items-center gap-1.5">
            <i class="fa fa-paintbrush text-slate-400 text-xs"></i>
            <input type="range" id="brush-size" min="1" max="40" value="4" class="w-16 sm:w-20" oninput="setBrushSize(this.value)">
            <span id="brush-label" class="text-xs text-slate-600 font-bold w-4 text-center">4</span>
        </div>
    </div>
</div>

<!-- ========== CANVAS WRAPPER ========== -->
<div id="canvas-wrapper" class="flex-1 overflow-hidden relative bg-white m-1.5 sm:m-3 rounded-xl sm:rounded-2xl shadow-inner border border-slate-200">
    <canvas id="whiteboard-canvas"></canvas>

    <!-- Eraser floating hint / cursor indicator -->
    <div id="eraser-indicator" class="hidden absolute pointer-events-none rounded-full border-2 border-rose-500 bg-rose-500/20 -translate-x-1/2 -translate-y-1/2 z-30 transition-none"></div>

    <!-- Active tool toast / hint -->
    <div id="tool-hint" class="absolute bottom-3 left-1/2 -translate-x-1/2 bg-slate-900/80 text-white text-xs px-3.5 py-1.5 rounded-full shadow-lg backdrop-blur-md pointer-events-none transition-all opacity-0 transform translate-y-2 z-20">
        <span id="tool-hint-text">Mode Penghapus Aktif: Sentuh garis untuk menghapus</span>
    </div>
</div>

<script>
const BOARD_ID   = <?= (int)$board->id ?>;
const SAVE_URL   = '<?= site_url('whiteboard/save') ?>';
const INIT_DATA  = <?= $board->canvas_data ? $board->canvas_data : 'null' ?>;

// ===== Init Fabric.js Canvas =====
const wrapper = document.getElementById('canvas-wrapper');
const canvas  = new fabric.Canvas('whiteboard-canvas', {
    isDrawingMode: true,
    selection: false,
    backgroundColor: '#ffffff',
});

function resizeCanvas() {
    if (!wrapper) return;
    const w = wrapper.clientWidth;
    const h = wrapper.clientHeight;
    if (w > 0 && h > 0) {
        canvas.setWidth(w);
        canvas.setHeight(h);
        canvas.renderAll();
    }
}
resizeCanvas();
window.addEventListener('resize', resizeCanvas);
window.addEventListener('orientationchange', () => setTimeout(resizeCanvas, 200));

// Load saved data
if (INIT_DATA) {
    canvas.loadFromJSON(INIT_DATA, () => canvas.renderAll());
}

// ===== History (Undo / Redo) =====
let history = [], redoStack = [], isHistoryAction = false;
function pushHistory() {
    if (isHistoryAction) return;
    history.push(JSON.stringify(canvas));
    if (history.length > 40) history.shift();
    redoStack = [];
}
canvas.on('object:added', pushHistory);
canvas.on('object:modified', pushHistory);
canvas.on('object:removed', pushHistory);

function undo() {
    if (history.length === 0) return;
    isHistoryAction = true;
    redoStack.push(JSON.stringify(canvas));
    const prev = history.pop();
    canvas.loadFromJSON(prev, () => {
        canvas.renderAll();
        isHistoryAction = false;
        markDirty();
    });
}

function redo() {
    if (redoStack.length === 0) return;
    isHistoryAction = true;
    history.push(JSON.stringify(canvas));
    const next = redoStack.pop();
    canvas.loadFromJSON(next, () => {
        canvas.renderAll();
        isHistoryAction = false;
        markDirty();
    });
}

document.addEventListener('keydown', e => {
    if (e.ctrlKey && e.key === 'z') { e.preventDefault(); undo(); }
    if (e.ctrlKey && e.key === 'y') { e.preventDefault(); redo(); }
});

// ===== Tools & State =====
let currentTool = 'pen', currentColor = '#1a1a2e', currentSize = 4;
let shapeStart = null, activeShape = null;

canvas.freeDrawingBrush.color = currentColor;
canvas.freeDrawingBrush.width = currentSize;

// Eraser state
let isErasing = false;
let erasedObjectsThisDrag = new Set();
const eraserIndicator = document.getElementById('eraser-indicator');

function showHint(text) {
    const hint = document.getElementById('tool-hint');
    const hintTxt = document.getElementById('tool-hint-text');
    hintTxt.textContent = text;
    hint.classList.remove('opacity-0', 'translate-y-2');
    hint.classList.add('opacity-100', 'translate-y-0');
    clearTimeout(hint._timer);
    hint._timer = setTimeout(() => {
        hint.classList.remove('opacity-100', 'translate-y-0');
        hint.classList.add('opacity-0', 'translate-y-2');
    }, 2000);
}

function setTool(tool) {
    currentTool = tool;

    // Update active button styles
    document.querySelectorAll('.tool-btn[id^="tool-"]').forEach(b => {
        b.classList.remove('active', 'active-eraser');
    });

    const btn = document.getElementById('tool-' + tool);
    if (btn) {
        if (tool === 'eraser') {
            btn.classList.add('active-eraser');
            showHint('🧹 Mode Penghapus: Usap atau klik garis/objek untuk menghapus');
        } else {
            btn.classList.add('active');
        }
    }

    // Configure canvas modes
    if (tool === 'pen') {
        canvas.isDrawingMode = true;
        canvas.selection = false;
        canvas.defaultCursor = 'crosshair';
        canvas.freeDrawingBrush = new fabric.PencilBrush(canvas);
        canvas.freeDrawingBrush.color = currentColor;
        canvas.freeDrawingBrush.width = currentSize;
    } else if (tool === 'eraser') {
        canvas.isDrawingMode = false;
        canvas.selection = false;
        canvas.defaultCursor = 'crosshair';
    } else if (tool === 'select') {
        canvas.isDrawingMode = false;
        canvas.selection = true;
        canvas.defaultCursor = 'default';
    } else {
        // Shapes or Text
        canvas.isDrawingMode = false;
        canvas.selection = false;
        canvas.defaultCursor = 'crosshair';
    }

    if (tool === 'text') {
        canvas.on('mouse:up', addTextHandler);
    } else {
        canvas.off('mouse:up', addTextHandler);
    }
}

// Text handler
function addTextHandler(opt) {
    if (currentTool !== 'text') return;
    const pointer = canvas.getPointer(opt.e);
    const text = new fabric.IText('Ketik teks di sini', {
        left: pointer.x,
        top: pointer.y,
        fontFamily: 'Plus Jakarta Sans, sans-serif',
        fontSize: Math.max(16, currentSize * 4),
        fill: currentColor,
        editable: true,
    });
    canvas.add(text);
    canvas.setActiveObject(text);
    text.enterEditing();
    markDirty();
    setTool('select');
}

// ===== SMART STROKE / OBJECT ERASER IMPLEMENTATION =====
function getEraserRadius() {
    return Math.max(14, currentSize * 3);
}

function eraseAtPointer(e) {
    const pointer = canvas.getPointer(e);
    const radius = getEraserRadius();
    const objects = canvas.getObjects();
    let removedAny = false;

    for (let i = objects.length - 1; i >= 0; i--) {
        const obj = objects[i];
        if (!obj || erasedObjectsThisDrag.has(obj)) continue;

        if (isPointerHittingObject(obj, pointer, radius)) {
            erasedObjectsThisDrag.add(obj);
            canvas.remove(obj);
            removedAny = true;
        }
    }

    if (removedAny) {
        canvas.requestRenderAll();
        markDirty();
    }
}

function isPointerHittingObject(obj, ptr, radius) {
    // 1. Check bounding box with radius padding
    const b = obj.getBoundingRect(true);
    if (ptr.x < b.left - radius || ptr.x > b.left + b.width + radius ||
        ptr.y < b.top - radius || ptr.y > b.top + b.height + radius) {
        return false;
    }

    // 2. Direct hit test if inside object
    if (obj.containsPoint(ptr)) return true;

    // 3. For Freehand Path (Pencil strokes)
    if (obj.type === 'path' && obj.path) {
        const matrix = obj.calcTransformMatrix();
        const inverted = fabric.util.invertTransform(matrix);
        const localPoint = fabric.util.transformPoint(ptr, inverted);

        for (let i = 0; i < obj.path.length; i++) {
            const cmd = obj.path[i];
            for (let j = 1; j < cmd.length; j += 2) {
                const px = cmd[j];
                const py = cmd[j + 1];
                if (px !== undefined && py !== undefined) {
                    if (Math.hypot(localPoint.x - px, localPoint.y - py) <= radius) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    // 4. For Line objects
    if (obj.type === 'line') {
        const x1 = obj.x1, y1 = obj.y1, x2 = obj.x2, y2 = obj.y2;
        const A = ptr.x - x1, B = ptr.y - y1, C = x2 - x1, D = y2 - y1;
        const dot = A * C + B * D;
        const lenSq = C * C + D * D;
        let param = -1;
        if (lenSq !== 0) param = dot / lenSq;
        let xx, yy;
        if (param < 0) { xx = x1; yy = y1; }
        else if (param > 1) { xx = x2; yy = y2; }
        else { xx = x1 + param * C; yy = y1 + param * D; }
        return Math.hypot(ptr.x - xx, ptr.y - yy) <= radius;
    }

    return true;
}

// Canvas Mouse / Touch Handlers for Eraser & Shapes
canvas.on('mouse:down', function(opt) {
    if (currentTool === 'eraser') {
        isErasing = true;
        erasedObjectsThisDrag.clear();
        eraseAtPointer(opt.e);
        return;
    }

    if (['line','rect','circle'].includes(currentTool)) {
        shapeStart = canvas.getPointer(opt.e);
    }
});

canvas.on('mouse:move', function(opt) {
    const ptr = canvas.getPointer(opt.e);

    // Eraser cursor indicator
    if (currentTool === 'eraser') {
        const r = getEraserRadius();
        eraserIndicator.style.width = (r * 2) + 'px';
        eraserIndicator.style.height = (r * 2) + 'px';
        eraserIndicator.style.left = (ptr.x + wrapper.offsetLeft) + 'px';
        eraserIndicator.style.top = (ptr.y + wrapper.offsetTop) + 'px';
        eraserIndicator.classList.remove('hidden');

        if (isErasing) {
            eraseAtPointer(opt.e);
        }
        return;
    } else {
        eraserIndicator.classList.add('hidden');
    }

    // Shapes drawing preview
    if (!shapeStart) return;
    const p = ptr;
    if (activeShape) canvas.remove(activeShape);

    const opts = { stroke: currentColor, strokeWidth: currentSize, fill: 'transparent', selectable: false };
    if (currentTool === 'line') {
        activeShape = new fabric.Line([shapeStart.x, shapeStart.y, p.x, p.y], opts);
    } else if (currentTool === 'rect') {
        activeShape = new fabric.Rect({
            left: Math.min(shapeStart.x, p.x),
            top: Math.min(shapeStart.y, p.y),
            width: Math.abs(p.x - shapeStart.x),
            height: Math.abs(p.y - shapeStart.y),
            ...opts
        });
    } else if (currentTool === 'circle') {
        const r = Math.hypot(p.x - shapeStart.x, p.y - shapeStart.y) / 2;
        activeShape = new fabric.Circle({
            left: Math.min(shapeStart.x, p.x),
            top: Math.min(shapeStart.y, p.y),
            radius: r,
            ...opts
        });
    }

    if (activeShape) {
        canvas.add(activeShape);
        canvas.renderAll();
    }
});

canvas.on('mouse:up', function() {
    if (currentTool === 'eraser') {
        if (isErasing && erasedObjectsThisDrag.size > 0) {
            pushHistory();
        }
        isErasing = false;
        erasedObjectsThisDrag.clear();
        return;
    }

    if (activeShape) {
        activeShape.set('selectable', true);
        markDirty();
    }
    shapeStart = null;
    activeShape = null;
});

// Color picker
function setColor(color, el) {
    currentColor = color;
    if (currentTool === 'pen') {
        canvas.freeDrawingBrush.color = color;
    }
    document.querySelectorAll('.color-dot').forEach(d => d.classList.remove('selected'));
    if (el) el.classList.add('selected');

    // Update active selected object if any
    const active = canvas.getActiveObject();
    if (active) {
        if (active.type === 'i-text') {
            active.set('fill', color);
        } else {
            active.set('stroke', color);
        }
        canvas.renderAll();
        markDirty();
    }
}

// Brush Size
function setBrushSize(val) {
    currentSize = parseInt(val);
    if (currentTool === 'pen') {
        canvas.freeDrawingBrush.width = currentSize;
    }
    document.getElementById('brush-label').textContent = val;
}

// Clear Canvas
function clearCanvas() {
    Swal.fire({
        title: 'Bersihkan kanvas?',
        text: 'Semua coretan dan gambar akan dihapus dari papan tulis.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Bersihkan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#94a3b8',
        customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl', cancelButton: 'rounded-xl' }
    }).then(r => {
        if (r.isConfirmed) {
            canvas.clear();
            canvas.setBackgroundColor('#ffffff', canvas.renderAll.bind(canvas));
            markDirty();
        }
    });
}

// Export PNG
function exportPNG() {
    const link = document.createElement('a');
    link.download = (document.getElementById('title-input').value || 'Papan-Tulis') + '.png';
    link.href = canvas.toDataURL({ format: 'png', multiplier: 2 });
    link.click();
}

// ===== Auto-save Note =====
let isDirty = false, autoSaveTimer = null;
function markDirty() {
    isDirty = true;
    setSaveStatus('Belum disimpan...', 'text-amber-500');
    clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(() => saveCanvas(), 20000);
}

function setSaveStatus(text, cls = 'text-slate-400') {
    const el = document.getElementById('save-status');
    const txt = document.getElementById('save-text');
    if (el && txt) {
        el.className = 'text-xs hidden lg:block ' + cls;
        txt.textContent = text;
    }
}

async function saveCanvas(showToast = false) {
    if (!isDirty && !showToast) return;
    setSaveStatus('Menyimpan...', 'text-violet-500');

    const thumbnail = canvas.toDataURL({ format: 'png', quality: 0.5, multiplier: 0.4 });
    const body = new URLSearchParams({
        id:          BOARD_ID,
        title:       document.getElementById('title-input').value,
        canvas_data: JSON.stringify(canvas),
        thumbnail:   thumbnail,
    });

    try {
        const res  = await fetch(SAVE_URL, { method: 'POST', body });
        const data = await res.json();
        if (data.success) {
            isDirty = false;
            setSaveStatus('Tersimpan ' + data.time, 'text-emerald-500');
            if (showToast) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Papan tulis tersimpan!',
                    showConfirmButton: false,
                    timer: 2000,
                    customClass: { popup: 'rounded-2xl' }
                });
            }
        }
    } catch (e) {
        setSaveStatus('Gagal simpan', 'text-rose-500');
    }
}

// Keyboard shortcut save
document.addEventListener('keydown', e => {
    if (e.ctrlKey && e.key === 's') { e.preventDefault(); saveCanvas(true); }
});

// Warn before leaving
window.addEventListener('beforeunload', e => {
    if (isDirty) { e.preventDefault(); e.returnValue = ''; }
});
</script>
</body>
</html>
