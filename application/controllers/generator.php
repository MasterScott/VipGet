<?php

class Generator extends ControllerBase
{
    function index($args)
    {
        $contents = $this->registry->smarty->fetch($this->registry->smartyControllerContainer.'index.tpl');

        $this->registry->smarty->assign(array('contents' => $contents));

        $this->registry->smarty->display($this->registry->smartyControllerContainerRoot.'index.tpl');
    }

    function captchaTL()
    {
        $data = Helper::curl('http://tailieu.vn/download', '', 'xjxfun=changeCaptcha&xjxr='.time().Helper::RandomNumber(100, 999).'&xjxargs[]=spmImgCaptchaDL&xjxargs[]=captcha_id_DL', 0);
        $sessId = Helper::cut_str($data, 'captcha_id_DL").value="', '"');
        $imgUrl = Helper::cut_str($data, '<img src=\'', '\'');
        echo json_encode(array('sessId'     =>  $sessId,
                               'imgUrl'     =>  $imgUrl));
    }

    function get($args)
    {
        $url = (isset($_REQUEST["url"])) ? trim($_REQUEST["url"]) : "";
        $url = Helper::fixUrl($url);
        if (Helper::isValidURL($url))
        {
            include SITE_PATH . 'application/classes/generator.class.php';
            $gen = new Generation($this->registry->db);
            $gen->set_url($url);
            $key = $gen->init();
            if ($key !== false) {
                $result = json_encode(array('error'     =>  0,
                                            'message'   =>  (count($gen->errors) > 0) ? implode("\n", $gen->errors) : '',
                                            'url'       =>  $url,
                                            'filename'  =>  $gen->filename,
                                            'filesize'  =>  $gen->filesize,
                                            'key'       =>  $key));
                echo $result;
            } else {
                $result = json_encode(array('error'     =>  1,
                                            'message'   =>  (count($gen->errors) > 0) ? implode("\n", $gen->errors) : '',
                                            'url'       =>  $url,
                                            'filename'  =>  '',
                                            'filesize'  =>  0,
                                            'key'       =>  ''));
                echo $result;
            }
        } else {
            $result = json_encode(array('error'     =>  1,
                                        'message'   =>  'Your url is not valid',
                                        'url'       =>  $url,
                                        'filename'  =>  '',
                                        'filesize'  =>  0,
                                        'key'       =>  ''));
            echo $result;
        }
    }
}