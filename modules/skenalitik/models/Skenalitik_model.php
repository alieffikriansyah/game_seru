<?php

class Skenalitik_model extends Model {

    public function __construct() {
        parent::__construct();
    }

    // ── Master Data 40 Petak Papan Skenalitik ─────────────────────
    public static function get_tiles_config() {
        return [
            0  => ['id' => 0,  'name' => 'START (Panggung Utama)',        'icon' => '🎪', 'badge' => 'PANGGUNG',    'type' => 'corner',   'group' => 'corner',    'price' => 0,   'rent' => 0,  'desc' => '+200 PR saat melintas'],
            1  => ['id' => 1,  'name' => 'Warkop Trotoar Gondangdia',     'icon' => '🍜', 'badge' => 'WARKOP',      'type' => 'property', 'group' => 'cokelat',   'price' => 60,  'rent' => 2,  'upgrade_cost' => 50],
            2  => ['id' => 2,  'name' => 'Gimmick Skena',                 'icon' => '🎭', 'badge' => 'GIMMICK',     'type' => 'card',     'group' => 'skena',     'price' => 0,   'rent' => 0,  'desc' => 'Ambil 1 kartu Skena'],
            3  => ['id' => 3,  'name' => 'Basement Parkiran Antik',       'icon' => '🛵', 'badge' => 'BASEMENT',    'type' => 'property', 'group' => 'cokelat',   'price' => 60,  'rent' => 4,  'upgrade_cost' => 50],
            4  => ['id' => 4,  'name' => 'Pajak Royalti Musik',           'icon' => '💸', 'badge' => 'ROYALTI',     'type' => 'tax',      'group' => 'tax',       'price' => 0,   'rent' => 200,'desc' => 'Bayar 200 PR ke Kas/Pot'],
            5  => ['id' => 5,  'name' => 'Halte MRT Blok M',              'icon' => '🚇', 'badge' => 'TRANSIT',     'type' => 'transit',  'group' => 'transit',   'price' => 200, 'rent' => 25, 'desc' => 'Sewa kelipatan transit'],
            6  => ['id' => 6,  'name' => 'Toko Kaset & Rilisan Fisik',    'icon' => '📼', 'badge' => 'KASET',       'type' => 'property', 'group' => 'biru_muda', 'price' => 100, 'rent' => 6,  'upgrade_cost' => 50],
            7  => ['id' => 7,  'name' => 'Manuver Politik',               'icon' => '♟️', 'badge' => 'POLITIK',     'type' => 'card',     'group' => 'politik',   'price' => 0,   'rent' => 0,  'desc' => 'Ambil 1 kartu Politik'],
            8  => ['id' => 8,  'name' => 'Kedai Kopi Manual Brew',        'icon' => '☕', 'badge' => 'KOPI',        'type' => 'property', 'group' => 'biru_muda', 'price' => 100, 'rent' => 6,  'upgrade_cost' => 50],
            9  => ['id' => 9,  'name' => 'Bioskop Sinema Alternatif',     'icon' => '🎬', 'badge' => 'CINEMA',      'type' => 'property', 'group' => 'biru_muda', 'price' => 120, 'rent' => 8,  'upgrade_cost' => 50],
            10 => ['id' => 10, 'name' => 'KARANTINA KLARIFIKASI',         'icon' => '🚨', 'badge' => 'KLARIFIKASI', 'type' => 'corner',   'group' => 'corner',    'price' => 0,   'rent' => 0,  'desc' => 'Singgah / Penjara Skandal'],
            11 => ['id' => 11, 'name' => 'Studio Sablon Zine',            'icon' => '👕', 'badge' => 'SABLON',      'type' => 'property', 'group' => 'merah_muda','price' => 140, 'rent' => 10, 'upgrade_cost' => 100],
            12 => ['id' => 12, 'name' => 'Genset Konser Sewaan',         'icon' => '⚡', 'badge' => 'GENSET',      'type' => 'utility',  'group' => 'utilitas',  'price' => 150, 'rent' => 0,  'desc' => '4x / 10x angka dadu'],
            13 => ['id' => 13, 'name' => 'Kolektif Mural Jalanan',       'icon' => '🎨', 'badge' => 'MURAL',       'type' => 'property', 'group' => 'merah_muda','price' => 140, 'rent' => 10, 'upgrade_cost' => 100],
            14 => ['id' => 14, 'name' => 'Galeri Seni Rupa Abstrak',     'icon' => '🖼️', 'badge' => 'GALERI',      'type' => 'property', 'group' => 'merah_muda','price' => 160, 'rent' => 12, 'upgrade_cost' => 100],
            15 => ['id' => 15, 'name' => 'Stasiun Manggarai',            'icon' => '🚉', 'badge' => 'TRANSIT',     'type' => 'transit',  'group' => 'transit',   'price' => 200, 'rent' => 25, 'desc' => 'Sewa kelipatan transit'],
            16 => ['id' => 16, 'name' => 'Label Rekaman Independen',     'icon' => '💿', 'badge' => 'INDIE',       'type' => 'property', 'group' => 'oranye',    'price' => 180, 'rent' => 14, 'upgrade_cost' => 100],
            17 => ['id' => 17, 'name' => 'Gimmick Skena',                 'icon' => '🎭', 'badge' => 'GIMMICK',     'type' => 'card',     'group' => 'skena',     'price' => 0,   'rent' => 0,  'desc' => 'Ambil 1 kartu Skena'],
            18 => ['id' => 18, 'name' => 'Bar Musik Bawah Tanah',        'icon' => '🍸', 'badge' => 'BAR CLUB',    'type' => 'property', 'group' => 'oranye',    'price' => 180, 'rent' => 14, 'upgrade_cost' => 100],
            19 => ['id' => 19, 'name' => 'Gudang Konser Alternatif',     'icon' => '🏭', 'badge' => 'GUDANG',      'type' => 'property', 'group' => 'oranye',    'price' => 200, 'rent' => 16, 'upgrade_cost' => 100],
            20 => ['id' => 20, 'name' => 'PARKIR GRATIS V.I.P',          'icon' => '🛑', 'badge' => 'V.I.P',        'type' => 'corner',   'group' => 'corner',    'price' => 0,   'rent' => 0,  'desc' => 'Titik aman & klaim denda'],
            21 => ['id' => 21, 'name' => 'Co-working Space Jaksel',      'icon' => '💻', 'badge' => 'JAKSEL',      'type' => 'property', 'group' => 'merah',     'price' => 220, 'rent' => 18, 'upgrade_cost' => 150],
            22 => ['id' => 22, 'name' => 'Manuver Politik',               'icon' => '♟️', 'badge' => 'POLITIK',     'type' => 'card',     'group' => 'politik',   'price' => 0,   'rent' => 0,  'desc' => 'Ambil 1 kartu Politik'],
            23 => ['id' => 23, 'name' => 'Agensi Periklanan Viral',       'icon' => '📢', 'badge' => 'AGENCY',      'type' => 'property', 'group' => 'merah',     'price' => 220, 'rent' => 18, 'upgrade_cost' => 150],
            24 => ['id' => 24, 'name' => 'Studio Podcast Kontroversial', 'icon' => '🎙️', 'badge' => 'PODCAST',     'type' => 'property', 'group' => 'merah',     'price' => 240, 'rent' => 20, 'upgrade_cost' => 150],
            25 => ['id' => 25, 'name' => 'Stasiun Sudirman BNI City',    'icon' => '🚄', 'badge' => 'TRANSIT',     'type' => 'transit',  'group' => 'transit',   'price' => 200, 'rent' => 25, 'desc' => 'Sewa kelipatan transit'],
            26 => ['id' => 26, 'name' => 'Media Gosip & Buzzer Akun X',   'icon' => '📱', 'badge' => 'BUZZER',      'type' => 'property', 'group' => 'kuning',    'price' => 260, 'rent' => 22, 'upgrade_cost' => 150],
            27 => ['id' => 27, 'name' => 'Lembaga Survei Rekayasa',      'icon' => '📊', 'badge' => 'SURVEI',      'type' => 'property', 'group' => 'kuning',    'price' => 260, 'rent' => 22, 'upgrade_cost' => 150],
            28 => ['id' => 28, 'name' => 'Server Pasukan Siber Bot',     'icon' => '🤖', 'badge' => 'BOTS',        'type' => 'utility',  'group' => 'utilitas',  'price' => 150, 'rent' => 0,  'desc' => '4x / 10x angka dadu'],
            29 => ['id' => 29, 'name' => 'Kantor Konsultan Pencitraan',  'icon' => '👔', 'badge' => 'KONSULTAN',   'type' => 'property', 'group' => 'kuning',    'price' => 280, 'rent' => 24, 'upgrade_cost' => 150],
            30 => ['id' => 30, 'name' => 'TERJERAT SKANDAL (Penjara)',   'icon' => '🚔', 'badge' => 'SKANDAL',     'type' => 'corner',   'group' => 'corner',    'price' => 0,   'rent' => 0,  'desc' => 'Langsung ke Karantina'],
            31 => ['id' => 31, 'name' => 'Yayasan Kebudayaan Negara',     'icon' => '🏛️', 'badge' => 'YAYASAN',     'type' => 'property', 'group' => 'hijau',     'price' => 300, 'rent' => 26, 'upgrade_cost' => 200],
            32 => ['id' => 32, 'name' => 'Event Organizer Festival 3 Hari','icon'=> '🎪', 'badge' => 'FESTIVAL',   'type' => 'property', 'group' => 'hijau',     'price' => 300, 'rent' => 26, 'upgrade_cost' => 200],
            33 => ['id' => 33, 'name' => 'Gimmick Skena',                 'icon' => '🎭', 'badge' => 'GIMMICK',     'type' => 'card',     'group' => 'skena',     'price' => 0,   'rent' => 0,  'desc' => 'Ambil 1 kartu Skena'],
            34 => ['id' => 34, 'name' => 'Holding Company Hiburan',      'icon' => '🏢', 'badge' => 'HOLDING',     'type' => 'property', 'group' => 'hijau',     'price' => 320, 'rent' => 28, 'upgrade_cost' => 200],
            35 => ['id' => 35, 'name' => 'Bandara Tur Musisi Luar',      'icon' => '✈️', 'badge' => 'TRANSIT',     'type' => 'transit',  'group' => 'transit',   'price' => 200, 'rent' => 25, 'desc' => 'Sewa kelipatan transit'],
            36 => ['id' => 36, 'name' => 'Manuver Politik',               'icon' => '♟️', 'badge' => 'POLITIK',     'type' => 'card',     'group' => 'politik',   'price' => 0,   'rent' => 0,  'desc' => 'Ambil 1 kartu Politik'],
            37 => ['id' => 37, 'name' => 'Konsorsium Tambang & Sawit',   'icon' => '🏗️', 'badge' => 'TAMBANG',     'type' => 'property', 'group' => 'biru_tua',  'price' => 350, 'rent' => 35, 'upgrade_cost' => 200],
            38 => ['id' => 38, 'name' => 'Iuran Solidaritas Palsu',       'icon' => '💸', 'badge' => 'IURAN',       'type' => 'tax',      'group' => 'tax',       'price' => 0,   'rent' => 100,'desc' => 'Bayar 100 PR ke Kas/Pot'],
            39 => ['id' => 39, 'name' => 'Gedung Parlemen Senayan',      'icon' => '🏛️', 'badge' => 'SENAYAN',     'type' => 'property', 'group' => 'biru_tua',  'price' => 400, 'rent' => 50, 'upgrade_cost' => 200],
        ];
    }

