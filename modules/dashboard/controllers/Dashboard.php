<?php

class Dashboard extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('ui');
        $this->_guard();
    }

    private function _guard() {
        if (!Session::get('user_id')) {
            redirect('auth');
            exit;
        }
    }

    public function index() {
        $user_id = Session::get('user_id');
        $today   = date('Y-m-d');

        // Stats per user
        $total_wb = $this->db->table('whiteboards')->where('user_id', $user_id)->where('is_deleted', 0)->get()->num_rows();
        $total_nt = $this->db->table('notes')->where('user_id', $user_id)->where('is_deleted', 0)->get()->num_rows();
        $today_wb = $this->db->table('whiteboards')->where('user_id', $user_id)->where('is_deleted', 0)->like('created_at', $today, 'after')->get()->num_rows();
        $today_nt = $this->db->table('notes')->where('user_id', $user_id)->where('is_deleted', 0)->like('created_at', $today, 'after')->get()->num_rows();

        // Recent Whiteboards (6 terbaru milik user ini)
        $recent_wb = $this->db->table('whiteboards')
            ->where('user_id', $user_id)
            ->where('is_deleted', 0)
            ->order_by('updated_at', 'DESC')
            ->limit(6)
            ->get()->result();

        // Recent Notes (6 terbaru milik user ini)
        $recent_nt = $this->db->table('notes')
            ->where('user_id', $user_id)
            ->where('is_deleted', 0)
            ->order_by('updated_at', 'DESC')
            ->limit(6)
            ->get()->result();

        $data['title']     = 'Beranda';
        $data['total_wb']  = $total_wb;
        $data['total_nt']  = $total_nt;
        $data['today_wb']  = $today_wb;
        $data['today_nt']  = $today_nt;
        $data['recent_wb'] = $recent_wb;
        $data['recent_nt'] = $recent_nt;

        $this->load->view('dashboard/index', $data);
    }
}
