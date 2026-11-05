<?php

class Router {
    private $controller;
    private $method;
    private $params = [];
    private $module = '';

    public function __construct() {
        global $config;
        $this->controller = $config['default_controller'];
        $this->method = $config['default_method'];
        $this->module = $config['default_module'];
    }

    public function parseUrl() {
        // 1. Cek query string dari rewrite .htaccess (misal: index.php?/auth/process)
        if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
            $qs = $_SERVER['QUERY_STRING'];
            if ($qs[0] === '/') {
                $pos = strpos($qs, '&');
                $route = ($pos !== false) ? substr($qs, 0, $pos) : $qs;
                $route = trim($route, '/');
                if (!empty($route)) {
                    return explode('/', filter_var($route, FILTER_SANITIZE_URL));
                }
            }
        }

        // 2. Cek parameter $_GET['/'] jika di-pass dengan format ?/=route
        if (isset($_GET['/']) && !empty($_GET['/'])) {
            $url = trim($_GET['/'], '/');
            if (!empty($url)) {
                return explode('/', filter_var($url, FILTER_SANITIZE_URL));
            }
        }

        // 3. Fallback: Parse dari REQUEST_URI (bekerja di root domain maupun subfolder)
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

        // Hapus query string jika ada (?...)
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }

        // Hapus direktori script (misal: /coretan) jika aplikasi di subfolder
        $script_dir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        if ($script_dir !== '/' && $script_dir !== '\\' && $script_dir !== '.') {
            $script_dir = str_replace('\\', '/', $script_dir);
            if (strpos($uri, $script_dir) === 0) {
                $uri = substr($uri, strlen($script_dir));
            }
        }

        // Hapus /index.php jika ada di URL
        if (strpos($uri, '/index.php') === 0) {
            $uri = substr($uri, 10);
        }

        $uri = trim($uri, '/');
        if (!empty($uri)) {
            return explode('/', filter_var($uri, FILTER_SANITIZE_URL));
        }

        return [];
    }

    public function dispatch() {
        $url = $this->parseUrl();

        if (empty($url)) {
            // Default route
            if ($this->loadController()) {
                if (method_exists($this->controller, '_middleware')) {
                     $this->controller->_middleware($this->method);
                }
                call_user_func_array([$this->controller, $this->method], []);
            }
            return;
        }

        // Check if first segment is a module
        if (file_exists(MODULESPATH . $url[0])) {
            $this->module = $url[0];
            unset($url[0]);
            $url = array_values($url);
            
            // Controller is the same as module name if not specified
            if (isset($url[0]) && file_exists(MODULESPATH . $this->module . '/controllers/' . ucfirst($url[0]) . '.php')) {
                $this->controller = $url[0];
                unset($url[0]);
            } else {
                $this->controller = $this->module; // fallback to module name
            }
        } 
        // If not a module, maybe a core controller (we don't strictly support this in HMVC unless needed, but let's keep it simple: everything is a module)
        else {
            $this->module = $url[0];
            $this->controller = $url[0];
            unset($url[0]);
        }
        
        $url = array_values($url);

        if (!$this->loadController()) {
            return;
        }

        if (isset($url[0])) {
            if (method_exists($this->controller, $url[0])) {
                $this->method = $url[0];
                unset($url[0]);
            } else {
                $this->show404();
                return;
            }
        }

        $this->params = $url ? array_values($url) : [];

        // Execute middleware before method if implemented in controller
        if (method_exists($this->controller, '_middleware')) {
             $this->controller->_middleware($this->method);
        }

        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    private function loadController() {
        $controller_name = ucfirst($this->controller);
        $controller_file = MODULESPATH . $this->module . '/controllers/' . $controller_name . '.php';

        if (file_exists($controller_file)) {
            require_once $controller_file;
            $this->controller = new $controller_name();
            return true;
        } else {
            $this->show404();
            return false;
        }
    }

    private function show404() {
        header("HTTP/1.0 404 Not Found");
        echo "404 - Page Not Found";
    }
}
