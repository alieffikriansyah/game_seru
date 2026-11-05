<?php require_once APPPATH . 'views/layout/header.php'; ?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-900">🎨 Papan Tulis</h2>
        <p class="text-slate-500 text-sm mt-1">Gambar, jelaskan, dan simpan ide visual Anda.</p>
    </div>
    <a href="<?= site_url('whiteboard/create') ?>"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-violet-600 to-violet-700 hover:from-violet-500 hover:to-violet-600 text-white font-bold rounded-2xl shadow-lg shadow-violet-500/30 hover:shadow-xl hover:scale-[1.02] transition-all duration-200 text-sm whitespace-nowrap">
        <i class="fa fa-plus"></i> Papan Tulis Baru
    </a>
</div>

<?php if (empty($list)): ?>
<!-- Empty State -->
<div class="flex flex-col items-center justify-center py-24 text-center">
    <div class="w-24 h-24 bg-violet-50 rounded-3xl flex items-center justify-center mb-6 shadow-inner">
        <i class="fa fa-chalkboard text-4xl text-violet-300"></i>
    </div>
    <h3 class="text-xl font-bold text-slate-700 mb-2">Belum ada papan tulis</h3>
    <p class="text-slate-400 text-sm mb-6 max-w-xs">Mulai buat papan tulis pertama Anda dan ungkapkan ide visual secara bebas!</p>
    <a href="<?= site_url('whiteboard/create') ?>"
        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-violet-600 to-violet-700 text-white font-bold rounded-2xl shadow-lg hover:scale-[1.03] transition-all text-sm">
        <i class="fa fa-plus"></i> Buat Sekarang
    </a>
</div>

<?php else: ?>
<!-- Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
    <?php foreach ($list as $board): ?>
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-lg hover:border-violet-200 hover:-translate-y-1 transition-all duration-200 overflow-hidden group">
        <!-- Thumbnail -->
        <a href="<?= site_url('whiteboard/edit/' . $board->id) ?>" class="block">
            <div class="h-44 bg-gradient-to-br from-slate-50 to-violet-50/30 relative overflow-hidden">
                <?php if ($board->thumbnail): ?>
                    <img src="<?= $board->thumbnail ?>" alt="Preview" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                <?php else: ?>
                    <div class="flex items-center justify-center h-full">
                        <i class="fa fa-chalkboard text-5xl text-violet-200"></i>
                    </div>
                <?php endif; ?>
                <!-- Hover overlay -->
                <div class="absolute inset-0 bg-violet-600/0 group-hover:bg-violet-600/10 transition-all duration-200 flex items-center justify-center">
                    <span class="opacity-0 group-hover:opacity-100 transition-all duration-200 bg-white/90 backdrop-blur text-violet-700 text-sm font-bold px-4 py-2 rounded-full shadow-lg">
                        <i class="fa fa-pen mr-1"></i> Buka Editor
                    </span>
                </div>
            </div>
        </a>

        <!-- Info -->
        <div class="px-4 py-3">
            <a href="<?= site_url('whiteboard/edit/' . $board->id) ?>" class="block">
                <h3 class="font-bold text-slate-800 text-sm truncate mb-1 group-hover:text-violet-700 transition-colors">
                    <?= htmlspecialchars($board->title) ?>
                </h3>
                <p class="text-xs text-slate-400">
                    <i class="fa fa-clock mr-1"></i>
                    <?= date('d M Y, H:i', strtotime($board->updated_at)) ?>
                </p>
            </a>
        </div>

        <!-- Actions -->
        <div class="px-4 pb-3 flex items-center gap-2">
            <a href="<?= site_url('whiteboard/edit/' . $board->id) ?>"
                class="flex-1 text-center py-2 text-xs font-semibold text-violet-700 bg-violet-50 hover:bg-violet-100 rounded-xl transition-colors">
                <i class="fa fa-pen mr-1"></i> Edit
            </a>
            <a href="<?= site_url('whiteboard/duplicate/' . $board->id) ?>"
                class="px-3 py-2 text-xs font-semibold text-slate-500 bg-slate-50 hover:bg-slate-100 rounded-xl transition-colors" title="Duplikat">
                <i class="fa fa-copy"></i>
            </a>
            <button onclick="confirmDelete(<?= $board->id ?>, '<?= htmlspecialchars(addslashes($board->title)) ?>')"
                class="px-3 py-2 text-xs font-semibold text-rose-500 bg-rose-50 hover:bg-rose-100 rounded-xl transition-colors" title="Hapus">
                <i class="fa fa-trash"></i>
            </button>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<script>
function confirmDelete(id, title) {
    Swal.fire({
        title: 'Hapus Papan Tulis?',
        html: `<span class="text-slate-500">Yakin hapus <strong>"${title}"</strong>?<br>Tindakan ini tidak bisa dibatalkan.</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fa fa-trash mr-1"></i> Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#94a3b8',
        customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl font-bold', cancelButton: 'rounded-xl' }
    }).then(r => {
        if (r.isConfirmed) {
            window.location.href = '<?= site_url('whiteboard/delete') ?>/' + id;
        }
    });
}
</script>

<?php require_once APPPATH . 'views/layout/footer.php'; ?>
