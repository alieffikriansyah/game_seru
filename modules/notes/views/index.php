<?php require_once APPPATH . 'views/layout/header.php'; ?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-900">📝 Catatan</h2>
        <p class="text-slate-500 text-sm mt-1">Ketik atau tulis tangan — otomatis dikonversi ke teks.</p>
    </div>
    <a href="<?= site_url('notes/create') ?>"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-400 hover:to-orange-500 text-white font-bold rounded-2xl shadow-lg shadow-orange-500/30 hover:shadow-xl hover:scale-[1.02] transition-all duration-200 text-sm whitespace-nowrap">
        <i class="fa fa-plus"></i> Catatan Baru
    </a>
</div>

<?php if (empty($list)): ?>
<div class="flex flex-col items-center justify-center py-24 text-center">
    <div class="w-24 h-24 bg-orange-50 rounded-3xl flex items-center justify-center mb-6 shadow-inner">
        <i class="fa fa-note-sticky text-4xl text-orange-200"></i>
    </div>
    <h3 class="text-xl font-bold text-slate-700 mb-2">Belum ada catatan</h3>
    <p class="text-slate-400 text-sm mb-6 max-w-xs">Buat catatan pertama Anda — ketik atau tulis tangan di kanvas!</p>
    <a href="<?= site_url('notes/create') ?>"
        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold rounded-2xl shadow-lg hover:scale-[1.03] transition-all text-sm">
        <i class="fa fa-plus"></i> Buat Sekarang
    </a>
</div>

<?php else: ?>
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
    <?php foreach ($list as $note): ?>
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-lg hover:border-orange-200 hover:-translate-y-1 transition-all duration-200 overflow-hidden group">
        <!-- Thumbnail / Preview -->
        <a href="<?= site_url('notes/edit/' . $note->id) ?>" class="block">
            <div class="h-36 relative overflow-hidden bg-gradient-to-br from-orange-50 to-amber-50/50">
                <?php if ($note->thumbnail): ?>
                    <img src="<?= $note->thumbnail ?>" alt="Preview" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                <?php else: ?>
                    <!-- Text preview -->
                    <div class="p-4 h-full overflow-hidden">
                        <?php
                        $preview = $note->typed_text ?: $note->ocr_text ?: '';
                        $preview = htmlspecialchars(substr(strip_tags($preview), 0, 150));
                        ?>
                        <?php if ($preview): ?>
                            <p class="text-slate-600 text-xs leading-relaxed font-mono"><?= $preview ?>...</p>
                        <?php else: ?>
                            <div class="flex items-center justify-center h-full">
                                <i class="fa fa-note-sticky text-4xl text-orange-100"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Mode badges -->
                <div class="absolute top-2 right-2 flex gap-1">
                    <?php if ($note->typed_text): ?>
                    <span class="px-2 py-0.5 bg-white/90 backdrop-blur text-orange-700 text-[10px] font-bold rounded-full border border-orange-100">
                        <i class="fa fa-keyboard text-[9px]"></i> Ketik
                    </span>
                    <?php endif; ?>
                    <?php if ($note->ocr_text || $note->canvas_data): ?>
                    <span class="px-2 py-0.5 bg-white/90 backdrop-blur text-violet-700 text-[10px] font-bold rounded-full border border-violet-100">
                        <i class="fa fa-pen-nib text-[9px]"></i> Tulis
                    </span>
                    <?php endif; ?>
                </div>

                <!-- Hover overlay -->
                <div class="absolute inset-0 bg-orange-600/0 group-hover:bg-orange-600/10 transition-all duration-200 flex items-center justify-center">
                    <span class="opacity-0 group-hover:opacity-100 transition-all duration-200 bg-white/90 backdrop-blur text-orange-700 text-sm font-bold px-4 py-2 rounded-full shadow-lg">
                        <i class="fa fa-pen mr-1"></i> Buka Editor
                    </span>
                </div>
            </div>
        </a>

        <!-- Info -->
        <div class="px-4 py-3">
            <a href="<?= site_url('notes/edit/' . $note->id) ?>" class="block">
                <h3 class="font-bold text-slate-800 text-sm truncate mb-1 group-hover:text-orange-600 transition-colors">
                    <?= htmlspecialchars($note->title) ?>
                </h3>
                <p class="text-xs text-slate-400">
                    <i class="fa fa-clock mr-1"></i>
                    <?= date('d M Y, H:i', strtotime($note->updated_at)) ?>
                </p>
            </a>
        </div>

        <!-- Actions -->
        <div class="px-4 pb-3 flex items-center gap-2">
            <a href="<?= site_url('notes/edit/' . $note->id) ?>"
                class="flex-1 text-center py-2 text-xs font-semibold text-orange-700 bg-orange-50 hover:bg-orange-100 rounded-xl transition-colors">
                <i class="fa fa-pen mr-1"></i> Edit
            </a>
            <button onclick="confirmDelete(<?= $note->id ?>, '<?= htmlspecialchars(addslashes($note->title)) ?>')"
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
        title: 'Hapus Catatan?',
        html: `<span class="text-slate-500">Yakin hapus <strong>"${title}"</strong>?</span>`,
        icon: 'warning', showCancelButton: true,
        confirmButtonText: '<i class="fa fa-trash mr-1"></i> Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#ef4444', cancelButtonColor: '#94a3b8',
        customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl font-bold', cancelButton: 'rounded-xl' }
    }).then(r => {
        if (r.isConfirmed) window.location.href = '<?= site_url('notes/delete') ?>/' + id;
    });
}
</script>

<?php require_once APPPATH . 'views/layout/footer.php'; ?>
