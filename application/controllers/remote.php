<?php

class Remote extends ControllerBase
{
    function index($args)
    {
        $url = (isset($_REQUEST['url'])) ? trim($_REQUEST['url']) : '';
        $post = (isset($_REQUEST['post'])) ? trim($_REQUEST['post']) : '';

        $data = Helper::curl($url, '', $post, 0);
        echo $data;
    }

}