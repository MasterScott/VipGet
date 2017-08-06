<?php

class FileHostGenerator
{
    private $filehost = 'uptobox.com';
    private $downhost = 'alldebrid.com';
    private $url = '';
    private $username = '';
    private $password = '';
    public $filename = '';
    public $filesize = 0;
    public $cookie = '';
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
            $cookie = self::loginAllDebrid();
            if ($cookie !== false) {
                $this->cookie = $cookie;
                Helper::saveCookie($this->downhost, $this->cookie);
            }
        } else {
            $this->cookie = Helper::getCookie($this->downhost);
            // Check login with cookie
            $data = Helper::curl('http://alldebrid.com/index.php', $this->cookie, '', 1);
            if (strpos($data, 'disconnect') === false) {
                Helper::saveCookie($this->downhost, '');
                if (!$relogin = self::loginAllDebrid()){
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

        if ($this->downhost == 'alldebrid.com') {
            $direct = self::downAllDebrid();
        } else {
            $direct = self::downMain();
        }

        if ($direct !== false) {
            $size_name = Helper::size_name($direct, $this->cookie);
            $this->filename = $size_name[1];
            $this->filesize = $size_name[0];
            return $direct;
        } else {
            $this->isError = true;
            $this->errors[] = 'Something went wrong';
            return false;
        }
    }

    protected function checkLink($url) {
        $data = Helper::curl($this->url, '', '', 0);
        if ((strpos($data, 'Access Denied') !== false) || (strpos($data, 'File Not Found') !== false))
            return false;
        return true;
    }

    protected function downRealDebrid() {
        $data = Helper::curl('http://www.real-debrid.com/ajax/unrestrict.php?link='.$this->url.'&password=&remote=0&time='.time().Helper::RandomNumber(100, 999), $this->cookie, '', 0);
        $decoded = json_decode($data, true);
        if ($decoded['error'] == 0) {
            return $decoded['main_link'];
        } else {
            return false;
        }
    }

    protected function downAllDebrid() {
        $data = Helper::curl('http://www.alldebrid.com/service.php?link='.$this->url.'&nb=0&json=true&pw=', $this->cookie, '', 0);
        $decoded = json_decode($data, true);
        if (!$decoded['error']) {
            return $decoded['link'];
        } else {
            return false;
        }
    }

    protected function downMain() {
        $urlHost = parse_url($this->url, PHP_URL_HOST);
        $link = str_replace($urlHost, $urlHost . '://' . $this->username . ':' . $this->password . '@', $this->url);
        $page = Helper::curl($link, $this->cookie, '');
        if (Helper::GetLocation($page) !== false) {
            $direct = Helper::GetLocation($page);
            return $direct;
        } else {
            return false;
        }
    }

    protected function loginRealDebrid() {
        $data = Helper::curl('http://www.real-debrid.com/ajax/login.php?user='.$this->username.'&pass='.md5($this->password).'&captcha_challenge=&captcha_answer=&time='.Helper::datedmyToTimestamp(date("d/m/yyyy")).Helper::RandomNumber(100, 999), '', '', 0);
        $decoded = json_decode($data, true);
        if ($decoded['error'] == 0 && $decoded['message'] == 'OK') {
            return $decoded['cookie'];
        } else {
            return false;
        }
    }

    protected function loginAllDebrid() {
        $data = Helper::curl('http://www.alldebrid.com/register/?action=login&returnpage=&login_login='.$this->username.'&login_password='.$this->password, '', '', 1);
        if (Helper::GetLocation($data) !== false) {
            $cookie = Helper::GetCookies($data);
            $this->cookie = $cookie;
            return $cookie;
        } else {
            return false;
        }
    }
}