    // ── Kelompok Rumpun Warna untuk Monopoli ──────────────────────
    public static function get_color_groups() {
        return [
            'cokelat'    => [1, 3],
            'biru_muda'  => [6, 8, 9],
            'merah_muda' => [11, 13, 14],
            'oranye'     => [16, 18, 19],
            'merah'      => [21, 23, 24],
            'kuning'     => [26, 27, 29],
            'hijau'      => [31, 32, 34],
            'biru_tua'   => [37, 39],
            'transit'    => [5, 15, 25, 35],
            'utilitas'   => [12, 28],
        ];
    }

    // ── 6 Karakter Bidak Khas ─────────────────────────────────────
    public static function get_tokens_config() {
        return [
            ['name' => 'Kamera Analog',          'icon' => '📷', 'color' => '#f59e0b'],
            ['name' => 'Kopi Susu Literan',      'icon' => '☕', 'color' => '#8b5cf6'],
            ['name' => 'Kacamata Frame Tebal',   'icon' => '👓', 'color' => '#06b6d4'],
            ['name' => 'Mikrofon Podcast',       'icon' => '🎙️', 'color' => '#ef4444'],
            ['name' => 'Amplop Cokelat',         'icon' => '✉️', 'color' => '#eab308'],
            ['name' => 'Sepatu Sneakers Kanvas', 'icon' => '👟', 'color' => '#10b981'],
        ];
    }

    // ── 20 Kartu Manuver Politik ──────────────────────────────────
    public static function get_politik_cards() {
        return [
            1  => ['title' => 'Gunting dalam Lipatan',      'desc' => 'Rampas 300 PR dari pemain di sebelah kanan Anda.'],
            2  => ['title' => 'Kudeta Aset',                'desc' => 'Rebut paksa 1 petak cokelat/biru muda milik lawan dengan poin tertinggi secara cuma-cuma.'],
            3  => ['title' => 'OTT Rekayasa',               'desc' => 'Kirim satu pemain pilihan langsung ke petak Karantina Klarifikasi.'],
            4  => ['title' => 'Ijazah Palsu',               'desc' => 'Bongkar kebohongan publik lawan; target kehilangan 350 PR dan poinnya masuk ke saldo Anda.'],
            5  => ['title' => 'Cabut Izin Keramaian',       'desc' => 'Pilih 1 properti lawan; properti tersebut dilarang menarik sewa selama 2 putaran.'],
            6  => ['title' => 'Buzzer Pembunuh Karakter',   'desc' => 'Bayar 150 PR ke kas untuk memangkas 500 PR milik pemain dengan poin tertinggi.'],
            7  => ['title' => 'Bansos Dobel',               'desc' => 'Ambil 400 PR dari Kas tanpa audit.'],
            8  => ['title' => 'Intervensi Hakim',           'desc' => 'Bebaskan diri Anda dari petak Karantina Klarifikasi tanpa syarat. Simpan kartu ini.'],
            9  => ['title' => 'Kriminalisasi Rival',        'desc' => 'Pilih 1 pemain; ia harus membayar Anda 200 PR atau langsung masuk Karantina.'],
            10 => ['title' => 'Lobi Senayan',               'desc' => 'Gandakan tarif sewa seluruh properti Anda selama 1 putaran penuh.'],
            11 => ['title' => 'Pajak Ekosistem Kreatif',    'desc' => 'Tarik 100 PR dari setiap pemain yang memiliki minimal 1 properti.'],
            12 => ['title' => 'Goreng Isu SARA',            'desc' => 'Ciptakan kegaduhan; seluruh lawan mundur 2 langkah dari posisinya saat ini.'],
            13 => ['title' => 'Staf Khusus Jalur Titipan',  'desc' => 'Ambil 300 PR dari kas negara sebagai uang kompensasi jabatan.'],
            14 => ['title' => 'Audit Fiktif',               'desc' => 'Paksa 1 pemain membayar 50 PR untuk setiap unit Sablon Gelap yang dimilikinya.'],
            15 => ['title' => 'Serangan Siber DDoS',        'desc' => 'Lumpuhkan petak Utilitas milik lawan; mereka tidak bisa menarik bayaran sampai putaran berikutnya.'],
            16 => ['title' => 'Tender Pengadaan Laptop',    'desc' => 'Dapatkan 500 PR langsung dari kas negara.'],
            17 => ['title' => 'Jatah Komisaris Siluman',    'desc' => 'Dapatkan 350 PR dari kas atas nama konsorsium.'],
            18 => ['title' => 'Pembungkaman Saksi',         'desc' => 'Bayar 100 PR untuk membatalkan efek kartu aksi lawan. Simpan kartu ini.'],
            19 => ['title' => 'Tukar Kasus',                'desc' => 'Pindahkan status Karantina Anda kepada pemain lain yang memiliki PR paling sedikit.'],
            20 => ['title' => 'Blackmail Politik',          'desc' => 'Intip kartu tangan lawan dan buang 1 kartu terkuat yang mereka pegang.'],
        ];
    }

    // ── 20 Kartu Gimmick Skena ────────────────────────────────────
    public static function get_skena_cards() {
        return [
            1  => ['title' => 'Bajak Royalti',              'desc' => 'Klaim lagu hits lawan; tarik 150 PR dari setiap pemain di meja.'],
            2  => ['title' => 'Kolektif Tipu-Tipu',         'desc' => 'Bawa kabur uang pre-order kaus band; dapatkan 600 PR seketika.'],
            3  => ['title' => 'Gatekeeping Jalur Gig',      'desc' => 'Paksa pemain dengan PR terendah membayar upeti 100 PR ke Anda.'],
            4  => ['title' => 'Podcast Eksploitasi Aib',    'desc' => 'Bongkar rahasia sahabat; raih 450 PR dari kas.'],
            5  => ['title' => 'Drama Settingan Twitter',    'desc' => 'Buat perselisihan fiktif; ambil 100 PR dari seluruh pemain.'],
            6  => ['title' => 'Calo Tiket Ring 1',          'desc' => 'Timbun dan jual tiket VIP konser; dapatkan 500 PR dari kas.'],
            7  => ['title' => 'Rilis Piringan Hitam Terbatas','desc' => 'Cetak piringan hitam terbatas; tarik 150 PR dari seluruh pemain.'],
            8  => ['title' => 'Tepergok Sing-along Pop',    'desc' => 'Ketahuan menyanyikan lagu komersial; reputasi hancur, kurangi 250 PR.'],
            9  => ['title' => 'Sneaker Kolaborasi Cringe',   'desc' => 'Rilis produk gagal; bayar 200 PR ke kas untuk menutupi kerugian produksi.'],
            10 => ['title' => 'Debat Seduh Kopi',           'desc' => 'Berdebat kusir soal suhu air kopi di internet; bayar denda 100 PR.'],
            11 => ['title' => 'Dituduh Nepo Baby',          'desc' => 'Skandal koneksi keluarga terbongkar; kurangi 300 PR.'],
            12 => ['title' => 'Jual Prinsip ke Korporasi',  'desc' => 'Terima sponsor rokok/alkohol besar; dapatkan 600 PR.'],
            13 => ['title' => 'Plagiasi Sampul Album',      'desc' => 'Desain merchandise Anda terbukti menjiplak seniman luar; bayar ganti rugi 350 PR ke kas.'],
            14 => ['title' => 'Headliner Batal Tampil',     'desc' => 'Panggung utama Anda gagal mengundang band luar; bayar denda 100 PR ke setiap pemain.'],
            15 => ['title' => 'Klaim Kurator Skena',        'desc' => 'Dapatkan bonus kurasi 300 PR dari kas.'],
            16 => ['title' => 'Kacamata Frame Rusak',       'desc' => 'Aksesoris utama Anda patah; bayar 100 PR untuk reparasi gaya.'],
            17 => ['title' => 'Duta Bir Murah',             'desc' => 'Ambil 50 PR dari kas untuk tiap properti oranye/merah yang Anda miliki.'],
            18 => ['title' => 'Rombongan Liar',             'desc' => 'Tumpangi bus tur musisi lain; maju langsung ke petak Bandara (Petak 35)!'],
            19 => ['title' => 'Klarifikasi Format PDF',     'desc' => 'Minta maaf secara formal di media sosial; bayar 150 PR untuk menyewa konsultan reputasi.'],
            20 => ['title' => 'FOMO Line-up Bocor',         'desc' => 'Beli tiket festival early bird palsu; kehilangan 200 PR ke kas.'],
        ];
    }

    // ── Inisialisasi Game Skenalitik Baru ──────────────────────────
    public function init_game($room_id, $players) {
        $this->db->table('sk_games')->where('room_id', $room_id)->delete();

        $first_player_id = $players[0]['user_id'];

        $this->db->table('sk_games')->insert([
            'room_id'              => $room_id,
            'current_turn_user_id' => $first_player_id,
            'winner_user_id'       => null,
            'dice_1'               => null,
            'dice_2'               => null,
            'consecutive_doubles'  => 0,
            'pot_tax'              => 0,
            'game_phase'           => 'roll',
            'pending_action'       => null,
            'pending_tile_id'      => null,
            'last_action'          => 'GAME_START',
            'last_action_desc'     => 'Game Skenalitik dimulai! Semua pemain siap di Panggung Utama.',
            'state_version'        => 1,
        ]);

        $game_id = $this->db->last_insert_id();

        // Siapkan Bidak Pemain
        $tokens = self::get_tokens_config();
        $order = 1;
        foreach ($players as $idx => $p) {
            $t = $tokens[$idx % count($tokens)];
            $this->db->table('sk_players')->insert([
                'sk_game_id'        => $game_id,
                'user_id'           => $p['user_id'],
                'token_character'   => $t['name'],
                'token_icon'        => $t['icon'],
                'position'          => 0, // Petak 0: START
                'reputation_points' => 1500, // Modal Awal 1.500 PR
                'is_in_jail'        => 0,
                'jail_turns'        => 0,
                'is_eliminated'     => 0,
                'turn_order'        => $order++,
            ]);
        }

        // Siapkan Properti 40 Petak
        $tiles = self::get_tiles_config();
        foreach ($tiles as $tile_id => $cfg) {
            if (in_array($cfg['type'], ['property', 'transit', 'utility'])) {
                $this->db->table('sk_properties')->insert([
                    'sk_game_id'           => $game_id,
                    'tile_id'              => $tile_id,
                    'owner_user_id'        => null,
                    'building_level'       => 0,
                    'rent_blocked_turns'   => 0,
                    'rent_multiplier_buff' => 1.0,
                ]);
            }
        }

        // Siapkan Dek Kartu Politik & Skena (Acak urutan)
        $politik_indices = range(1, 20);
        shuffle($politik_indices);
        foreach ($politik_indices as $order_idx => $c_idx) {
            $this->db->table('sk_cards')->insert([
                'sk_game_id'     => $game_id,
                'deck_type'      => 'politik',
                'card_index'     => $c_idx,
                'status'         => 'deck',
                'holder_user_id' => null,
                'card_order'     => $order_idx,
            ]);
        }

        $skena_indices = range(1, 20);
        shuffle($skena_indices);
        foreach ($skena_indices as $order_idx => $c_idx) {
            $this->db->table('sk_cards')->insert([
                'sk_game_id'     => $game_id,
                'deck_type'      => 'skena',
                'card_index'     => $c_idx,
                'status'         => 'deck',
                'holder_user_id' => null,
                'card_order'     => $order_idx,
            ]);
        }

        // Catat Log Pembuka
        $this->add_log($game_id, null, 'GAME_START', 'Game Skenalitik resmi dimulai! Putaran pertama dibuka.');

        return $game_id;
    }

