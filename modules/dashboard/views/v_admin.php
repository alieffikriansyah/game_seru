<!-- Admin Dashboard View — Simplified & Elegant -->
<div class="space-y-6">

    <!-- Top Hero Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-sky-950 rounded-2xl p-6 md:p-7 text-white shadow-sm border border-slate-800 relative overflow-hidden">
        <div class="absolute right-4 -bottom-6 opacity-10 pointer-events-none">
            <i class="fa fa-plane text-9xl text-white"></i>
        </div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-sky-500/20 text-sky-300 text-[11px] font-semibold mb-2 border border-sky-400/20">
                    <i class="fa fa-plane-up text-[10px]"></i> Airport Administration
                </div>
                <h2 class="text-2xl font-black tracking-tight">Operasional & Komersial Mitra</h2>
                <p class="text-slate-300 text-xs mt-1">Kelola permohonan tenant, verifikasi promo, dan utilisasi fasilitas bandara.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?= site_url('tenants') ?>" class="px-3.5 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-semibold backdrop-blur-sm transition-colors flex items-center gap-1.5">
                    <i class="fa fa-building"></i> Data Tenant
                </a>
                <a href="<?= site_url('subscriptions/packages') ?>" class="px-3.5 py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-semibold shadow-sm transition-colors flex items-center gap-1.5">
                    <i class="fa fa-layer-group"></i> Paket Iklan
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats Metric Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Total Tenants -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-500">Mitra Tenant</span>
                <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                    <i class="fa fa-building"></i>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900"><?= number_format($total_tenants) ?></div>
            <div class="mt-1 text-[11px] text-slate-400">Total mitra aktif</div>
        </div>

        <!-- Metric 2: Active Ad Subscriptions -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-500">Sewa Iklan Aktif</span>
                <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                    <i class="fa fa-plane-departure"></i>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900"><?= number_format($active_subscriptions) ?></div>
            <div class="mt-1 text-[11px] text-slate-400">Slot berbayar aktif</div>
        </div>

        <!-- Metric 3: Pending Promo Verifications -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-500">Antrean Promo</span>
                <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                    <i class="fa fa-tags"></i>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900"><?= number_format($pending_promos) ?></div>
            <div class="mt-1 text-[11px]">
                <?php if ($pending_promos > 0): ?>
                    <a href="<?= site_url('promotions/admin_index') ?>" class="text-amber-600 font-semibold hover:underline">Perlu kurasi &rarr;</a>
                <?php else: ?>
                    <span class="text-slate-400">Tidak ada antrean</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Metric 4: Pending Services / Utility Tickets -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-500">Tiket Utilitas</span>
                <span class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-xs">
                    <i class="fa fa-tools"></i>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900"><?= number_format($pending_services) ?></div>
            <div class="mt-1 text-[11px]">
                <?php if ($pending_services > 0): ?>
                    <a href="<?= site_url('services/admin_index') ?>" class="text-purple-600 font-semibold hover:underline">Perlu approval &rarr;</a>
                <?php else: ?>
                    <span class="text-slate-400">Semua terselesaikan</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Section: 2 Columns for Verification Queues -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Column A: Pending Promo Verifications -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900 flex items-center gap-2 text-sm">
                        <i class="fa fa-tags text-amber-500"></i>
                        Kurasi Materi Promo
                    </h3>
                    <a href="<?= site_url('promotions/admin_index') ?>" class="text-xs text-sky-600 hover:text-sky-800 font-semibold">Semua &rarr;</a>
                </div>

                <?php if (empty($recent_promos)): ?>
                    <div class="text-center py-8 text-slate-400 text-xs">
                        <i class="fa fa-check-circle text-2xl text-emerald-400 mb-1.5"></i>
                        <p>Semua promo telah terverifikasi.</p>
                    </div>
                <?php else: ?>
                    <div class="divide-y divide-slate-100">
                        <?php foreach ($recent_promos as $promo): ?>
                            <div class="py-2.5 flex items-center justify-between gap-2 text-xs">
                                <div class="truncate">
                                    <div class="font-bold text-slate-900 truncate"><?= htmlspecialchars($promo->title) ?></div>
                                    <div class="text-[11px] text-slate-500"><?= htmlspecialchars($promo->tenant_name) ?></div>
                                </div>
                                <a href="<?= site_url('promotions/admin_review/' . $promo->id) ?>" class="px-2.5 py-1 bg-sky-50 text-sky-700 hover:bg-sky-100 rounded-lg text-xs font-semibold shrink-0 transition-colors">
                                    Review
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Column B: Pending Utility Service Approvals -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900 flex items-center gap-2 text-sm">
                        <i class="fa fa-tools text-purple-500"></i>
                        Permohonan Utilitas
                    </h3>
                    <a href="<?= site_url('services/admin_index') ?>" class="text-xs text-sky-600 hover:text-sky-800 font-semibold">Semua &rarr;</a>
                </div>

                <?php if (empty($recent_services)): ?>
                    <div class="text-center py-8 text-slate-400 text-xs">
                        <i class="fa fa-check-circle text-2xl text-emerald-400 mb-1.5"></i>
                        <p>Tidak ada tiket utilitas pending.</p>
                    </div>
                <?php else: ?>
                    <div class="divide-y divide-slate-100">
                        <?php foreach ($recent_services as $svc): ?>
                            <div class="py-2.5 flex items-center justify-between gap-2 text-xs">
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-bold text-slate-900 capitalize"><?= htmlspecialchars($svc->service_type) ?></span>
                                        <?= status_badge($svc->status) ?>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        <?= htmlspecialchars($svc->tenant_name) ?> &bull; <?= htmlspecialchars($svc->current_capacity) ?> &rarr; <?= htmlspecialchars($svc->requested_capacity) ?>
                                    </div>
                                </div>
                                <a href="<?= site_url('services/admin_review/' . $svc->id) ?>" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg text-xs font-semibold shrink-0 transition-colors">
                                    Detail
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- Recent Billings Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100">
            <div>
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa fa-receipt text-emerald-600"></i>
                    Tagihan Sewa Iklan Terbaru
                </h3>
            </div>
            <a href="<?= site_url('subscriptions/admin_billings') ?>" class="text-xs font-semibold text-sky-600 hover:text-sky-800">
                Semua Tagihan &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-200 text-[11px]">
                        <th class="px-3.5 py-2.5">Invoice</th>
                        <th class="px-3.5 py-2.5">Tenant</th>
                        <th class="px-3.5 py-2.5">Jumlah</th>
                        <th class="px-3.5 py-2.5">Jatuh Tempo</th>
                        <th class="px-3.5 py-2.5">Status</th>
                        <th class="px-3.5 py-2.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($recent_billings)): ?>
                        <tr><td colspan="6" class="text-center py-6 text-slate-400">Belum ada data tagihan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recent_billings as $b): ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-3.5 py-2.5 font-mono font-semibold text-slate-700"><?= htmlspecialchars($b->invoice_no) ?></td>
                                <td class="px-3.5 py-2.5 font-bold text-slate-800"><?= htmlspecialchars($b->tenant_name) ?></td>
                                <td class="px-3.5 py-2.5 font-semibold text-slate-900"><?= rupiah($b->amount) ?></td>
                                <td class="px-3.5 py-2.5 text-slate-500"><?= tgl_indo($b->due_date) ?></td>
                                <td class="px-3.5 py-2.5"><?= status_badge($b->payment_status) ?></td>
                                <td class="px-3.5 py-2.5 text-right">
                                    <a href="<?= site_url('subscriptions/admin_verify_payment/' . $b->id) ?>" class="px-2.5 py-1 bg-sky-50 text-sky-700 hover:bg-sky-100 rounded-lg text-xs font-semibold border border-sky-200 transition-colors">
                                        Periksa
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
