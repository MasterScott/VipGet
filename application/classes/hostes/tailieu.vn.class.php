<?php

class FileHostGenerator
{
    private $filehost = 'tailieu.vn';
    private $downhost = 'tailieu.vn';
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
            $cookie = self::loginMain();
            if ($cookie !== false) {
                $this->cookie = $cookie;
                Helper::saveCookie($this->downhost, $this->cookie);
            }
        } else {
            $this->cookie = Helper::getCookie($this->downhost);
            // Check login with cookie
            $data = Helper::curl('http://tailieu.vn/index.php', $this->cookie, '', 1, 1);
            if (strpos($data, 'Thoát') === false) {
                Helper::saveCookie($this->downhost, '');
                if (!$relogin = self::loginMain()){
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

        $direct = self::downMain();

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

        $data = Helper::curl($this->url, $this->cookie, '');
        if (Helper::GetLocation($data) !== false)
            return false;
        return true;
    }

    protected function downMain() {

        $data = Helper::curl($this->url, $this->cookie, '', 0);
        $downLink = Helper::cut_str($data, 'tailieu.vn/download/document/', '"');
        $downLink = 'http://tailieu.vn/download/document/'.$downLink;

        $data = Helper::curl($downLink, $this->cookie, '');

        $random = time() . Helper::RandomNumber(100, 999);
        $captchaId = (isset($_GET['captchaId'])) ? trim($_GET['captchaId']) : '';
        $captchaInput = (isset($_GET['captchaIn'])) ? trim($_GET['captchaIn']) : '';
        $docId = Helper::cut_str($data, 'DocID = \'', '\'');
        $memId = '';
        if (preg_match('~"(\d{5,9})"\sname="MemberID~', $data, $match))
            $memId = $match[1];

        $data = Helper::curl($downLink, $this->cookie, 'xjxfun=getDownload&xjxr='.$random.'&xjxargs[]=<xjxobj><e><k>captcha_id_DL</k><v>'.$captchaId.'</v></e><e><k>DocID</k><v>'.$docId.'</v></e><e><k>MemberID</k><v>'.$memId.'</v></e><e><k>captcha_input_DL</k><v>'.$captchaInput.'</v></e></xjxobj>', 0);
        if (preg_match('~(http://download\d{1,2}\.tailieu\.vn.+)\"\s~', $data, $match)) {
            $direct = $match[1];
            return $direct;
        } else {
            return false;
        }
    }

    protected function loginMain() {
        $data = Helper::curl('http://tailieu.vn/tai-khoan1/dangnhap.html', '', 'txtLoginUsername='.$this->username.'&txtLoginPassword='.$this->password.'&remem_pass=on', 1);
        if (Helper::GetLocation($data) !== false) {
            $this->cookie = Helper::GetAllCookies($data);
            return true;
        } else {
            return false;
        }
    }
}