    // ── Dapatkan State Version ────────────────────────────────────
    public function get_state_version($room_id) {
        $row = $this->db->query("SELECT state_version FROM sk_games WHERE room_id = ?", [$room_id])->result_array();
        return (int)($row[0]['state_version'] ?? 0);
    }

    private function bump_version($game_id, $room_id) {
        $this->db->query("UPDATE sk_games SET state_version = state_version + 1 WHERE id = ?", [$game_id]);
        $this->db->query("UPDATE game_rooms SET state_version = state_version + 1 WHERE id = ?", [$room_id]);
    }

    // ── Ambil Seluruh Data State untuk Frontend ───────────────────
    public function get_state($room_id, $user_id) {
        $game = $this->db->query("SELECT * FROM sk_games WHERE room_id = ?", [$room_id])->result_array();
        if (empty($game)) return null;
        $game = $game[0];
        $game_id = $game['id'];

        // Pemain
        $raw_players = $this->db->query(
            "SELECT sp.*, u.username, u.avatar 
             FROM sk_players sp
             JOIN users u ON sp.user_id = u.id
             WHERE sp.sk_game_id = ?
             ORDER BY sp.turn_order ASC",
            [$game_id]
        )->result_array();

        $players = [];
        foreach ($raw_players as $p) {
            $is_current = ((int)$game['current_turn_user_id'] === (int)$p['user_id']);
            $is_me = ((int)$p['user_id'] === (int)$user_id);
            $players[] = [
                'id'                => (int)$p['id'],
                'sk_game_id'        => (int)$p['sk_game_id'],
                'user_id'           => (int)$p['user_id'],
                'username'          => $p['username'],
                'avatar'            => $p['avatar'] ?? 'default.png',
                'token_character'   => $p['token_character'],
                'token_icon'        => $p['token_icon'],
                'position'          => (int)$p['position'],
                'reputation_points' => (int)$p['reputation_points'],
                'is_in_jail'        => (int)($p['is_in_jail'] ?? 0) === 1,
                'jail_turns'        => (int)($p['jail_turns'] ?? 0),
                'is_eliminated'     => (int)($p['is_eliminated'] ?? 0) === 1,
                'eliminated_reason' => $p['eliminated_reason'] ?? null,
                'turn_order'        => (int)$p['turn_order'],
                'is_current'        => $is_current,
                'is_me'             => $is_me,
            ];
        }

        // Properti
        $properties = $this->db->query(
            "SELECT sprop.*, u.username AS owner_name, sp.token_icon AS owner_token, sp.turn_order AS owner_order, sp.token_character AS owner_character
             FROM sk_properties sprop
             LEFT JOIN users u ON sprop.owner_user_id = u.id
             LEFT JOIN sk_players sp ON sp.sk_game_id = sprop.sk_game_id AND sp.user_id = sprop.owner_user_id
             WHERE sprop.sk_game_id = ?",
            [$game_id]
        )->result_array();

        $props_by_tile = [];
        foreach ($properties as $pr) {
            $props_by_tile[(int)$pr['tile_id']] = [
                'id'                   => (int)$pr['id'],
                'sk_game_id'           => (int)$pr['sk_game_id'],
                'tile_id'              => (int)$pr['tile_id'],
                'owner_user_id'        => !empty($pr['owner_user_id']) ? (int)$pr['owner_user_id'] : null,
                'building_level'       => (int)($pr['building_level'] ?? 0),
                'rent_blocked_turns'   => (int)($pr['rent_blocked_turns'] ?? 0),
                'rent_multiplier_buff' => (float)($pr['rent_multiplier_buff'] ?? 1.0),
                'owner_name'           => $pr['owner_name'] ?? null,
                'owner_token'          => $pr['owner_token'] ?? null,
                'owner_order'          => !empty($pr['owner_order']) ? (int)$pr['owner_order'] : null,
                'owner_character'      => $pr['owner_character'] ?? null,
            ];
        }

        // Kartu di tangan user ini
        $my_cards = $this->db->query(
            "SELECT * FROM sk_cards WHERE sk_game_id = ? AND holder_user_id = ? AND status = 'hand'",
            [$game_id, $user_id]
        )->result_array();

        $politik_defs = self::get_politik_cards();
        $skena_defs   = self::get_skena_cards();
        $formatted_my_cards = [];
        foreach ($my_cards as $mc) {
            $c_idx     = (int)$mc['card_index'];
            $deck_type = $mc['deck_type'];
            $def       = ($deck_type === 'politik') ? ($politik_defs[$c_idx] ?? []) : ($skena_defs[$c_idx] ?? []);
            $formatted_my_cards[] = [
                'id'         => (int)$mc['id'],
                'card_index' => $c_idx,
                'deck_type'  => $deck_type,
                'title'      => $def['title'] ?? 'Kartu Rahasia',
                'desc'       => $def['desc'] ?? '',
                'icon'       => ($deck_type === 'politik' ? '🏛️' : '⚡'),
                'can_use'    => ($deck_type === 'politik' && in_array($c_idx, [8, 18])),
            ];
        }

        // Logs Terakhir
        $logs = $this->db->query(
            "SELECT sl.*, u.username 
             FROM sk_logs sl 
             LEFT JOIN users u ON sl.user_id = u.id 
             WHERE sl.sk_game_id = ? 
             ORDER BY sl.id DESC LIMIT 15",
            [$game_id]
        )->result_array();

        $is_my_turn = ((int)$game['current_turn_user_id'] === (int)$user_id) && empty($game['winner_user_id']);

        $formatted_game = [
            'id'                   => (int)$game['id'],
            'room_id'              => (int)$game['room_id'],
            'current_turn_user_id' => (int)$game['current_turn_user_id'],
            'winner_user_id'       => !empty($game['winner_user_id']) ? (int)$game['winner_user_id'] : null,
            'dice_1'               => !empty($game['dice_1']) ? (int)$game['dice_1'] : null,
            'dice_2'               => !empty($game['dice_2']) ? (int)$game['dice_2'] : null,
            'consecutive_doubles'  => (int)($game['consecutive_doubles'] ?? 0),
            'pot_tax'              => (int)($game['pot_tax'] ?? 0),
            'game_phase'           => $game['game_phase'],
            'pending_action'       => $game['pending_action'],
            'pending_tile_id'      => $game['pending_tile_id'] !== null ? (int)$game['pending_tile_id'] : null,
            'last_action'          => $game['last_action'],
            'last_action_desc'     => $game['last_action_desc'],
            'state_version'        => (int)($game['state_version'] ?? 1),
        ];

        return [
            'game'         => $formatted_game,
            'is_my_turn'   => $is_my_turn,
            'players'      => $players,
            'properties'   => $props_by_tile,
            'my_cards'     => $formatted_my_cards,
            'logs'         => $logs,
            'tiles_config' => self::get_tiles_config(),
        ];
    }

