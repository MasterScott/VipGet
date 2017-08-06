<?php

require 'includes/Config.php';
require 'includes/Init.php';

# Load router
$router = new Router($registry);
$registry->set ('router', $router);

$router->setPath (SITE_PATH . 'application' . DIR_SEP . 'controllers');

$router->delegate();