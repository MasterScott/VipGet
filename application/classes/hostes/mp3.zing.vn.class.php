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
        /* Check link alive or died */
        if (!self::checkLink($this->url)) {
            $this->isError = true;
            $this->errors[] = 'Your link died';
            return false;
        }

        if (Helper::getCookie($this->filehost) === false) {
            /* Get Cookie of Site */
            self::loginSite();
        } else {
            $this->cookie = Helper::getCookie($this->filehost);
            // Check login with cookie
            $data = Helper::curl('http://me.zing.vn/index.php', $this->cookie, '', 1);
            if (strpos($data, 'Thoát') === false || empty($this->cookie)) {
                Helper::saveCookie($this->filehost, '');
                if (!$relogin = self::loginSite()){
                    $this->isError = true;
                    $this->errors[] = 'Can\'t login site. Please report master';
                    return false;
                }
            }
        }
        if (empty($this->cookie)) {
            $this->isError = true;
            $this->errors[] = 'Can\'t login site. Please report master';
            return false;
        }
        Helper::saveCookie($this->filehost, $this->cookie);

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
        $data = Helper::curl($this->url, $this->cookie, '', 0, 1);
        if (strpos($data, 'Hệ thống đang bận hoặc quá tải.') !== false)
            return false;
        return true;
    }

    protected function getDirect() {
        $page = Helper::curl($this->url, $this->cookie, '');
        if (preg_match('~Tải\snhạc\s320Kb"\shref="(.*)\"\sclass="icon\s_btnDownload~', $page, $matches) !== false) {
            $direct = $matches[1];
            $page = Helper::curl($direct, $this->cookie, '');
            return Helper::GetLocation($page);
        } else {
            Helper::saveCookie($this->filehost, '');
            return false;
        }
    }

    protected function loginSite() {
        $data = Helper::curl('https://sso3.zing.vn/login', '', 'u='.$this->username.'&p='.$this->password.'&x=105&y=7&u1=http://mp3.zing.vn//login/index-v2&pid=38&fp=http://mp3.zing.vn//login/index-v2', 1, 0);
        $loc = Helper::GetLocation($data);
        if (strpos($loc, 'succ') !== false) {
            $this->cookie = Helper::GetAllCookies($data);
            return true;
        } else {
            return false;
        }
    }
}