    // ── Aksi: Lempar Dadu 2x D6 ───────────────────────────────────
    public function roll_dice($room_id, $user_id) {
        $game = $this->db->query("SELECT * FROM sk_games WHERE room_id = ?", [$room_id])->result_array();
        if (empty($game)) return ['ok' => false, 'message' => 'Game tidak ditemukan.'];
        $game = $game[0];
        $game_id = (int)$game['id'];

        if ((int)$game['current_turn_user_id'] !== (int)$user_id) {
            return ['ok' => false, 'message' => 'Bukan giliran Anda!'];
        }
        if ($game['game_phase'] !== 'roll') {
            return ['ok' => false, 'message' => 'Anda sudah melempar dadu, selesaikan aksi giliran Anda!'];
        }

        $player = $this->db->query(
            "SELECT * FROM sk_players WHERE sk_game_id = ? AND user_id = ?",
            [$game_id, $user_id]
        )->result_array();
        if (empty($player)) return ['ok' => false, 'message' => 'Pemain tidak terdaftar.'];
        $player = $player[0];

        if ($player['is_eliminated']) {
            return ['ok' => false, 'message' => 'Anda sudah tereliminasi dari panggung!'];
        }

        // Lempar sepasang dadu (D6)
        $d1 = rand(1, 6);
        $d2 = rand(1, 6);
        $total_dice = $d1 + $d2;
        $is_double = ($d1 === $d2);
        $consecutive_doubles = $is_double ? ((int)$game['consecutive_doubles'] + 1) : 0;

        // Cek jika sedang di Karantina
        if ($player['is_in_jail']) {
            if ($is_double) {
                // Bebas karena dadu kembar!
                $this->db->query(
                    "UPDATE sk_players SET is_in_jail = 0, jail_turns = 0 WHERE id = ?",
                    [$player['id']]
                );
                $this->add_log($game_id, $user_id, 'JAIL_FREE', "Dadu kembar ({$d1} & {$d2})! Bebas dari Karantina Klarifikasi.");
            } else {
                $jail_turns = (int)$player['jail_turns'] + 1;
                if ($jail_turns >= 3) {
                    // Wajib bayar denda 150 PR setelah 3x gagal
                    $new_pr = (int)$player['reputation_points'] - 150;
                    $this->db->query(
                        "UPDATE sk_players SET is_in_jail = 0, jail_turns = 0, reputation_points = ? WHERE id = ?",
                        [$new_pr, $player['id']]
                    );
                    $this->add_log($game_id, $user_id, 'JAIL_PAY', "Sudah 3 putaran di Karantina! Wajib bayar denda 150 PR dan bebas.");
                } else {
                    $this->db->query(
                        "UPDATE sk_players SET jail_turns = ? WHERE id = ?",
                        [$jail_turns, $player['id']]
                    );
                    $this->db->query(
                        "UPDATE sk_games SET dice_1 = ?, dice_2 = ?, consecutive_doubles = 0, game_phase = 'action', last_action = 'JAIL_WAIT', last_action_desc = 'Gagal melempar angka kembar. Tetap berada di Karantina.' WHERE id = ?",
                        [$d1, $d2, $game_id]
                    );
                    $this->add_log($game_id, $user_id, 'JAIL_STAY', "Dadu {$d1}+{$d2} ({$total_dice}). Masih terkurung di Karantina Klarifikasi (Putaran ke-{$jail_turns}).");
                    $this->bump_version($game_id, $room_id);
                    return ['ok' => true, 'd1' => $d1, 'd2' => $d2, 'in_jail' => true, 'new_pos' => (int)$player['position']];
                }
            }
        }

        // Cek Aturan Skandal: 3x Kembar Berturut-turut Masuk Karantina
        if ($consecutive_doubles >= 3) {
            $this->db->query(
                "UPDATE sk_players SET position = 10, is_in_jail = 1, jail_turns = 0 WHERE id = ?",
                [$player['id']]
            );
            $this->db->query(
                "UPDATE sk_games SET dice_1 = ?, dice_2 = ?, consecutive_doubles = 0, game_phase = 'action', last_action = 'GO_TO_JAIL', last_action_desc = 'Dadu kembar 3x berturut-turut! Terjerat Skandal dan masuk Karantina.' WHERE id = ?",
                [$d1, $d2, $game_id]
            );
            $this->add_log($game_id, $user_id, 'GO_TO_JAIL', "Dadu kembar 3 kali ({$d1} & {$d2})! Dituduh main mata, langsung dikirim ke Karantina Klarifikasi!");
            $this->bump_version($game_id, $room_id);
            return ['ok' => true, 'd1' => $d1, 'd2' => $d2, 'sent_to_jail' => true, 'new_pos' => 10];
        }

        // Pergerakan Bidak di Papan (40 Petak)
        $old_pos = (int)$player['position'];
        $new_pos = ($old_pos + $total_dice) % 40;
        $passed_start = ($old_pos + $total_dice >= 40);

        $cur_pr = (int)$player['reputation_points'];
        if ($passed_start) {
            $cur_pr += 200; // Dana Segar Proyek Komunitas (+200 PR)
            $this->add_log($game_id, $user_id, 'PASS_START', "Melewati Petak START! Mengantongi +200 PR (Dana Proyek Komunitas).");
        }

        $this->db->query(
            "UPDATE sk_players SET position = ?, reputation_points = ? WHERE id = ?",
            [$new_pos, $cur_pr, $player['id']]
        );

        // Evaluasi Petak Pendaratan
        $tiles = self::get_tiles_config();
        $tile = $tiles[$new_pos];
        $pending_action = null;
        $pending_tile_id = null;
        $phase = 'action';
        $action_desc = "Mendarat di {$tile['name']}.";

        if ($tile['type'] === 'corner') {
            if ($new_pos === 30) {
                // Terjerat Skandal -> Pindah ke Petak 10
                $this->db->query("UPDATE sk_players SET position = 10, is_in_jail = 1, jail_turns = 0 WHERE id = ?", [$player['id']]);
                $action_desc = "Terjerat Skandal! Langsung digiring ke Karantina Klarifikasi.";
                $this->add_log($game_id, $user_id, 'TERJERAT_SKANDAL', "Mendarat di Terjerat Skandal! Bidak dipindahkan paksa ke Karantina Klarifikasi.");
                $new_pos = 10;
            } elseif ($new_pos === 20) {
                // Parkir VIP -> Ambil Pot Denda
                $pot = (int)$game['pot_tax'];
                if ($pot > 0) {
                    $cur_pr += $pot;
                    $this->db->query("UPDATE sk_players SET reputation_points = ? WHERE id = ?", [$cur_pr, $player['id']]);
                    $this->db->query("UPDATE sk_games SET pot_tax = 0 WHERE id = ?", [$game_id]);
                    $action_desc = "Parkir VIP! Mengambil seluruh uang denda di Kas sebesar +{$pot} PR!";
                    $this->add_log($game_id, $user_id, 'PARKIR_VIP', "Mendarat di Parkir VIP dan mengklaim seluruh pot denda kas sebesar +{$pot} PR!");
                }
            }
        } elseif ($tile['type'] === 'tax') {
            // Bayar Pajak
            $tax_amount = (int)$tile['rent'];
            $cur_pr -= $tax_amount;
            $new_pot = (int)$game['pot_tax'] + $tax_amount;
            $this->db->query("UPDATE sk_players SET reputation_points = ? WHERE id = ?", [$cur_pr, $player['id']]);
            $this->db->query("UPDATE sk_games SET pot_tax = ? WHERE id = ?", [$new_pot, $game_id]);
            $action_desc = "Membayar {$tile['name']} sebesar -{$tax_amount} PR ke Kas.";
            $this->add_log($game_id, $user_id, 'PAY_TAX', "Membayar denda {$tile['name']} sebesar -{$tax_amount} PR.");
        } elseif ($tile['type'] === 'card') {
            // Tarik Kartu Skena / Politik
            $card_res = $this->draw_and_apply_card($game_id, $user_id, $tile['group']);
            $action_desc = "Mengambil kartu {$card_res['title']}: {$card_res['desc']}";
            if (!empty($card_res['teleport_pos'])) {
                $new_pos = (int)$card_res['teleport_pos'];
            }
        } elseif (in_array($tile['type'], ['property', 'transit', 'utility'])) {
            // Cek Kepemilikan Properti
            $prop = $this->db->query(
                "SELECT * FROM sk_properties WHERE sk_game_id = ? AND tile_id = ?",
                [$game_id, $new_pos]
            )->result_array();
            $prop = $prop[0] ?? null;

            if ($prop) {
                if (empty($prop['owner_user_id'])) {
                    // Properti Masih Kosong -> Tawarkan Beli
                    $pending_action = 'buy_property';
                    $pending_tile_id = $new_pos;
                    $action_desc = "Petak {$tile['name']} belum ada pemilik. Ingin membeli seharga {$tile['price']} PR?";
                } elseif ((int)$prop['owner_user_id'] !== (int)$user_id) {
                    // Milik Lawan -> Bayar Sewa!
                    $owner_id = (int)$prop['owner_user_id'];
                    $rent_fee = $this->calculate_rent($game_id, $new_pos, $total_dice);

                    if ($rent_fee > 0) {
                        $cur_pr -= $rent_fee;
                        $this->db->query("UPDATE sk_players SET reputation_points = ? WHERE id = ?", [$cur_pr, $player['id']]);
                        $this->db->query("UPDATE sk_players SET reputation_points = reputation_points + ? WHERE sk_game_id = ? AND user_id = ?", [$rent_fee, $game_id, $owner_id]);
                        
                        $owner_info = $this->db->query("SELECT username FROM users WHERE id = ?", [$owner_id])->result_array();
                        $owner_name = $owner_info[0]['username'] ?? 'Lawan';

                        $action_desc = "Mendarat di {$tile['name']} milik {$owner_name}! Membayar sewa sebesar -{$rent_fee} PR.";
                        $this->add_log($game_id, $user_id, 'PAY_RENT', "Membayar uang sewa sebesar {$rent_fee} PR kepada {$owner_name} di {$tile['name']}.");
                    } else {
                        $action_desc = "Petak {$tile['name']} sedang diblokir izin keramaiannya! Bebas sewa.";
                    }
                } else {
                    // Milik Sendiri! ((int)$prop['owner_user_id'] === (int)$user_id)
                    if ($tile['type'] === 'property') {
                        $cur_level = (int)$prop['building_level'];
                        if ($cur_level < 3) {
                            $next_level = $cur_level + 1;
                            $upgrade_cost = (int)($tile['upgrade_cost'] ?? 100);
                            $pending_action = 'upgrade_property';
                            $pending_tile_id = $new_pos;
                            $lvl_names = [1 => 'Level 1 (Sablon Gelap)', 2 => 'Level 2 (Ruang Kreatif Elit)', 3 => 'Level 3 (Korporasi Media)'];
                            $target_name = $lvl_names[$next_level] ?? "Level {$next_level}";
                            $action_desc = "Mendarat di {$tile['name']} milik sendiri! Ingin bangun {$target_name} seharga {$upgrade_cost} PR?";
                        } else {
                            $action_desc = "Mendarat di {$tile['name']} milik sendiri! Bangunan sudah di Level Maksimal (Level 3 - Korporasi Media).";
                        }
                    } else {
                        $action_desc = "Mendarat di {$tile['name']} milik sendiri.";
                    }
                }
            }
        }

        // Update Sesi Game
        $this->db->query(
            "UPDATE sk_games SET dice_1 = ?, dice_2 = ?, consecutive_doubles = ?, game_phase = ?, pending_action = ?, pending_tile_id = ?, last_action = 'ROLL', last_action_desc = ? WHERE id = ?",
            [$d1, $d2, $consecutive_doubles, $phase, $pending_action, $pending_tile_id, $action_desc, $game_id]
        );

        $this->bump_version($game_id, $room_id);

        return [
            'ok'             => true,
            'd1'             => $d1,
            'd2'             => $d2,
            'is_double'      => $is_double,
            'new_pos'        => $new_pos,
            'pending_action' => $pending_action,
            'pending_tile_id'=> $pending_tile_id,
            'message'        => $action_desc,
            'drawn_card'     => $card_res ?? null,
        ];
    }

    // ── Kalkulasi Sewa Skenalitik ─────────────────────────────────
    public function calculate_rent($game_id, $tile_id, $dice_roll = 7) {
        $tiles = self::get_tiles_config();
        $tile = $tiles[$tile_id] ?? null;
        if (!$tile) return 0;

        $prop = $this->db->query("SELECT * FROM sk_properties WHERE sk_game_id = ? AND tile_id = ?", [$game_id, $tile_id])->result_array();
        if (empty($prop) || empty($prop[0]['owner_user_id'])) return 0;
        $prop = $prop[0];

        // Jika izin keramaian sedang dicabut
        if ((int)$prop['rent_blocked_turns'] > 0) return 0;

        $owner_id = (int)$prop['owner_user_id'];
        $base_rent = (int)$tile['rent'];
        $level = (int)$prop['building_level'];
        $mult_buff = (float)$prop['rent_multiplier_buff'];

        if ($tile['type'] === 'property') {
            if ($level === 1) {
                $rent = $base_rent * 5;  // Level 1: Sablon Gelap (5x)
            } elseif ($level === 2) {
                $rent = $base_rent * 15; // Level 2: Ruang Elit (15x)
            } elseif ($level === 3) {
                $rent = $base_rent * 40; // Level 3: Korporasi Media (40x)
            } else {
                // Cek Monopoli Warna (Tanah Kosong tapi punya satu grup)
                $groups = self::get_color_groups();
                $group_tiles = $groups[$tile['group']] ?? [];
                $owns_all = true;
                foreach ($group_tiles as $gt_id) {
                    $other = $this->db->query("SELECT owner_user_id FROM sk_properties WHERE sk_game_id = ? AND tile_id = ?", [$game_id, $gt_id])->result_array();
                    if (empty($other) || (int)($other[0]['owner_user_id'] ?? 0) !== $owner_id) {
                        $owns_all = false;
                        break;
                    }
                }
                $rent = $owns_all ? ($base_rent * 2) : $base_rent;
            }
        } elseif ($tile['type'] === 'transit') {
            // Skala jumlah transit yang dimiliki
            $transit_tiles = [5, 15, 25, 35];
            $count = 0;
            foreach ($transit_tiles as $tt_id) {
                $other = $this->db->query("SELECT owner_user_id FROM sk_properties WHERE sk_game_id = ? AND tile_id = ?", [$game_id, $tt_id])->result_array();
                if (!empty($other) && (int)($other[0]['owner_user_id'] ?? 0) === $owner_id) {
                    $count++;
                }
            }
            $scale = [1 => 25, 2 => 50, 3 => 100, 4 => 200];
            $rent = $scale[$count] ?? 25;
        } elseif ($tile['type'] === 'utility') {
            // Skala utilitas
            $util_tiles = [12, 28];
            $count = 0;
            foreach ($util_tiles as $ut_id) {
                $other = $this->db->query("SELECT owner_user_id FROM sk_properties WHERE sk_game_id = ? AND tile_id = ?", [$game_id, $ut_id])->result_array();
                if (!empty($other) && (int)($other[0]['owner_user_id'] ?? 0) === $owner_id) {
                    $count++;
                }
            }
            $multiplier = ($count >= 2) ? 10 : 4;
            $rent = $dice_roll * $multiplier;
        } else {
            $rent = $base_rent;
        }

        return (int)round($rent * $mult_buff);
    }

