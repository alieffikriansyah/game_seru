<?php

class Controller {
    private static $instance;
    public $load;
    public $db;
    public $input;

    public function __construct() {
        self::$instance =& $this;
        
        $this->load = new Loader();
        $this->db = Database::get_instance();
        $this->input = new Security();
        
        // Memuat UI Helper secara otomatis (Global) untuk seluruh aplikasi HMVC
        $this->load->helper('ui');
    }

    public static function &get_instance() {
        return self::$instance;
    }
}
