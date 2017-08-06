<?php

Abstract Class ControllerBase {
    protected $registry;
    function __construct($registry) {

        //set smarty template container
        $registry->smartyControllerContainerRoot = $registry->CurrentTemplate . '/';
        $registry->smartyControllerContainer = $registry->CurrentTemplate . '/'. $registry->controller . '/';

        $registry->smarty->assign(array(
            'smartyControllerContainerRoot'	=> $registry->CurrentTemplate . '/',
            'smartyControllerContainer' => $registry->CurrentTemplate . '/' . $registry->controller . '/',
            'module_title' => strtoupper($registry->controller)
        ));

        $this->registry = $registry;
    }
    abstract function index($args);
}