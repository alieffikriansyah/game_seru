<?php

class AuthMiddleware {
    public static function check($allowed_roles = []) {
        $user_id = Session::get('user_id');
        $role    = Session::get('role');

        if (!$user_id || !$role) {
            redirect('auth');
            exit;
        }

        if (!empty($allowed_roles)) {
            if (is_string($allowed_roles)) {
                $allowed_roles = [$allowed_roles];
            }
            if (!in_array($role, $allowed_roles)) {
                Session::set_flashdata('error', 'Akses ditolak: Anda tidak memiliki izin untuk membuka halaman tersebut.');
                if ($role === 'karyawan') {
                    redirect('employee');
                } else {
                    redirect('dashboard');
                }
                exit;
            }
        }
    }
}