    // ── Tarik & Eksekusi Kartu Aksi ───────────────────────────────
    public function draw_and_apply_card($game_id, $user_id, $deck_type) {
        $card = $this->db->query(
            "SELECT * FROM sk_cards WHERE sk_game_id = ? AND deck_type = ? AND status = 'deck' ORDER BY card_order ASC LIMIT 1",
            [$game_id, $deck_type]
        )->result_array();

        if (empty($card)) {
            // Reshuffle discard deck jika habis
            $this->db->query(
                "UPDATE sk_cards SET status = 'deck' WHERE sk_game_id = ? AND deck_type = ? AND status = 'discard'",
                [$game_id, $deck_type]
            );
            $card = $this->db->query(
                "SELECT * FROM sk_cards WHERE sk_game_id = ? AND deck_type = ? AND status = 'deck' ORDER BY RAND() LIMIT 1",
                [$game_id, $deck_type]
            )->result_array();
        }

        if (empty($card)) {
            return ['title' => 'Kartu Kosong', 'desc' => 'Tidak ada efek.', 'deck_type' => $deck_type, 'card_index' => 0, 'is_kept' => false];
        }

        $card = $card[0];
        $c_idx = (int)$card['card_index'];
        $defs = ($deck_type === 'politik') ? self::get_politik_cards() : self::get_skena_cards();
        $def = $defs[$c_idx] ?? ['title' => 'Kartu Rahasia', 'desc' => 'Efek misterius.'];
        $def['deck_type'] = $deck_type;
        $def['card_index'] = $c_idx;
        $def['icon'] = ($deck_type === 'politik') ? '🏛️' : '⚡';

        $player = $this->db->query("SELECT * FROM sk_players WHERE sk_game_id = ? AND user_id = ?", [$game_id, $user_id])->result_array();
        $player = $player[0];
        $cur_pr = (int)$player['reputation_points'];

        // Kartu yang disimpan di tangan (Kartu Politik #8 Intervensi Hakim & #18 Pembungkaman Saksi)
        if ($deck_type === 'politik' && in_array($c_idx, [8, 18])) {
            $this->db->query("UPDATE sk_cards SET status = 'hand', holder_user_id = ? WHERE id = ?", [$user_id, $card['id']]);
            $this->add_log($game_id, $user_id, 'DRAW_CARD', "Mendapatkan kartu sakti '{$def['title']}' dan menyimpannya di tangan!");
            $def['is_kept'] = true;
            return $def;
        }

        $def['is_kept'] = false;

        // Eksekusi Efek Kartu Skena (20 Kartu)
        if ($deck_type === 'skena') {
            switch ($c_idx) {
                case 1: // Bajak Royalti: tarik 150 dari setiap pemain di meja
                    $all = $this->db->query("SELECT * FROM sk_players WHERE sk_game_id = ? AND user_id != ? AND is_eliminated = 0", [$game_id, $user_id])->result_array();
                    $total = 0;
                    foreach ($all as $other) {
                        $this->db->query("UPDATE sk_players SET reputation_points = reputation_points - 150 WHERE id = ?", [$other['id']]);
                        $total += 150;
                    }
                    $cur_pr += $total;
                    break;
                case 2: // Kolektif Tipu-Tipu: +600 PR
                    $cur_pr += 600;
                    break;
                case 3: // Gatekeeping Jalur Gig: Tarik 100 PR dari pemain dengan PR terendah
                    $lowest = $this->db->query("SELECT * FROM sk_players WHERE sk_game_id = ? AND user_id != ? AND is_eliminated = 0 ORDER BY reputation_points ASC LIMIT 1", [$game_id, $user_id])->result_array();
                    if (!empty($lowest)) {
                        $this->db->query("UPDATE sk_players SET reputation_points = reputation_points - 100 WHERE id = ?", [$lowest[0]['id']]);
                        $cur_pr += 100;
                    }
                    break;
                case 4: // Podcast Aib: +450 PR dari kas
                    $cur_pr += 450;
                    break;
                case 5: // Drama Settingan: +100 dari tiap pemain
                    $all = $this->db->query("SELECT * FROM sk_players WHERE sk_game_id = ? AND user_id != ? AND is_eliminated = 0", [$game_id, $user_id])->result_array();
                    $total = 0;
                    foreach ($all as $other) {
                        $this->db->query("UPDATE sk_players SET reputation_points = reputation_points - 100 WHERE id = ?", [$other['id']]);
                        $total += 100;
                    }
                    $cur_pr += $total;
                    break;
                case 6: // Calo Tiket: +500 PR dari kas
                    $cur_pr += 500;
                    break;
                case 7: // Piringan Hitam: +150 dari semua pemain
                    $all = $this->db->query("SELECT * FROM sk_players WHERE sk_game_id = ? AND user_id != ? AND is_eliminated = 0", [$game_id, $user_id])->result_array();
                    $total = 0;
                    foreach ($all as $other) {
                        $this->db->query("UPDATE sk_players SET reputation_points = reputation_points - 150 WHERE id = ?", [$other['id']]);
                        $total += 150;
                    }
                    $cur_pr += $total;
                    break;
                case 8: // Sing-along Pop: -250 PR
                    $cur_pr -= 250;
                    break;
                case 9: // Sneaker Cringe: -200 PR ke kas
                    $cur_pr -= 200;
                    $this->db->query("UPDATE sk_games SET pot_tax = pot_tax + 200 WHERE id = ?", [$game_id]);
                    break;
                case 10: // Debat Seduh Kopi: -100 PR ke kas
                    $cur_pr -= 100;
                    $this->db->query("UPDATE sk_games SET pot_tax = pot_tax + 100 WHERE id = ?", [$game_id]);
                    break;
                case 11: // Nepo Baby: -300 PR
                    $cur_pr -= 300;
                    break;
                case 12: // Jual Prinsip: +600 PR
                    $cur_pr += 600;
                    break;
                case 13: // Plagiasi: -350 PR ke kas
                    $cur_pr -= 350;
                    $this->db->query("UPDATE sk_games SET pot_tax = pot_tax + 350 WHERE id = ?", [$game_id]);
                    break;
                case 14: // Headliner Batal: -100 ke tiap pemain
                    $all = $this->db->query("SELECT * FROM sk_players WHERE sk_game_id = ? AND user_id != ? AND is_eliminated = 0", [$game_id, $user_id])->result_array();
                    foreach ($all as $other) {
                        $this->db->query("UPDATE sk_players SET reputation_points = reputation_points + 100 WHERE id = ?", [$other['id']]);
                        $cur_pr -= 100;
                    }
                    break;
                case 15: // Kurator: +300 PR dari kas
                    $cur_pr += 300;
                    break;
                case 16: // Kacamata Patah: -100 PR ke kas
                    $cur_pr -= 100;
                    $this->db->query("UPDATE sk_games SET pot_tax = pot_tax + 100 WHERE id = ?", [$game_id]);
                    break;
                case 17: // Duta Bir: Ambil 50 PR untuk tiap properti oranye/merah yang dimiliki
                    $props = $this->db->query("SELECT COUNT(*) AS total FROM sk_properties WHERE sk_game_id = ? AND owner_user_id = ? AND tile_id IN (16, 18, 19, 21, 23, 24)", [$game_id, $user_id])->result_array();
                    $beer_props = (int)($props[0]['total'] ?? 0);
                    $bonus = max(50, $beer_props * 50);
                    $cur_pr += $bonus;
                    break;
                case 18: // Rombongan Liar: Maju langsung ke petak Bandara (Petak 35)
                    $this->db->query("UPDATE sk_players SET position = 35 WHERE id = ?", [$player['id']]);
                    $def['teleport_pos'] = 35;
                    break;
                case 19: // Klarifikasi PDF: -150 PR ke kas
                    $cur_pr -= 150;
                    $this->db->query("UPDATE sk_games SET pot_tax = pot_tax + 150 WHERE id = ?", [$game_id]);
                    break;
                case 20: // FOMO Line-up: -200 PR ke kas
                    $cur_pr -= 200;
                    $this->db->query("UPDATE sk_games SET pot_tax = pot_tax + 200 WHERE id = ?", [$game_id]);
                    break;
            }
        } else {
            // Eksekusi Efek Kartu Politik (20 Kartu)
            switch ($c_idx) {
                case 1: // Gunting Lipatan: Rampas 300 PR dari pemain di sebelah kanan Anda
                    $turn_order = (int)$player['turn_order'];
                    $right_player = $this->db->query(
                        "SELECT * FROM sk_players WHERE sk_game_id = ? AND user_id != ? AND is_eliminated = 0 ORDER BY CASE WHEN turn_order < ? THEN turn_order ELSE turn_order - 100 END DESC LIMIT 1",
                        [$game_id, $user_id, $turn_order]
                    )->result_array();
                    if (empty($right_player)) {
                        $right_player = $this->db->query("SELECT * FROM sk_players WHERE sk_game_id = ? AND user_id != ? AND is_eliminated = 0 ORDER BY reputation_points DESC LIMIT 1", [$game_id, $user_id])->result_array();
                    }
                    if (!empty($right_player)) {
                        $this->db->query("UPDATE sk_players SET reputation_points = reputation_points - 300 WHERE id = ?", [$right_player[0]['id']]);
                        $cur_pr += 300;
                    }
                    break;
                case 2: // Kudeta Aset: Rebut 1 properti cokelat/biru muda milik lawan dengan level/harga tertinggi
                    $target_prop = $this->db->query(
                        "SELECT * FROM sk_properties WHERE sk_game_id = ? AND tile_id IN (1, 3, 6, 8, 9) AND owner_user_id IS NOT NULL AND owner_user_id != ? ORDER BY building_level DESC, tile_id DESC LIMIT 1",
                        [$game_id, $user_id]
                    )->result_array();
                    if (!empty($target_prop)) {
                        $this->db->query("UPDATE sk_properties SET owner_user_id = ? WHERE id = ?", [$user_id, $target_prop[0]['id']]);
                        $this->add_log($game_id, $user_id, 'KUDETA', "Kudeta Aset! Merebut paksa petak nomor {$target_prop[0]['tile_id']}!");
                    }
                    break;
                case 3: // OTT Rekayasa: Kirim 1 lawan ke Karantina
                    $target = $this->db->query("SELECT * FROM sk_players WHERE sk_game_id = ? AND user_id != ? AND is_eliminated = 0 ORDER BY reputation_points DESC LIMIT 1", [$game_id, $user_id])->result_array();
                    if (!empty($target)) {
                        $this->db->query("UPDATE sk_players SET position = 10, is_in_jail = 1, jail_turns = 0 WHERE id = ?", [$target[0]['id']]);
                        $target_user_info = $this->db->query("SELECT username FROM users WHERE id = ?", [$target[0]['user_id']])->result_array();
                        $t_name = $target_user_info[0]['username'] ?? 'Lawan';
                        $this->add_log($game_id, $user_id, 'OTT', "OTT Rekayasa! Mengirim {$t_name} langsung ke Karantina Klarifikasi!");
                    }
                    break;
                case 4: // Ijazah Palsu: Target rugi 350 masuk ke saldo pemain
                    $richest = $this->db->query("SELECT * FROM sk_players WHERE sk_game_id = ? AND user_id != ? AND is_eliminated = 0 ORDER BY reputation_points DESC LIMIT 1", [$game_id, $user_id])->result_array();
                    if (!empty($richest)) {
                        $this->db->query("UPDATE sk_players SET reputation_points = reputation_points - 350 WHERE id = ?", [$richest[0]['id']]);
                        $cur_pr += 350;
                    }
                    break;
                case 5: // Cabut Izin Keramaian: Dilarang menarik sewa 2 putaran
                    $opp_prop = $this->db->query(
                        "SELECT * FROM sk_properties WHERE sk_game_id = ? AND owner_user_id IS NOT NULL AND owner_user_id != ? ORDER BY building_level DESC, tile_id DESC LIMIT 1",
                        [$game_id, $user_id]
                    )->result_array();
                    if (!empty($opp_prop)) {
                        $this->db->query("UPDATE sk_properties SET rent_blocked_turns = 2 WHERE id = ?", [$opp_prop[0]['id']]);
                        $this->add_log($game_id, $user_id, 'BLOCKED_RENT', "Cabut Izin Keramaian! Petak #{$opp_prop[0]['tile_id']} dilarang menarik uang sewa selama 2 putaran.");
                    }
                    break;
                case 6: // Buzzer: Bayar 150 potong 500 pemain terkaya
                    $cur_pr -= 150;
                    $this->db->query("UPDATE sk_games SET pot_tax = pot_tax + 150 WHERE id = ?", [$game_id]);
                    $richest = $this->db->query("SELECT * FROM sk_players WHERE sk_game_id = ? AND user_id != ? AND is_eliminated = 0 ORDER BY reputation_points DESC LIMIT 1", [$game_id, $user_id])->result_array();
                    if (!empty($richest)) {
                        $this->db->query("UPDATE sk_players SET reputation_points = reputation_points - 500 WHERE id = ?", [$richest[0]['id']]);
                    }
                    break;
                case 7: // Bansos Dobel: +400 PR
                    $cur_pr += 400;
                    break;
                case 9: // Kriminalisasi Rival: Target bayar 200 PR atau masuk Karantina
                    $target = $this->db->query("SELECT * FROM sk_players WHERE sk_game_id = ? AND user_id != ? AND is_eliminated = 0 ORDER BY reputation_points DESC LIMIT 1", [$game_id, $user_id])->result_array();
                    if (!empty($target)) {
                        if ((int)$target[0]['reputation_points'] >= 200) {
                            $this->db->query("UPDATE sk_players SET reputation_points = reputation_points - 200 WHERE id = ?", [$target[0]['id']]);
                            $cur_pr += 200;
                            $this->add_log($game_id, $user_id, 'KRIMINALISASI', "Kriminalisasi Rival: Target membayar upeti 200 PR kepada Anda.");
                        } else {
                            $this->db->query("UPDATE sk_players SET position = 10, is_in_jail = 1, jail_turns = 0 WHERE id = ?", [$target[0]['id']]);
                            $this->add_log($game_id, $user_id, 'KRIMINALISASI', "Kriminalisasi Rival: Target tidak mampu membayar 200 PR dan langsung dijebloskan ke Karantina!");
                        }
                    }
                    break;
                case 10: // Lobi Senayan: Buff multiplier sewa properti
                    $this->db->query("UPDATE sk_properties SET rent_multiplier_buff = 2.0 WHERE sk_game_id = ? AND owner_user_id = ?", [$game_id, $user_id]);
                    break;
                case 11: // Pajak Ekosistem: Tarik 100 dari tiap pemain yang punya properti
                    $all_owners = $this->db->query("SELECT DISTINCT owner_user_id FROM sk_properties WHERE sk_game_id = ? AND owner_user_id IS NOT NULL AND owner_user_id != ?", [$game_id, $user_id])->result_array();
                    $total = 0;
                    foreach ($all_owners as $ow) {
                        $this->db->query("UPDATE sk_players SET reputation_points = reputation_points - 100 WHERE sk_game_id = ? AND user_id = ?", [$game_id, $ow['owner_user_id']]);
                        $total += 100;
                    }
                    $cur_pr += $total;
                    break;
                case 12: // Isu SARA: Seluruh lawan mundur 2 langkah
                    $opponents = $this->db->query("SELECT * FROM sk_players WHERE sk_game_id = ? AND user_id != ? AND is_eliminated = 0", [$game_id, $user_id])->result_array();
                    foreach ($opponents as $op) {
                        $new_op_pos = ((int)$op['position'] - 2 + 40) % 40;
                        $this->db->query("UPDATE sk_players SET position = ? WHERE id = ?", [$new_op_pos, $op['id']]);
                    }
                    break;
                case 13: // Staf Khusus: +300 PR
                case 16: // Tender Laptop: +500 PR
                case 17: // Komisaris: +350 PR
                    $add = ($c_idx === 16) ? 500 : (($c_idx === 17) ? 350 : 300);
                    $cur_pr += $add;
                    break;
                case 14: // Audit Fiktif: Paksa 1 pemain membayar 50 PR per unit bangunan
                    $target = $this->db->query("SELECT * FROM sk_players WHERE sk_game_id = ? AND user_id != ? AND is_eliminated = 0 ORDER BY reputation_points DESC LIMIT 1", [$game_id, $user_id])->result_array();
                    if (!empty($target)) {
                        $bld_count = $this->db->query("SELECT SUM(building_level) as total FROM sk_properties WHERE sk_game_id = ? AND owner_user_id = ?", [$game_id, $target[0]['user_id']])->result_array();
                        $units = max(1, (int)($bld_count[0]['total'] ?? 1));
                        $fee = $units * 50;
                        $this->db->query("UPDATE sk_players SET reputation_points = reputation_points - ? WHERE id = ?", [$fee, $target[0]['id']]);
                        $cur_pr += $fee;
                        $this->add_log($game_id, $user_id, 'AUDIT_FIKTIF', "Audit Fiktif! Memaksa target membayar denda {$fee} PR untuk {$units} unit bangunan.");
                    }
                    break;
                case 15: // Serangan Siber DDoS: Lumpuhkan utilitas lawan (petak 12 & 28)
                    $this->db->query("UPDATE sk_properties SET rent_blocked_turns = 2 WHERE sk_game_id = ? AND tile_id IN (12, 28) AND owner_user_id IS NOT NULL AND owner_user_id != ?", [$game_id, $user_id]);
                    $this->add_log($game_id, $user_id, 'DDOS', "Serangan Siber DDoS! Seluruh utilitas lawan dilumpuhkan dari menarik sewa selama 2 putaran.");
                    break;
                case 19: // Tukar Kasus: Jika di penjara, pindah ke lawan termiskin
                    if ($player['is_in_jail']) {
                        $lowest = $this->db->query("SELECT * FROM sk_players WHERE sk_game_id = ? AND user_id != ? AND is_eliminated = 0 ORDER BY reputation_points ASC LIMIT 1", [$game_id, $user_id])->result_array();
                        if (!empty($lowest)) {
                            $this->db->query("UPDATE sk_players SET is_in_jail = 0, jail_turns = 0 WHERE id = ?", [$player['id']]);
                            $this->db->query("UPDATE sk_players SET is_in_jail = 1, jail_turns = 0, position = 10 WHERE id = ?", [$lowest[0]['id']]);
                            $this->add_log($game_id, $user_id, 'TUKAR_KASUS', "Tukar Kasus! Status Karantina berhasil dialihkan ke lawan.");
                        }
                    } else {
                        $cur_pr += 200;
                    }
                    break;
                case 20: // Blackmail Politik: Intip kartu tangan lawan & buang 1
                    $opp_card = $this->db->query("SELECT * FROM sk_cards WHERE sk_game_id = ? AND holder_user_id != ? AND status = 'hand' LIMIT 1", [$game_id, $user_id])->result_array();
                    if (!empty($opp_card)) {
                        $this->db->query("UPDATE sk_cards SET status = 'discard', holder_user_id = NULL WHERE id = ?", [$opp_card[0]['id']]);
                        $this->add_log($game_id, $user_id, 'BLACKMAIL', "Blackmail Politik! Berhasil membocorkan dan membuang 1 kartu tangan lawan ke discard.");
                    } else {
                        $cur_pr += 200;
                    }
                    break;
            }
        }

        $this->db->query("UPDATE sk_players SET reputation_points = ? WHERE id = ?", [$cur_pr, $player['id']]);
        $this->db->query("UPDATE sk_cards SET status = 'discard' WHERE id = ?", [$card['id']]);
        $this->add_log($game_id, $user_id, 'DRAW_CARD', "Memainkan kartu '{$def['title']}': {$def['desc']}");

        return $def;
    }

