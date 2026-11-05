<?php

class User_model extends Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Cari user berdasarkan email
     */
    public function find_by_email($email) {
        return $this->db->table('users')->where('email', $email)->get()->row();
    }

    /**
     * Cari user berdasarkan username
     */
    public function find_by_username($username) {
        return $this->db->table('users')->where('username', $username)->get()->row();
    }

    /**
     * Cari user berdasarkan ID
     */
    public function find_by_id($id) {
        return $this->db->table('users')->where('id', (int)$id)->get()->row();
    }

    /**
     * Buat user baru
     */
    public function create($data) {
        $this->db->table('users')->insert([
            'username'      => $data['username'],
            'email'         => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'avatar'        => $data['avatar'] ?? 'default.png',
        ]);
        return $this->db->last_insert_id();
    }

    /**
     * Ambil data profil + statistik user
     */
    public function get_profil($user_id) {
        return $this->db
            ->table('users')
            ->select('id, username, email, avatar, uno_wins, ww_wins, created_at')
            ->where('id', (int)$user_id)
            ->get()->row();
    }

    /**
     * Tambah hitungan kemenangan
     * @param int $user_id
     * @param string $type 'uno' atau 'ww'
     */
    public function increment_wins($user_id, $type) {
        $col = ($type === 'uno') ? 'uno_wins' : 'ww_wins';
        $this->db->query(
            "UPDATE users SET {$col} = {$col} + 1 WHERE id = ?",
            [(int)$user_id]
        );
    }

    /**
     * Cek apakah username atau email sudah terdaftar
     */
    public function is_duplicate($username, $email) {
        $by_username = $this->db->table('users')->where('username', $username)->get()->row();
        if ($by_username) return 'username';

        $by_email = $this->db->table('users')->where('email', $email)->get()->row();
        if ($by_email) return 'email';

        return false;
    }

    /**
     * Update avatar user
     */
    public function update_avatar($user_id, $avatar_filename) {
        $this->db->table('users')
            ->where('id', (int)$user_id)
            ->update(['avatar' => $avatar_filename]);
    }
}
