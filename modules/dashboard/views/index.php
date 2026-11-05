<?php require_once APPPATH . 'views/layout/header.php'; ?>

<?php $greeting = (date('H') < 12) ? 'Selamat Pagi' : ((date('H') < 17) ? 'Selamat Siang' : 'Selamat Malam'); ?>

<!-- Greeting Banner -->
<div class="mb-7 bg-gradient-to-br from-[#1a1a2e] to-[#2d1b69] rounded-3xl p-6 sm:p-8 text-white relative overflow-hidden">
    <!-- Decorative bg -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-violet-600/20 rounded-full -translate-y-1/2 translate-x-1/4 pointer-events-none"></div>
    <div class="absolute bottom-0 left-1/3 w-32 h-32 bg-orange-500/10 rounded-full translate-y-1/2 pointer-events-none"></div>
    <div class="relative z-10">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-violet-300 text-sm font-semibold mb-1"><?= $greeting ?>, 👋</p>
                <h2 class="text-2xl sm:text-3xl font-extrabold mb-2"><?= htmlspecialchars(Session::get('name', 'Pengguna')) ?></h2>
                <p class="text-white/50 text-sm">Apa yang ingin Anda buat hari ini?</p>
            </div>
            <div class="flex gap-3">
                <a href="<?= site_url('whiteboard/create') ?>"
                    class="flex items-center gap-2 px-4 py-2.5 bg-white/15 hover:bg-white/25 backdrop-blur border border-white/20 text-white font-bold rounded-2xl text-sm transition-all hover:scale-105 whitespace-nowrap">
                    <i class="fa fa-chalkboard"></i> Papan Baru
                </a>
                <a href="<?= site_url('notes/create') ?>"
                    class="flex items-center gap-2 px-4 py-2.5 bg-orange-500 hover:bg-orange-400 text-white font-bold rounded-2xl text-sm transition-all hover:scale-105 shadow-lg shadow-orange-500/30 whitespace-nowrap">
                    <i class="fa fa-pen-nib"></i> Catatan Baru
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
    <?php
    $stats = [
        ['icon' => 'fa-chalkboard',   'color' => 'violet', 'label' => 'Papan Tulis',  'val' => $total_wb,  'sub' => $today_wb . ' hari ini'],
        ['icon' => 'fa-note-sticky',  'color' => 'orange', 'label' => 'Catatan',       'val' => $total_nt,  'sub' => $today_nt . ' hari ini'],
        ['icon' => 'fa-calendar-day', 'color' => 'emerald','label' => 'Dibuat Hari Ini','val' => $today_wb + $today_nt, 'sub' => 'total kreasi'],
        ['icon' => 'fa-layer-group',  'color' => 'sky',    'label' => 'Total Kreasi',  'val' => $total_wb + $total_nt, 'sub' => 'semua waktu'],
    ];
    $colors = ['violet'=>'bg-violet-50 text-violet-600','orange'=>'bg-orange-50 text-orange-600','emerald'=>'bg-emerald-50 text-emerald-600','sky'=>'bg-sky-50 text-sky-600'];
    foreach ($stats as $s): ?>
    <div class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-100 shadow-sm hover:shadow-md transition-all duration-200 hover:-translate-y-0.5">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-2xl <?= $colors[$s['color']] ?> flex items-center justify-center shadow-inner">
                <i class="fa <?= $s['icon'] ?> text-base"></i>
            </div>
        </div>
        <div class="text-3xl font-extrabold text-slate-900 mb-0.5"><?= $s['val'] ?></div>
        <div class="text-xs font-semibold text-slate-600"><?= $s['label'] ?></div>
        <div class="text-[11px] text-slate-400 mt-0.5"><?= $s['sub'] ?></div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Recent Whiteboards -->
