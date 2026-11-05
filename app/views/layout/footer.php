    </main>
</div><!-- end main wrapper -->

<!-- FAB Button (quick create) -->
<div class="fixed bottom-6 right-6 z-40 flex flex-col-reverse items-end gap-3" id="fab-container">
    <!-- Main FAB -->
    <button id="fab-main" onclick="toggleFab()"
        class="w-14 h-14 bg-gradient-to-br from-violet-600 to-violet-700 hover:from-violet-500 hover:to-violet-600 text-white rounded-full shadow-xl shadow-violet-500/40 flex items-center justify-center transition-all duration-200 hover:scale-110 active:scale-95">
        <i class="fa fa-plus text-lg transition-transform duration-200" id="fab-icon"></i>
    </button>
    <!-- Sub FAB: Whiteboard -->
    <a href="<?= site_url('whiteboard/create') ?>"
        class="fab-sub hidden items-center gap-3 bg-white rounded-full px-4 py-2.5 shadow-lg shadow-black/10 hover:shadow-violet-200 border border-violet-100 transition-all duration-200 hover:scale-105 animate-slide-up">
        <span class="w-8 h-8 bg-violet-100 text-violet-600 rounded-full flex items-center justify-center text-sm">
            <i class="fa fa-chalkboard"></i>
        </span>
        <span class="text-sm font-semibold text-slate-700 whitespace-nowrap">Papan Tulis Baru</span>
    </a>
    <!-- Sub FAB: Note -->
    <a href="<?= site_url('notes/create') ?>"
        class="fab-sub hidden items-center gap-3 bg-white rounded-full px-4 py-2.5 shadow-lg shadow-black/10 hover:shadow-coral/20 border border-orange-100 transition-all duration-200 hover:scale-105 animate-slide-up">
        <span class="w-8 h-8 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center text-sm">
            <i class="fa fa-note-sticky"></i>
        </span>
        <span class="text-sm font-semibold text-slate-700 whitespace-nowrap">Catatan Baru</span>
    </a>
</div>

<script>
let fabOpen = false;
function toggleFab() {
    fabOpen = !fabOpen;
    const icon = document.getElementById('fab-icon');
    const subs = document.querySelectorAll('.fab-sub');
    if (fabOpen) {
        icon.classList.add('rotate-45');
        subs.forEach(s => { s.classList.remove('hidden'); s.classList.add('flex'); });
    } else {
        icon.classList.remove('rotate-45');
        subs.forEach(s => { s.classList.add('hidden'); s.classList.remove('flex'); });
    }
}
// Close FAB on outside click
document.addEventListener('click', function(e) {
    const fab = document.getElementById('fab-container');
    if (fabOpen && fab && !fab.contains(e.target)) {
        fabOpen = true; toggleFab();
    }
});
</script>

</body>
</html>
