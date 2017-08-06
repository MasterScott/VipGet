<?php

class FileHostGenerator
{
    private $filehost = 'fshare.vn';
    private $downhost = 'fshare.vn';
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
        /* Check link alive or died */
        if (!self::checkLink($this->url)) {
            $this->isError = true;
            $this->errors[] = 'Your link died';
            return false;
        }

        if (Helper::getCookie($this->downhost) === false) {
            /* Get Cookie of Site */
            self::loginSite();
        } else {
            $this->cookie = Helper::getCookie($this->downhost);
            // Check login with cookie
            $data = Helper::curl('http://www.fshare.vn/index.php', $this->cookie, '', 1);
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
        $data = Helper::curl('http://www.fshare.vn/check_link.php', $this->cookie, 'action=check_link&arrlinks='.$url, 0);
        if (strpos($data, 'file_able') === false)
            return false;
        return true;
    }

    protected function downMain() {
        $page = Helper::curl($this->url, $this->cookie, '');
        if (Helper::GetLocation($page) !== false) {
            $direct = Helper::GetLocation($page);
            return $direct;
        } else {
            Helper::saveCookie($this->downhost, '');
            return false;
        }
    }

    protected function loginSite() {
        $data = Helper::curl('https://www.fshare.vn/login.php', '', 'login_useremail='.$this->username.'&login_password='.$this->password.'&url_refe=https://www.fshare.vn/index.php&auto_login=1', 1);
        if (Helper::GetLocation($data) !== false) {
            $this->cookie = str_replace('fshare_userid=-1;', '', Helper::GetAllCookies($data));
            return true;
        } else {
            return false;
        }
    }
}