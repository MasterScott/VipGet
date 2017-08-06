<?php

class FileHostGenerator
{
    private $filehost = 'mediafire.com';
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
        /* Check link alive or died */
        if (!self::checkLink($this->url)) {
            $this->isError = true;
            $this->errors[] = 'Your link died';
            return false;
        }

        $direct = self::getDirect();

        if ($direct !== false) {
            $size_name = Helper::size_name($direct, $this->cookie);
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
        $data = Helper::curl($this->url, $this->cookie, '', 1, 1);
        if (strpos($data, 'Invalid or Deleted File.') !== false)
            return false;
        return true;
    }

    protected function getDirect() {
        $page = Helper::curl($this->url, $this->cookie, '', 0, 1);
        echo $page;
        if (preg_match('~kNO\s=\s"(.*)";~', $page, $matches)) {
            $direct = $matches[1];
            return $direct;
        } else {
            return false;
        }
    }
}