    // ── Aksi: Beli Properti ───────────────────────────────────────
    public function buy_property($room_id, $user_id) {
        $game = $this->db->query("SELECT * FROM sk_games WHERE room_id = ?", [$room_id])->result_array();
        if (empty($game)) return ['ok' => false, 'message' => 'Game tidak ditemukan.'];
        $game = $game[0];
        $game_id = (int)$game['id'];

        if ((int)$game['current_turn_user_id'] !== (int)$user_id) {
            return ['ok' => false, 'message' => 'Bukan giliran Anda!'];
        }
        if ($game['pending_action'] !== 'buy_property' || $game['pending_tile_id'] === null) {
            return ['ok' => false, 'message' => 'Tidak ada properti yang dapat dibeli saat ini.'];
        }

        $tile_id = (int)$game['pending_tile_id'];
        $tiles = self::get_tiles_config();
        $tile = $tiles[$tile_id] ?? null;
        if (!$tile) return ['ok' => false, 'message' => 'Petak tidak valid.'];

        $prop = $this->db->query("SELECT * FROM sk_properties WHERE sk_game_id = ? AND tile_id = ?", [$game_id, $tile_id])->result_array();
        if (empty($prop) || !empty($prop[0]['owner_user_id'])) {
            return ['ok' => false, 'message' => 'Properti sudah memiliki pemilik!'];
        }

        $player = $this->db->query("SELECT * FROM sk_players WHERE sk_game_id = ? AND user_id = ?", [$game_id, $user_id])->result_array();
        $player = $player[0];
        $price = (int)$tile['price'];

        // Cek saldo & batas utang: saldo setelah beli tidak boleh lebih parah dari -300 PR
        if (((int)$player['reputation_points'] - $price) < -300) {
            return ['ok' => false, 'message' => 'Saldo PR tidak mencukupi untuk membeli petak ini (melebihi batas utang -300 PR)!'];
        }

        // Eksekusi Beli
        $new_pr = (int)$player['reputation_points'] - $price;
        $this->db->query("UPDATE sk_players SET reputation_points = ? WHERE id = ?", [$new_pr, $player['id']]);
        $this->db->query("UPDATE sk_properties SET owner_user_id = ? WHERE id = ?", [$user_id, $prop[0]['id']]);
        $this->db->query("UPDATE sk_games SET pending_action = NULL, pending_tile_id = NULL, last_action = 'BUY', last_action_desc = ? WHERE id = ?", ["Membeli petak {$tile['name']} seharga {$price} PR.", $game_id]);

        $this->add_log($game_id, $user_id, 'BUY_PROPERTY', "Membeli hak milik '{$tile['name']}' seharga {$price} PR!");
        $this->bump_version($game_id, $room_id);

        return ['ok' => true, 'tile_id' => $tile_id, 'new_pr' => $new_pr, 'message' => "Berhasil membeli {$tile['name']}!"];
    }

