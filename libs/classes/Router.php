<?php

class Router {
    private $registry;
    private $path;
    private $args = array();

    function __construct($registry) {
        $this->registry = $registry;
    }

    function setPath($path) {
        $path = trim($path);
        $path .= DIR_SEP;
        if (is_dir($path) == false) {
            throw new Exception ('Invalid controller path: `' . $path . '`');
        }
        $this->path = $path;
    }

    private function getController(&$file, &$controller, &$action, &$args) {
        $route = (empty($_GET['route'])) ? '' : $_GET['route'];
        if (empty($route)) { $route = 'home'; }
        // Get separate parts
        $route = trim($route, '/\\');
        $parts = explode('/', $route);
        // Find right controller
        $cmd_path = $this->path;
        foreach ($parts as $part) {
            $fullpath = $cmd_path . $part;

            // Is there a dir with this path?
            if (is_dir($fullpath)) {
                $cmd_path .= $part . DIR_SEP;
                array_shift($parts);
                continue;
            }
            // Find the file
            if (is_file($fullpath . '.php')) {
                $controller = $part;
                array_shift($parts);
                break;
            }
        }
        if (empty($controller)) { $controller = 'home'; };
        // Get action
        $action = array_shift($parts);
        if (empty($action)) { $action = 'index'; }
        $file = $cmd_path . $controller . '.php';
        $args = $parts;
    }

    function delegate() {
        global $registry, $config;
        // Analyze route
        $this->getController($file, $controller, $action, $args);
        $registry->controller = $controller;
        $registry->action = $action;
        // File available?
        if (is_readable($file) == false) {
            Helper::redirect($config['SITE_URL'] . 'error.php');
        }
        // Include the file
        include ($file);
        // Initiate the class
        $class = $controller;
        $controller = new $class($this->registry); // (1)
        // Action available?
        if (is_callable(array($controller, $action)) == false) {
            Helper::redirect($config['SITE_URL'] . 'error.php');
        }
        // Run action
        $controller->$action($args);
    }
}