<?php

class Home extends ControllerBase
{
    function index($args)
    {
        $contents = $this->registry->smarty->fetch($this->registry->smartyControllerContainer.'index.tpl');

        $this->registry->smarty->assign(array('contents' => $contents));

        $this->registry->smarty->display($this->registry->smartyControllerContainerRoot.'index.tpl');
    }

    function term($args)
    {
        $contents = $this->registry->smarty->fetch($this->registry->smartyControllerContainer.'term.tpl');

        $this->registry->smarty->assign(array('contents' => $contents));

        $this->registry->smarty->display($this->registry->smartyControllerContainerRoot.'index.tpl');
    }

    function contact($args)
    {
        $contents = $this->registry->smarty->fetch($this->registry->smartyControllerContainer.'contact.tpl');

        $this->registry->smarty->assign(array('contents' => $contents));

        $this->registry->smarty->display($this->registry->smartyControllerContainerRoot.'index.tpl');
    }

    function faq($args)
    {
        $contents = $this->registry->smarty->fetch($this->registry->smartyControllerContainer.'faq.tpl');

        $this->registry->smarty->assign(array('contents' => $contents));

        $this->registry->smarty->display($this->registry->smartyControllerContainerRoot.'index.tpl');
    }

    function privacy($args)
    {
        $contents = $this->registry->smarty->fetch($this->registry->smartyControllerContainer.'privacy.tpl');

        $this->registry->smarty->assign(array('contents' => $contents));

        $this->registry->smarty->display($this->registry->smartyControllerContainerRoot.'index.tpl');
    }

    function howtouse($args)
    {
        $contents = $this->registry->smarty->fetch($this->registry->smartyControllerContainer.'howtouse.tpl');

        $this->registry->smarty->assign(array('contents' => $contents));

        $this->registry->smarty->display($this->registry->smartyControllerContainerRoot.'index.tpl');
    }

}