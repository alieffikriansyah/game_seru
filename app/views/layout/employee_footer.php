<?php
$current_uri = trim($_SERVER['REQUEST_URI'], '/');
$script_name = trim(str_replace('index.php', '', $_SERVER['SCRIPT_NAME']), '/');
$current_route = trim(str_replace($script_name, '', $current_uri), '/');

// Count employee claimed active vouchers
$CI =& Controller::get_instance();
$claimed_count = 0;
if ($user_id = Session::get('user_id')) {
    $row = $CI->db->query("SELECT COUNT(*) as total FROM employee_coupons WHERE employee_id = ? AND status = 'claimed'", [$user_id])->row();
    $claimed_count = $row ? (int)$row->total : 0;
}
?>
    </main>

    <!-- Mobile Bottom Navigation Bar (Fixed) -->
    <nav class="fixed bottom-0 left-0 right-0 max-w-md mx-auto bg-white/95 backdrop-blur-md border-t border-slate-200 z-40 bottom-nav px-4 py-2 flex items-center justify-around shadow-lg">
        <!-- Tab 1: Katalog Promo -->
        <a href="<?= site_url('employee') ?>" class="flex flex-col items-center gap-1 text-center group <?= ($current_route === 'employee' || $current_route === '') ? 'text-sky-600' : 'text-slate-400 hover:text-slate-600' ?>">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center transition-all <?= ($current_route === 'employee' || $current_route === '') ? 'bg-sky-50 text-sky-600 font-bold' : '' ?>">
                <i class="fa fa-tags text-base"></i>
            </div>
            <span class="text-[11px] font-semibold tracking-tight">Katalog</span>
        </a>

        <!-- Tab 2: Voucher Saya -->
        <a href="<?= site_url('employee/vouchers') ?>" class="flex flex-col items-center gap-1 text-center group relative <?= (strpos($current_route, 'employee/vouchers') !== false) ? 'text-sky-600' : 'text-slate-400 hover:text-slate-600' ?>">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center transition-all <?= (strpos($current_route, 'employee/vouchers') !== false) ? 'bg-sky-50 text-sky-600 font-bold' : '' ?>">
                <i class="fa fa-ticket-alt text-base"></i>
            </div>
            <span class="text-[11px] font-semibold tracking-tight">Voucher Saya</span>
            <?php if ($claimed_count > 0): ?>
                <span class="absolute top-0 right-2 w-4 h-4 rounded-full bg-rose-500 text-white text-[9px] font-bold flex items-center justify-center shadow">
                    <?= $claimed_count ?>
                </span>
            <?php endif; ?>
        </a>

        <!-- Tab 3: Profil Saya -->
        <a href="<?= site_url('employee/profile') ?>" class="flex flex-col items-center gap-1 text-center group <?= (strpos($current_route, 'employee/profile') !== false) ? 'text-sky-600' : 'text-slate-400 hover:text-slate-600' ?>">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center transition-all <?= (strpos($current_route, 'employee/profile') !== false) ? 'bg-sky-50 text-sky-600 font-bold' : '' ?>">
                <i class="fa fa-user-circle text-base"></i>
            </div>
            <span class="text-[11px] font-semibold tracking-tight">Profil</span>
        </a>
    </nav>

</div>

</body>
</html>