    // ── Aksi: Lewati Beli Properti ────────────────────────────────
    public function skip_buy($room_id, $user_id) {
        $game = $this->db->query("SELECT * FROM sk_games WHERE room_id = ?", [$room_id])->result_array();
        if (empty($game)) return ['ok' => false, 'message' => 'Game tidak ditemukan.'];
        $game = $game[0];
        $game_id = (int)$game['id'];

        if ((int)$game['current_turn_user_id'] !== (int)$user_id) {
            return ['ok' => false, 'message' => 'Bukan giliran Anda!'];
        }

        $this->db->query("UPDATE sk_games SET pending_action = NULL, pending_tile_id = NULL WHERE id = ?", [$game_id]);
        $this->bump_version($game_id, $room_id);

        return ['ok' => true];
    }

    // ── Aksi: Upgrade Bangunan (Sablon Gelap / Ruang Elit / Korporasi Media) ─
    public function upgrade_property($room_id, $user_id, $tile_id, $target_level = 0) {
        $game = $this->db->query("SELECT * FROM sk_games WHERE room_id = ?", [$room_id])->result_array();
        if (empty($game)) return ['ok' => false, 'message' => 'Game tidak ditemukan.'];
        $game = $game[0];
        $game_id = (int)$game['id'];

        if ((int)$game['current_turn_user_id'] !== (int)$user_id) {
            return ['ok' => false, 'message' => 'Bukan giliran Anda!'];
        }

        // Pemain hanya bisa upgrade jika mendarat di petak miliknya sendiri setelah berkeliling (bukan saat baru beli)
        if (($game['pending_action'] ?? '') !== 'upgrade_property' || (int)($game['pending_tile_id'] ?? -1) !== (int)$tile_id) {
            return ['ok' => false, 'message' => 'Anda baru bisa membangun properti jika kembali mendarat di petak milik Anda sendiri setelah berkeliling papan!'];
        }

        $tiles = self::get_tiles_config();
        $tile = $tiles[$tile_id] ?? null;
        if (!$tile || $tile['type'] !== 'property') {
            return ['ok' => false, 'message' => 'Petak ini bukan properti yang bisa dibangun.'];
        }

        $prop = $this->db->query("SELECT * FROM sk_properties WHERE sk_game_id = ? AND tile_id = ?", [$game_id, $tile_id])->result_array();
        if (empty($prop) || (int)$prop[0]['owner_user_id'] !== (int)$user_id) {
            return ['ok' => false, 'message' => 'Anda bukan pemilik properti ini.'];
        }
        $prop = $prop[0];

        $cur_level = (int)$prop['building_level'];
        if ($cur_level >= 3) {
            return ['ok' => false, 'message' => 'Properti ini sudah mencapai Level Maksimal (Korporasi Media)!'];
        }

        // Tentukan target level (1, 2, atau 3)
        $target_level = (int)$target_level;
        if ($target_level <= $cur_level || $target_level > 3) {
            $target_level = $cur_level + 1;
        }

        $levels_to_add = $target_level - $cur_level;
        $cost_per_level = (int)($tile['upgrade_cost'] ?? 100);
        $total_cost = $levels_to_add * $cost_per_level;

        $player = $this->db->query("SELECT * FROM sk_players WHERE sk_game_id = ? AND user_id = ?", [$game_id, $user_id])->result_array();
        $player = $player[0];

        if (((int)$player['reputation_points'] - $total_cost) < -300) {
            return ['ok' => false, 'message' => "Saldo PR tidak mencukupi untuk bangun ke Level {$target_level} (biaya {$total_cost} PR, melebihi batas utang -300 PR)!"];
        }

        $new_level = $target_level;
        $level_names = [1 => 'Level 1 (Sablon Gelap)', 2 => 'Level 2 (Ruang Kreatif Elit)', 3 => 'Level 3 (Korporasi Media)'];
        $lvl_name = $level_names[$new_level];

        $new_pr = (int)$player['reputation_points'] - $total_cost;
        $this->db->query("UPDATE sk_players SET reputation_points = ? WHERE id = ?", [$new_pr, $player['id']]);
        $this->db->query("UPDATE sk_properties SET building_level = ? WHERE id = ?", [$new_level, $prop['id']]);
        $this->db->query("UPDATE sk_games SET pending_action = NULL, pending_tile_id = NULL, last_action = 'UPGRADE', last_action_desc = ? WHERE id = ?", ["Upgrade {$tile['name']} ke {$lvl_name} seharga {$total_cost} PR.", $game_id]);

        $this->add_log($game_id, $user_id, 'UPGRADE', "Upgrade properti {$tile['name']} ke {$lvl_name} seharga {$total_cost} PR!");
        $this->bump_version($game_id, $room_id);

        return ['ok' => true, 'tile_id' => $tile_id, 'new_level' => $new_level, 'lvl_name' => $lvl_name, 'new_pr' => $new_pr, 'message' => "Sukses upgrade {$tile['name']} ke {$lvl_name}!"];
    }

    // ── Aksi: Bebas Karantina dengan Denda 150 PR atau Kartu Sakti 
    public function exit_jail($room_id, $user_id, $method = 'fine') {
        $game = $this->db->query("SELECT * FROM sk_games WHERE room_id = ?", [$room_id])->result_array();
        if (empty($game)) return ['ok' => false, 'message' => 'Game tidak ditemukan.'];
        $game = $game[0];
        $game_id = (int)$game['id'];

        if ((int)$game['current_turn_user_id'] !== (int)$user_id) {
            return ['ok' => false, 'message' => 'Bukan giliran Anda!'];
        }

        $player = $this->db->query("SELECT * FROM sk_players WHERE sk_game_id = ? AND user_id = ?", [$game_id, $user_id])->result_array();
        $player = $player[0];

        if (!$player['is_in_jail']) {
            return ['ok' => false, 'message' => 'Anda tidak sedang berada di Karantina Klarifikasi.'];
        }

        if ($method === 'card') {
            // Gunakan kartu Intervensi Hakim
            $card = $this->db->query(
                "SELECT * FROM sk_cards WHERE sk_game_id = ? AND holder_user_id = ? AND deck_type = 'politik' AND card_index = 8 AND status = 'hand' LIMIT 1",
                [$game_id, $user_id]
            )->result_array();

            if (empty($card)) {
                return ['ok' => false, 'message' => 'Anda tidak memiliki kartu Intervensi Hakim!'];
            }

            $this->db->query("UPDATE sk_cards SET status = 'discard', holder_user_id = NULL WHERE id = ?", [$card[0]['id']]);
            $this->db->query("UPDATE sk_players SET is_in_jail = 0, jail_turns = 0 WHERE id = ?", [$player['id']]);
            $this->add_log($game_id, $user_id, 'JAIL_CARD', "Memainkan kartu 'Intervensi Hakim'! Bebas dari Karantina tanpa denda.");
        } else {
            // Bayar denda 150 PR
            $new_pr = (int)$player['reputation_points'] - 150;
            $this->db->query("UPDATE sk_players SET is_in_jail = 0, jail_turns = 0, reputation_points = ? WHERE id = ?", [$new_pr, $player['id']]);
            $this->add_log($game_id, $user_id, 'JAIL_FINE', "Membayar denda 150 PR dan resmi keluar dari Karantina Klarifikasi.");
        }

        $this->bump_version($game_id, $room_id);
        return ['ok' => true, 'message' => 'Bebas dari Karantina! Anda kini bisa melangkah.'];
    }

    // ── Aksi: Mainkan Kartu dari Tangan ───────────────────────────
    public function use_hand_card($room_id, $user_id, $card_id) {
        $game = $this->db->query("SELECT * FROM sk_games WHERE room_id = ?", [$room_id])->result_array();
        if (empty($game)) return ['ok' => false, 'message' => 'Game tidak ditemukan.'];
        $game = $game[0];
        $game_id = (int)$game['id'];

        $card = $this->db->query(
            "SELECT * FROM sk_cards WHERE id = ? AND sk_game_id = ? AND holder_user_id = ? AND status = 'hand'",
            [$card_id, $game_id, $user_id]
        )->result_array();
        if (empty($card)) {
            return ['ok' => false, 'message' => 'Kartu tidak ditemukan di tangan Anda.'];
        }
        $card = $card[0];
        $c_idx = (int)$card['card_index'];
        $deck_type = $card['deck_type'];

        if ($deck_type === 'politik' && $c_idx === 8) {
            // Intervensi Hakim: Bebaskan diri dari Karantina
            return $this->exit_jail($room_id, $user_id, 'card');
        } elseif ($deck_type === 'politik' && $c_idx === 18) {
            // Pembungkaman Saksi: Tebus perlindungan & amankan 200 PR
            $this->db->query("UPDATE sk_cards SET status = 'discard', holder_user_id = NULL WHERE id = ?", [$card['id']]);
            $this->db->query("UPDATE sk_players SET reputation_points = reputation_points + 200 WHERE sk_game_id = ? AND user_id = ?", [$game_id, $user_id]);
            $this->add_log($game_id, $user_id, 'SAKSI_SILENT', "Memainkan kartu 'Pembungkaman Saksi'! Membungkam saksi dan mengamankan +200 PR.");
            $this->bump_version($game_id, $room_id);
            return ['ok' => true, 'message' => 'Kartu Pembungkaman Saksi berhasil diaktifkan (+200 PR)!'];
        }

        return ['ok' => false, 'message' => 'Kartu ini tidak dapat dimainkan secara langsung.'];
    }

