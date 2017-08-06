<?php

class FileHostGenerator
{
    private $filehost = 'up.4share.vn';
    private $downhost = 'up.4share.vn';
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
            $data = Helper::curl('http://up.4share.vn/index.php', $this->cookie, '', 1);
            if (strpos($data, 'Logout') === false || empty($this->cookie)) {
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
        $data = Helper::curl('http://up.4share.vn/?control=checkfile', $this->cookie, 'listfile='.$this->url, 0);
        if (strpos($data, 'File OK!') === false)
            return false;
        return true;
    }

    protected function downMain() {
        $page = Helper::curl($this->url, $this->cookie, '', 0, 0);
        if (preg_match('~(http://sv\d{1,2}\.4share\.vn.+)\'>~', $page, $matches)) {
            $direct = $matches[1];
            return $direct;
        } else {
            Helper::saveCookie($this->downhost, '');
            return false;
        }
    }

    protected function loginSite() {
        $data = Helper::curl('http://up.4share.vn/?control=login', '', 'inputUserName='.$this->username.'&inputPassword='.$this->password.'&rememberlogin=on', 1);
        if (Helper::GetLocation($data) !== false) {
            $this->cookie = Helper::GetAllCookies($data);
            return true;
        } else {
            return false;
        }
    }
}