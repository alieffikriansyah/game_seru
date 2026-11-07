<?php
ob_start();

// Define Application Paths
define('FCPATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('ROOTPATH', FCPATH);
define('APPPATH', ROOTPATH . 'app' . DIRECTORY_SEPARATOR);
define('MODULESPATH', ROOTPATH . 'modules' . DIRECTORY_SEPARATOR);

// Load Env
require_once APPPATH . 'core/Env.php';
Env::load(ROOTPATH . '.env');

// Set Error Reporting
if (Env::get('APP_ENV') === 'development' || Env::get('APP_DEBUG') === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Load Core Classes
require_once APPPATH . 'config/config.php';
require_once APPPATH . 'core/Session.php';
require_once APPPATH . 'core/Security.php';
require_once APPPATH . 'core/Database.php';
require_once APPPATH . 'core/Loader.php';
require_once APPPATH . 'core/Model.php';
require_once APPPATH . 'core/Controller.php';
require_once APPPATH . 'core/Router.php';

// Initialize Session
Session::init();

// Route the Request
$router = new Router();
$router->dispatch();
