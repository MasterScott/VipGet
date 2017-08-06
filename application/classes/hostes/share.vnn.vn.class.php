<?php

class FileHostGenerator
{
    private $filehost = 'share.vnn.vn';
    private $downhost = 'share.vnn.vn';
    private $url = '';
    private $username = '';
    private $password = '';
    public $filename = '';
    public $filesize = 0;
    public $cookie = '';
    public $streaming = 1;
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

        if (Helper::getCookie($this->downhost) === false) {
            /* Get Cookie of Site */
            self::loginSite();
        } else {
            $this->cookie = Helper::getCookie($this->downhost);
            // Check login with cookie
            $data = Helper::curl('http://share.vnn.vn/index.php', $this->cookie, '', 1);
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
        Helper::saveCookie($this->downhost, $this->cookie);

        /* Check link alive or died */
        if (!self::checkLink($this->url)) {
            $this->isError = true;
            $this->errors[] = 'Your link died';
            return false;
        }

        $direct = self::downMain();

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
        $data = Helper::curl($this->url, $this->cookie, '', 0);
        if (strpos($data, 'notfour') !== false)
            return false;
        return true;
    }

    protected function downMain() {
        $page = Helper::curl($this->url, $this->cookie, '', 0, 0);
        if (preg_match('~(http://dl\d{1,2}\.share\.vnn\.vn.+)\'~', $page, $matches)) {
            $direct = $matches[1];
            return $direct;
        } else {
            Helper::saveCookie($this->downhost, '');
            return false;
        }
    }

    protected function loginSite() {
        $data = Helper::curl('https://id.vnn.vn/login?service=http://share.vnn.vn/login.php?do=login&url=http://share.vnn.vn/', '', '', 1);
        $jsessionid = Helper::cut_str($data, 'JSESSIONID=', ';');
        $lt = Helper::cut_str($data, 'lt" value="', '"');
        $cookie = Helper::GetCookies($data);

        $data = Helper::curl('https://id.vnn.vn/login;jsessionid='.$jsessionid.'?service=http://share.vnn.vn/login.php?do=login&url=http://share.vnn.vn/', $cookie, 'username='.$this->username.'&password='.$this->password.'&lt='.$lt.'&_eventId=submit&submit=Đăng nhập', 1);
        if (Helper::GetLocation($data) !== false) {
            $data = Helper::curl(Helper::GetLocation($data), '', '');
            if (preg_match('~(PHPSESSID=.{39});~', $data, $match))
                $this->cookie = $match[1];
            return true;
        } else
            return false;
    }
}