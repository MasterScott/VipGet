<?php

class FileHostGenerator
{
    private $filehost = 'zing.vn';
    private $url = '';
    private $username = '';
    private $password = '';
    public $filename = '';
    public $filesize = 0;
    public $cookie = '';
    public $streaming = 0;
    public $isError = false;
    public $errors = array();

    public function __construct($url, $username, $password)
    {
        $this->url = $url;
        $this->username = $username;
        $this->password = $password;
    }

    public function getlink()
    {
        $query = parse_url($this->url, PHP_URL_QUERY);
        $param = end(explode('=/', $query));
        $this->url = 'http://download.apps.zing.vn/'.$param;

        /* Check link alive or died */
        if (!self::checkLink($this->url)) {
            $this->isError = true;
            $this->errors[] = 'Your link died';
            return false;
        }

        $direct = self::getDirect();

        if ($direct !== false) {
            $size_name = Helper::size_name($direct, '');
            $this->filename = $size_name[1];
            $this->filesize = $size_name[0];
            return $direct;
        } else {
            $this->isError = true;
            $this->errors[] = 'Can\'t download this file host at the moment';
            return false;
        }
    }

    protected function checkLink($url) {
        $data = Helper::curl($this->url, '', '', 0, 1);
        if (strpos($data, 'File không tồn tại') !== false)
            return false;
        return true;
    }

    protected function getDirect() {
        $page = Helper::curl($this->url, '', '');
        if (preg_match('~(http://((dl(\d{1,2})?)|adm)\.download.*)"~U', $page, $matches) !== false) {
            $direct = $matches[1];
            return $direct;
        } else {
            return false;
        }
    }
}