    // ── Aksi: Selesai Giliran & Evaluasi Eliminasi / Menang ───────
    public function end_turn($room_id, $user_id) {
        $game = $this->db->query("SELECT * FROM sk_games WHERE room_id = ?", [$room_id])->result_array();
        if (empty($game)) return ['ok' => false, 'message' => 'Game tidak ditemukan.'];
        $game = $game[0];
        $game_id = (int)$game['id'];

        if ((int)$game['current_turn_user_id'] !== (int)$user_id) {
            return ['ok' => false, 'message' => 'Bukan giliran Anda!'];
        }

        $player = $this->db->query("SELECT * FROM sk_players WHERE sk_game_id = ? AND user_id = ?", [$game_id, $user_id])->result_array();
        $player = $player[0];

        // Reset buff sewa sementara & kurangi putaran blokir sewa properti
        $this->db->query("UPDATE sk_properties SET rent_multiplier_buff = 1.0 WHERE sk_game_id = ? AND owner_user_id = ?", [$game_id, $user_id]);
        $this->db->query("UPDATE sk_properties SET rent_blocked_turns = GREATEST(0, rent_blocked_turns - 1) WHERE sk_game_id = ? AND owner_user_id = ? AND rent_blocked_turns > 0", [$game_id, $user_id]);

        // CEK MEKANIK UTANG & ELIMINASI: Batas maksimal utang adalah -300 PR.
        // Jika akhir giliran saldo <= -301 PR -> ELIMINASI TOTAL (DEPLATFORMED)
        if ((int)$player['reputation_points'] <= -301) {
            $this->db->query(
                "UPDATE sk_players SET is_eliminated = 1, eliminated_reason = 'Utang melewati batas toleransi (-301 PR)' WHERE id = ?",
                [$player['id']]
            );

            // Sita seluruh properti dan kembalikan ke bank/lelang
            $this->db->query("UPDATE sk_properties SET owner_user_id = NULL, building_level = 0 WHERE sk_game_id = ? AND owner_user_id = ?", [$game_id, $user_id]);

            $this->add_log($game_id, $user_id, 'DEPLATFORMED', "🚨 DEPLATFORMED! Saldo reputasi {$player['reputation_points']} PR (melebihi batas utang -300 PR). Tereliminasi dan seluruh aset disita!");
        }

        // Cek Apakah Tersisa 1 Pemain Hidup (Kondisi Kemenangan)
        $alive = $this->db->query(
            "SELECT * FROM sk_players WHERE sk_game_id = ? AND is_eliminated = 0 ORDER BY turn_order ASC",
            [$game_id]
        )->result_array();

        if (count($alive) <= 1) {
            $winner = $alive[0] ?? null;
            $winner_id = $winner ? (int)$winner['user_id'] : $user_id;

            $this->db->query("UPDATE sk_games SET winner_user_id = ?, game_phase = 'ended' WHERE id = ?", [$winner_id, $game_id]);
            $this->db->query("UPDATE game_rooms SET status = 'finished' WHERE id = ?", [$room_id]);
            $this->db->query("UPDATE users SET sk_wins = sk_wins + 1 WHERE id = ?", [$winner_id]);

            $winner_info = $this->db->query("SELECT username FROM users WHERE id = ?", [$winner_id])->result_array();
            $winner_name = $winner_info[0]['username'] ?? 'Pemain';

            $this->add_log($game_id, $winner_id, 'VICTORY', "🏆 {$winner_name} berhasil bertahan sebagai PENGUASA TAHTA & PANGGUNG SKENA! Lawan lainnya telah deplatformed.");
            $this->bump_version($game_id, $room_id);

            return ['ok' => true, 'game_ended' => true, 'winner_id' => $winner_id, 'message' => "Game Selesai! Pemenang: {$winner_name}."];
        }

        // Cari Pemain Aktif Berikutnya yang Belum Tereliminasi
        $all_players = $this->db->query(
            "SELECT * FROM sk_players WHERE sk_game_id = ? ORDER BY turn_order ASC",
            [$game_id]
        )->result_array();

        $cur_idx = 0;
        foreach ($all_players as $idx => $p) {
            if ((int)$p['user_id'] === (int)$user_id) {
                $cur_idx = $idx;
                break;
            }
        }

        $next_player_id = null;
        $total = count($all_players);
        for ($i = 1; $i <= $total; $i++) {
            $candidate = $all_players[($cur_idx + $i) % $total];
            if (!$candidate['is_eliminated']) {
                $next_player_id = (int)$candidate['user_id'];
                break;
            }
        }

        $this->db->query(
            "UPDATE sk_games SET current_turn_user_id = ?, game_phase = 'roll', pending_action = NULL, pending_tile_id = NULL WHERE id = ?",
            [$next_player_id, $game_id]
        );

        $this->bump_version($game_id, $room_id);

        return ['ok' => true, 'next_player_id' => $next_player_id];
    }

    // ── Aksi Menyerah (Surrender / Concede) ──────────────────────
    public function surrender($room_id, $user_id) {
        $game = $this->db->query("SELECT * FROM sk_games WHERE room_id = ?", [$room_id])->result_array();
        if (empty($game)) return ['ok' => false, 'message' => 'Game tidak ditemukan.'];
        $game = $game[0];
        $game_id = (int)$game['id'];

        // Cek info pemain di game
        $player = $this->db->query("SELECT * FROM sk_players WHERE sk_game_id = ? AND user_id = ?", [$game_id, $user_id])->result_array();
        if (empty($player)) {
            return ['ok' => false, 'message' => 'Pemain tidak terdaftar dalam game.'];
        }
        $player = $player[0];

        $user_info = $this->db->query("SELECT username FROM users WHERE id = ?", [$user_id])->result_array();
        $username = $user_info[0]['username'] ?? 'Pemain';

        // 1. Tandai pemain sebagai tereliminasi karena menyerah
        $this->db->query(
            "UPDATE sk_players SET is_eliminated = 1, eliminated_reason = 'Menyerah dari permainan' WHERE id = ?",
            [$player['id']]
        );

        // 2. Sita & bebaskan seluruh properti milik pemain yang menyerah
        $this->db->query(
            "UPDATE sk_properties SET owner_user_id = NULL, building_level = 0, rent_multiplier_buff = 1.0, rent_blocked_turns = 0 WHERE sk_game_id = ? AND owner_user_id = ?",
            [$game_id, $user_id]
        );

        // 3. Kembalikan kartu yang dipegang pemain ke deck
        $this->db->query(
            "UPDATE sk_cards SET holder_user_id = NULL, status = 'deck' WHERE sk_game_id = ? AND holder_user_id = ?",
            [$game_id, $user_id]
        );

        // 4. Catat log
        $this->add_log($game_id, $user_id, 'SURRENDER', "🏳️ {$username} telah MENYERAH dan keluar ke Lobby.");

        // 5. Keluarkan pemain dari room_players
        if (!class_exists('Room_model')) {
            require_once dirname(dirname(__DIR__)) . '/lobby/models/Room_model.php';
        }
        $room_model = new Room_model();
        $room_model->leave_room($room_id, $user_id);

        // 6. Cek sisa pemain yang masih bertahan (is_eliminated = 0)
        $alive = $this->db->query(
            "SELECT sp.*, u.username FROM sk_players sp JOIN users u ON sp.user_id = u.id WHERE sp.sk_game_id = ? AND sp.is_eliminated = 0 ORDER BY sp.turn_order ASC",
            [$game_id]
        )->result_array();

        $alive_count = count($alive);

        // JIKA TERSISA 1 PEMAIN (atau 0) -> GAME SELESAI! PEMAIN TERAKHIR MENANG
        if ($alive_count <= 1) {
            $winner = $alive[0] ?? null;
            $winner_id = $winner ? (int)$winner['user_id'] : null;

            if ($winner_id) {
                $winner_name = $winner['username'];
                $this->db->query("UPDATE sk_games SET winner_user_id = ?, game_phase = 'ended', pending_action = NULL WHERE id = ?", [$winner_id, $game_id]);
                $this->db->query("UPDATE game_rooms SET status = 'finished' WHERE id = ?", [$room_id]);
                $this->db->query("UPDATE users SET sk_wins = sk_wins + 1 WHERE id = ?", [$winner_id]);

                $this->add_log($game_id, $winner_id, 'VICTORY', "🏆 {$winner_name} dinyatakan sebagai PEMENANG karena lawan telah menyerah!");
            } else {
                $this->db->query("UPDATE sk_games SET game_phase = 'ended', pending_action = NULL WHERE id = ?", [$game_id]);
                $this->db->query("UPDATE game_rooms SET status = 'finished' WHERE id = ?", [$room_id]);
            }

            $this->bump_version($game_id, $room_id);
            return [
                'ok'         => true,
                'game_ended' => true,
                'winner_id'  => $winner_id,
                'message'    => 'Anda telah menyerah. Permainan selesai karena hanya tersisa 1 pemain.',
            ];
        }

        // JIKA MASIH ADA 2 PEMAIN ATAU LEBIH -> GAME DILANJUTKAN
        // Jika pemain yang menyerah sedang memegang giliran, alihkan giliran ke pemain hidup berikutnya
        if ((int)$game['current_turn_user_id'] === (int)$user_id) {
            $all_players = $this->db->query(
                "SELECT * FROM sk_players WHERE sk_game_id = ? ORDER BY turn_order ASC",
                [$game_id]
            )->result_array();

            $cur_idx = 0;
            foreach ($all_players as $idx => $p) {
                if ((int)$p['user_id'] === (int)$user_id) {
                    $cur_idx = $idx;
                    break;
                }
            }

            $next_player_id = null;
            $total = count($all_players);
            for ($i = 1; $i <= $total; $i++) {
                $candidate = $all_players[($cur_idx + $i) % $total];
                if (!$candidate['is_eliminated']) {
                    $next_player_id = (int)$candidate['user_id'];
                    break;
                }
            }

            if ($next_player_id) {
                $this->db->query(
                    "UPDATE sk_games SET current_turn_user_id = ?, game_phase = 'roll', pending_action = NULL, pending_tile_id = NULL WHERE id = ?",
                    [$next_player_id, $game_id]
                );
            }
        }

        $this->bump_version($game_id, $room_id);
        return [
            'ok'         => true,
            'game_ended' => false,
            'remaining'  => $alive_count,
            'message'    => "Anda telah menyerah dan keluar ke Lobby. Permainan berlanjut dengan sisa {$alive_count} pemain.",
        ];
    }

    // ── Helper Catat Log ──────────────────────────────────────────
    private function add_log($game_id, $user_id, $action_type, $message) {
        $this->db->table('sk_logs')->insert([
            'sk_game_id'  => $game_id,
            'user_id'     => $user_id,
            'action_type' => $action_type,
            'message'     => $message,
        ]);
    }
}
