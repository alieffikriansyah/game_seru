<!-- Tenant / Mitra Dashboard View — Simplified & Elegant -->
<div class="space-y-6">

    <!-- Top Hero Banner with Tenant Info -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-sky-950 rounded-2xl p-6 md:p-7 text-white shadow-sm border border-slate-800 relative overflow-hidden">
        <div class="absolute right-4 -bottom-6 opacity-10 pointer-events-none">
            <i class="fa fa-plane text-9xl text-white"></i>
        </div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-sky-500/20 text-sky-300 text-[11px] font-semibold mb-2 border border-sky-400/20">
                    <i class="fa fa-plane-departure text-[10px]"></i> Mitra Bandara
                </div>
                <h2 class="text-2xl font-black tracking-tight"><?= htmlspecialchars($tenant->tenant_name) ?></h2>
                <div class="text-slate-300 text-xs mt-1 flex items-center gap-2">
                    <span><i class="fa fa-map-pin text-sky-400"></i> <?= htmlspecialchars($tenant->terminal_zone) ?></span>
                    <span class="text-slate-500">•</span>
                    <span>Lot <strong><?= htmlspecialchars($tenant->lot_number) ?></strong></span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="<?= site_url('coupons') ?>" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-sm transition-all">
                    <i class="fa fa-qrcode"></i>
                    <span>Kasir Kupon</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Ad Subscription Status Alert -->
    <?php if ($active_sub): ?>
        <?php $is_expiring = ($active_sub->status === 'expiring_soon'); ?>
        <div class="p-4 rounded-2xl <?= $is_expiring ? 'bg-amber-50 border border-amber-200' : 'bg-white border border-slate-200' ?> shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl <?= $is_expiring ? 'bg-amber-500' : 'bg-sky-600' ?> text-white flex items-center justify-center text-base shrink-0 shadow-sm">
                    <i class="fa <?= $is_expiring ? 'fa-hourglass-half' : 'fa-plane' ?>"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($active_sub->package_name) ?></span>
                        <?= status_badge($active_sub->status) ?>
                        <?php if ($is_expiring): ?>
                            <span class="text-[11px] font-bold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-md">
                                Sisa <?= $days_remaining ?> Hari
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="text-xs text-slate-500 mt-0.5">
                        Berlaku s/d <strong><?= tgl_indo($active_sub->end_date) ?></strong> &bull; Kuota: <?= $active_sub->max_promo_upload ?> promo, <?= $active_sub->max_video_upload ?? 1 ?> video
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="<?= site_url('promotions/create') ?>" class="px-3.5 py-2 bg-slate-900 hover:bg-sky-600 text-white rounded-xl text-xs font-semibold transition-colors flex items-center gap-1.5">
                    <i class="fa fa-plus"></i> Upload Promo
                </a>
                <a href="<?= site_url('subscriptions') ?>" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-colors flex items-center gap-1.5">
                    <i class="fa fa-sync-alt"></i> Paket
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center text-base shrink-0">
                    <i class="fa fa-exclamation"></i>
                </div>
                <div>
                    <div class="font-bold text-amber-900 text-sm">Belum Ada Paket Langganan Aktif</div>
                    <div class="text-xs text-amber-700 mt-0.5">Pilih paket untuk mulai mengunggah promosi produk Anda.</div>
                </div>
            </div>
            <a href="<?= site_url('subscriptions') ?>" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl text-xs transition-colors flex items-center gap-1.5 shrink-0">
                <i class="fa fa-shopping-cart"></i> Pilih Paket
            </a>
        </div>
    <?php endif; ?>

    <!-- Unpaid Invoices Alert -->
    <?php if (!empty($unpaid_invoices)): ?>
        <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 flex items-center justify-between text-xs">
            <div class="flex items-center gap-2">
                <i class="fa fa-bell text-rose-600"></i>
                <span>Ada <strong><?= count($unpaid_invoices) ?> tagihan</strong> yang belum dibayar.</span>
            </div>
            <a href="<?= site_url('subscriptions') ?>" class="font-bold text-rose-700 hover:underline">
                Bayar Sekarang &rarr;
            </a>
        </div>
    <?php endif; ?>

    <!-- 4 Key Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Kupon Hari Ini -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-500">Kupon Hari Ini</span>
                <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                    <i class="fa fa-check"></i>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900"><?= number_format($today_redeemed) ?></div>
        </div>

        <!-- Card 2: Total Kupon -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-500">Total Ditebus</span>
                <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                    <i class="fa fa-receipt"></i>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900"><?= number_format($total_redeemed) ?></div>
        </div>

        <!-- Card 3: Promo Aktif -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-500">Promo Aktif</span>
                <span class="w-7 h-7 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center text-xs">
                    <i class="fa fa-plane-departure"></i>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900"><?= number_format($approved_promos_count) ?></div>
        </div>

        <!-- Card 4: Permohonan Servis -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-500">Servis Utilitas</span>
                <span class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-xs">
                    <i class="fa fa-tools"></i>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900"><?= number_format(count($service_requests)) ?></div>
        </div>
    </div>

    <!-- Quick Action Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <a href="<?= site_url('coupons') ?>" class="p-4 bg-white rounded-2xl border border-slate-200 hover:border-sky-500 hover:shadow-sm transition-all text-center group">
            <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-700 group-hover:bg-sky-50 group-hover:text-sky-600 flex items-center justify-center text-base mx-auto mb-2 transition-colors">
                <i class="fa fa-qrcode"></i>
            </div>
            <div class="font-bold text-slate-800 text-xs">Kasir Kupon</div>
        </a>

        <a href="<?= site_url('promotions') ?>" class="p-4 bg-white rounded-2xl border border-slate-200 hover:border-sky-500 hover:shadow-sm transition-all text-center group">
            <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-700 group-hover:bg-sky-50 group-hover:text-sky-600 flex items-center justify-center text-base mx-auto mb-2 transition-colors">
                <i class="fa fa-plane-departure"></i>
            </div>
            <div class="font-bold text-slate-800 text-xs">Promo Saya</div>
        </a>

        <a href="<?= site_url('subscriptions') ?>" class="p-4 bg-white rounded-2xl border border-slate-200 hover:border-sky-500 hover:shadow-sm transition-all text-center group">
            <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-700 group-hover:bg-sky-50 group-hover:text-sky-600 flex items-center justify-center text-base mx-auto mb-2 transition-colors">
                <i class="fa fa-layer-group"></i>
            </div>
            <div class="font-bold text-slate-800 text-xs">Paket Iklan</div>
        </a>

        <a href="<?= site_url('services') ?>" class="p-4 bg-white rounded-2xl border border-slate-200 hover:border-sky-500 hover:shadow-sm transition-all text-center group">
            <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-700 group-hover:bg-sky-50 group-hover:text-sky-600 flex items-center justify-center text-base mx-auto mb-2 transition-colors">
                <i class="fa fa-tools"></i>
            </div>
            <div class="font-bold text-slate-800 text-xs">Servis Utilitas</div>
        </a>
    </div>

</div>