<div class="mb-8">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-violet-100 text-violet-600 rounded-xl flex items-center justify-center text-sm">
                <i class="fa fa-chalkboard"></i>
            </div>
            <h3 class="text-lg font-extrabold text-slate-900">Papan Tulis Terbaru</h3>
        </div>
        <a href="<?= site_url('whiteboard') ?>" class="text-sm text-violet-600 font-bold hover:text-violet-700 hover:underline flex items-center gap-1">
            Lihat Semua <i class="fa fa-arrow-right text-xs"></i>
        </a>
    </div>

    <?php if (empty($recent_wb)): ?>
    <div class="text-center py-12 bg-white rounded-3xl border border-dashed border-violet-200">
        <i class="fa fa-chalkboard text-4xl text-violet-100 mb-3 block"></i>
        <p class="text-slate-400 text-sm">Belum ada papan tulis.</p>
        <a href="<?= site_url('whiteboard/create') ?>" class="mt-3 inline-flex items-center gap-2 text-xs text-violet-600 font-bold hover:underline">
            <i class="fa fa-plus"></i> Buat Sekarang
        </a>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        <?php foreach ($recent_wb as $wb): ?>
        <a href="<?= site_url('whiteboard/edit/' . $wb->id) ?>"
            class="group bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-violet-200 hover:-translate-y-1 transition-all duration-200 overflow-hidden flex items-start gap-3 p-3.5">
            <!-- Thumbnail -->
            <div class="w-16 h-14 rounded-xl flex-shrink-0 bg-violet-50 overflow-hidden flex items-center justify-center">
                <?php if ($wb->thumbnail): ?>
                    <img src="<?= $wb->thumbnail ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <i class="fa fa-chalkboard text-xl text-violet-200"></i>
                <?php endif; ?>
            </div>
            <div class="min-w-0 flex-1">
                <div class="font-bold text-slate-800 text-sm truncate group-hover:text-violet-700 transition-colors">
                    <?= htmlspecialchars($wb->title) ?>
                </div>
                <div class="text-xs text-slate-400 mt-1">
                    <i class="fa fa-clock mr-1"></i><?= date('d M, H:i', strtotime($wb->updated_at)) ?>
                </div>
            </div>
            <i class="fa fa-chevron-right text-xs text-slate-300 group-hover:text-violet-400 group-hover:translate-x-1 transition-all self-center shrink-0"></i>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Recent Notes -->
<div class="mb-4">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center text-sm">
                <i class="fa fa-note-sticky"></i>
            </div>
            <h3 class="text-lg font-extrabold text-slate-900">Catatan Terbaru</h3>
        </div>
        <a href="<?= site_url('notes') ?>" class="text-sm text-orange-600 font-bold hover:text-orange-700 hover:underline flex items-center gap-1">
            Lihat Semua <i class="fa fa-arrow-right text-xs"></i>
        </a>
    </div>

    <?php if (empty($recent_nt)): ?>
    <div class="text-center py-12 bg-white rounded-3xl border border-dashed border-orange-200">
        <i class="fa fa-note-sticky text-4xl text-orange-100 mb-3 block"></i>
        <p class="text-slate-400 text-sm">Belum ada catatan.</p>
        <a href="<?= site_url('notes/create') ?>" class="mt-3 inline-flex items-center gap-2 text-xs text-orange-600 font-bold hover:underline">
            <i class="fa fa-plus"></i> Buat Sekarang
        </a>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        <?php foreach ($recent_nt as $nt): ?>
        <a href="<?= site_url('notes/edit/' . $nt->id) ?>"
            class="group bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-orange-200 hover:-translate-y-1 transition-all duration-200 overflow-hidden flex items-start gap-3 p-3.5">
            <div class="w-16 h-14 rounded-xl flex-shrink-0 bg-orange-50 overflow-hidden flex items-center justify-center">
                <?php if ($nt->thumbnail): ?>
                    <img src="<?= $nt->thumbnail ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <i class="fa fa-note-sticky text-xl text-orange-200"></i>
                <?php endif; ?>
            </div>
            <div class="min-w-0 flex-1">
                <div class="font-bold text-slate-800 text-sm truncate group-hover:text-orange-600 transition-colors">
                    <?= htmlspecialchars($nt->title) ?>
                </div>
                <?php
                $preview = $nt->typed_text ?: $nt->ocr_text ?: '';
                $preview = mb_substr(strip_tags($preview), 0, 60);
                ?>
                <?php if ($preview): ?>
                <div class="text-xs text-slate-400 mt-0.5 truncate"><?= htmlspecialchars($preview) ?>...</div>
                <?php endif; ?>
                <div class="text-xs text-slate-400 mt-1">
                    <i class="fa fa-clock mr-1"></i><?= date('d M, H:i', strtotime($nt->updated_at)) ?>
                </div>
            </div>
            <i class="fa fa-chevron-right text-xs text-slate-300 group-hover:text-orange-400 group-hover:translate-x-1 transition-all self-center shrink-0"></i>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once APPPATH . 'views/layout/footer.php'; ?>
