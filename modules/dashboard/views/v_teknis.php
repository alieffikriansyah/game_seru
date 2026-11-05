<!-- Teknis Dashboard View — Simplified & Elegant -->
<div class="space-y-6">

    <!-- Top Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-amber-950 rounded-2xl p-6 md:p-7 text-white shadow-sm border border-slate-800 relative overflow-hidden">
        <div class="absolute right-4 -bottom-6 opacity-10 pointer-events-none">
            <i class="fa fa-plane text-9xl text-white"></i>
        </div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 text-[11px] font-semibold mb-2 border border-amber-400/20">
                    <i class="fa fa-tools text-[10px]"></i> Tim Utilitas Bandara
                </div>
                <h2 class="text-2xl font-black tracking-tight">Penanganan Utilitas & Fasilitas</h2>
                <p class="text-slate-300 text-xs mt-1">Survei teknis lapangan dan instalasi daya listrik, air, gas, dan pendingin ruangan.</p>
            </div>
            <a href="<?= site_url('services/teknis_index') ?>" class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-xs font-semibold shadow-sm transition-colors flex items-center gap-1.5 self-start md:self-auto">
                <i class="fa fa-list"></i> Semua Tiket
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Stat 1: Total Tiket -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-500">Total Tiket</span>
                <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-xs">
                    <i class="fa fa-clipboard-list"></i>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900"><?= number_format($total_tickets) ?></div>
        </div>

        <!-- Stat 2: Perlu Survei Lapangan -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-500">Perlu Survei</span>
                <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                    <i class="fa fa-search"></i>
                </span>
            </div>
            <div class="text-2xl font-black text-amber-600"><?= number_format($pending_survey) ?></div>
        </div>

        <!-- Stat 3: Sedang Dikerjakan -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-500">Dalam Pengerjaan</span>
                <span class="w-7 h-7 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center text-xs">
                    <i class="fa fa-spinner"></i>
                </span>
            </div>
            <div class="text-2xl font-black text-sky-600"><?= number_format($in_progress) ?></div>
        </div>

        <!-- Stat 4: Tiket Urgent -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-500">Prioritas Urgent</span>
                <span class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-xs">
                    <i class="fa fa-bolt"></i>
                </span>
            </div>
            <div class="text-2xl font-black text-rose-600"><?= number_format($urgent_count) ?></div>
        </div>
    </div>

    <!-- Active Tickets Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100">
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i class="fa fa-tools text-amber-500"></i>
                Tiket Permohonan Utilitas
            </h3>
            <a href="<?= site_url('services/teknis_index') ?>" class="text-xs text-sky-600 hover:text-sky-800 font-semibold">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-200 text-[11px]">
                        <th class="px-3.5 py-2.5">ID Tiket</th>
                        <th class="px-3.5 py-2.5">Tenant & Lokasi</th>
                        <th class="px-3.5 py-2.5">Layanan</th>
                        <th class="px-3.5 py-2.5">Kapasitas</th>
                        <th class="px-3.5 py-2.5">Status</th>
                        <th class="px-3.5 py-2.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($tickets)): ?>
                        <tr><td colspan="6" class="text-center py-6 text-slate-400">Tidak ada tiket utilitas saat ini.</td></tr>
                    <?php else: ?>
                        <?php foreach ($tickets as $t): ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-3.5 py-2.5 font-mono font-bold text-slate-600">#REQ-<?= str_pad($t->id, 4, '0', STR_PAD_LEFT) ?></td>
                                <td class="px-3.5 py-2.5">
                                    <div class="font-bold text-slate-900"><?= htmlspecialchars($t->tenant_name) ?></div>
                                    <div class="text-[11px] text-slate-500"><?= htmlspecialchars($t->terminal_zone) ?> &bull; Lot <?= htmlspecialchars($t->lot_number) ?></div>
                                </td>
                                <td class="px-3.5 py-2.5">
                                    <span class="inline-flex items-center gap-1 font-semibold uppercase text-[11px]">
                                        <?php if ($t->service_type === 'listrik'): ?>
                                            <i class="fa fa-bolt text-amber-500"></i> Listrik
                                        <?php elseif ($t->service_type === 'air'): ?>
                                            <i class="fa fa-tint text-sky-500"></i> Air
                                        <?php elseif ($t->service_type === 'gas'): ?>
                                            <i class="fa fa-fire text-orange-500"></i> Gas
                                        <?php else: ?>
                                            <i class="fa fa-wind text-teal-500"></i> HVAC
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td class="px-3.5 py-2.5 text-slate-600">
                                    <?= htmlspecialchars($t->current_capacity) ?> &rarr; <strong class="text-sky-700"><?= htmlspecialchars($t->requested_capacity) ?></strong>
                                </td>
                                <td class="px-3.5 py-2.5"><?= status_badge($t->status) ?></td>
                                <td class="px-3.5 py-2.5 text-right">
                                    <a href="<?= site_url('services/teknis_detail/' . $t->id) ?>" class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors inline-flex items-center gap-1">
                                        Survei
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
