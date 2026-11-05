<?php

class Notes extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('ui');
        $this->_guard();
    }

    private function _guard() {
        if (!Session::get('user_id')) {
            $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
                      || (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false);
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Sesi berakhir, silakan login kembali']);
                exit;
            }
            redirect('auth');
            exit;
        }
    }

    // Daftar catatan milik user
    public function index() {
        $user_id = Session::get('user_id');
        $list = $this->db->table('notes')
            ->where('user_id', $user_id)
            ->where('is_deleted', 0)
            ->order_by('updated_at', 'DESC')
            ->get()->result();

        $data['title'] = 'Catatan';
        $data['list']  = $list;
        $this->load->view('notes/index', $data);
    }

    // Buat catatan baru
    public function create() {
        $user_id = Session::get('user_id');
        $id = $this->db->table('notes')->insert([
            'user_id'    => $user_id,
            'title'      => 'Catatan ' . date('d M Y H:i'),
            'typed_text' => '',
        ]);
        redirect('notes/edit/' . $id);
    }

    // Edit catatan
    public function edit($id = null) {
        if (!$id) { redirect('notes'); return; }
        $user_id = Session::get('user_id');

        $note = $this->db->table('notes')
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->where('is_deleted', 0)
            ->get()->row();

        if (!$note) {
            Session::set_flashdata('error', 'Catatan tidak ditemukan.');
            redirect('notes');
            return;
        }

        $data['title'] = htmlspecialchars($note->title);
        $data['note']  = $note;
        $this->load->view('notes/editor', $data);
    }

    // AJAX: Auto-save note
    public function save() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false]);
            return;
        }

        $user_id         = Session::get('user_id');
        $id              = intval($_POST['id'] ?? 0);
        $title           = trim($this->input->xss_clean($_POST['title'] ?? 'Catatan Baru'));
        $typed_text      = $_POST['typed_text'] ?? '';
        $canvas_data     = $_POST['canvas_data'] ?? '';
        $handwriting_img = $_POST['handwriting_img'] ?? '';
        $ocr_text        = $_POST['ocr_text'] ?? '';
        $thumbnail       = $_POST['thumbnail'] ?? '';

        if (!$id || !$user_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        $this->db->table('notes')
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->update([
                'title'           => $title ?: 'Catatan Baru',
                'typed_text'      => $typed_text,
                'canvas_data'     => $canvas_data,
                'handwriting_img' => $handwriting_img,
                'ocr_text'        => $ocr_text,
                'thumbnail'       => $thumbnail,
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);

        echo json_encode(['success' => true, 'time' => date('H:i:s')]);
    }

    // AJAX: Pengenalan tulisan tangan (Google Handwriting Recognition API)
    public function recognize() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'POST required']);
            return;
        }

        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true);
        if (!$input) {
            $input = $_POST;
        }

        $ink    = $input['ink'] ?? [];
        $width  = intval($input['width'] ?? 800);
        $height = intval($input['height'] ?? 600);
        $lang   = ($input['lang'] ?? 'id') === 'eng' ? 'en' : 'id';

        if (empty($ink)) {
            echo json_encode(['success' => false, 'message' => 'Coretan kosong']);
            return;
        }

        $requestData = [
            'requests' => [
                [
                    'writing_guide' => [
                        'writing_area_width'  => $width > 0 ? $width : 800,
                        'writing_area_height' => $height > 0 ? $height : 600,
                    ],
                    'ink'      => $ink,
                    'language' => $lang,
                ]
            ]
        ];

        $ch = curl_init('https://www.google.com/inputtools/request?ime=handwriting&app=mobilesearch&cs=1&oe=UTF-8');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 7);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $res) {
            $parsed = json_decode($res, true);
            if (isset($parsed[0]) && $parsed[0] === 'SUCCESS' && isset($parsed[1][0][1])) {
                $candidates = $parsed[1][0][1];
                $best = $candidates[0] ?? '';
                echo json_encode([
                    'success'    => true,
                    'text'       => $best,
                    'candidates' => array_slice($candidates, 0, 5)
                ]);
                return;
            }
        }

        // Jika Google Handwriting tidak bisa dihubungi, beri tanda fallback
        echo json_encode([
            'success'  => false,
            'fallback' => true,
            'message'  => 'Google recognition unavailable, fallback to local OCR'
        ]);
    }

    // Hapus catatan (soft delete)
    public function delete($id = null) {
        if (!$id) { redirect('notes'); return; }
        $user_id = Session::get('user_id');

        $this->db->table('notes')
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->update(['is_deleted' => 1]);

        Session::set_flashdata('success', 'Catatan dihapus.');
        redirect('notes');
    }
}
