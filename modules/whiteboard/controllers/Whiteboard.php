<?php

class Whiteboard extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('ui');
        $this->_guard();
    }

    // Auth guard
    private function _guard() {
        if (!Session::get('user_id')) {
            redirect('auth');
            exit;
        }
    }

    // Daftar whiteboard milik user
    public function index() {
        $user_id = Session::get('user_id');
        $list = $this->db->table('whiteboards')
            ->where('user_id', $user_id)
            ->where('is_deleted', 0)
            ->order_by('updated_at', 'DESC')
            ->get()->result();

        $data['title'] = 'Papan Tulis';
        $data['list']  = $list;
        $this->load->view('whiteboard/index', $data);
    }

    // Buat whiteboard baru
    public function create() {
        $user_id = Session::get('user_id');
        $id = $this->db->table('whiteboards')->insert([
            'user_id'     => $user_id,
            'title'       => 'Papan Tulis ' . date('d M Y H:i'),
            'canvas_data' => null,
            'thumbnail'   => null,
        ]);
        redirect('whiteboard/edit/' . $id);
    }

    // Edit whiteboard
    public function edit($id = null) {
        if (!$id) { redirect('whiteboard'); return; }
        $user_id = Session::get('user_id');

        $board = $this->db->table('whiteboards')
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->where('is_deleted', 0)
            ->get()->row();

        if (!$board) {
            Session::set_flashdata('error', 'Papan tulis tidak ditemukan.');
            redirect('whiteboard');
            return;
        }

        $data['title'] = htmlspecialchars($board->title);
        $data['board'] = $board;
        $this->load->view('whiteboard/canvas', $data);
    }

    // AJAX: Auto-save canvas
    public function save() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid method']);
            return;
        }

        $user_id     = Session::get('user_id');
        $id          = intval($_POST['id'] ?? 0);
        $canvas_data = $_POST['canvas_data'] ?? '';
        $thumbnail   = $_POST['thumbnail'] ?? '';
        $title       = trim($this->input->xss_clean($_POST['title'] ?? 'Tanpa Judul'));

        if (!$id || !$user_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        $this->db->table('whiteboards')
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->update([
                'title'       => $title ?: 'Tanpa Judul',
                'canvas_data' => $canvas_data,
                'thumbnail'   => $thumbnail,
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);

        echo json_encode(['success' => true, 'message' => 'Tersimpan', 'time' => date('H:i:s')]);
    }

    // Hapus whiteboard (soft delete)
    public function delete($id = null) {
        if (!$id) { redirect('whiteboard'); return; }
        $user_id = Session::get('user_id');

        $this->db->table('whiteboards')
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->update(['is_deleted' => 1]);

        Session::set_flashdata('success', 'Papan tulis dihapus.');
        redirect('whiteboard');
    }

    // Duplikat whiteboard
    public function duplicate($id = null) {
        if (!$id) { redirect('whiteboard'); return; }
        $user_id = Session::get('user_id');

        $board = $this->db->table('whiteboards')
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->get()->row();

        if ($board) {
            $new_id = $this->db->table('whiteboards')->insert([
                'user_id'     => $user_id,
                'title'       => 'Salinan - ' . $board->title,
                'canvas_data' => $board->canvas_data,
                'thumbnail'   => $board->thumbnail,
            ]);
            Session::set_flashdata('success', 'Papan tulis berhasil diduplikat.');
            redirect('whiteboard/edit/' . $new_id);
        } else {
            redirect('whiteboard');
        }
    }
}
