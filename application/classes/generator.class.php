<?php

class Generation
{
    private $url = "";
    private $path = "hostes";
    public $errors = array();
    public $filename = '';
    public $filesize = 0;

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function set_url($i) {
        if (!empty($i))
            $this->url = $i;
    }

    private function checkExist() {
        $sql = $this->db->sql_query("SELECT * FROM generator WHERE `link`='".$this->url."'");
        if ($this->db->sql_affectedrows() > 0 ) {
            $row = $this->db->sql_fetchrow($sql);
            if ((time() - $row['created'] > 86400) || $row['limited'] == 1)
                return false;
            return $row;
        }
        else
            return false;
    }

    public function init()
    {
        $existed = self::checkExist();
        if ($existed !== false) {
            $this->filename = $existed['filename'];
            $this->filesize = $existed['filesize'];
            return $existed['key'];
        }

        $host = parse_url($this->url, PHP_URL_HOST);
        $host = (strpos($host, 'www.') === false) ? $host : str_replace('www.', '', $host);

        if (file_exists(SITE_PATH . 'application/classes/' . $this->path . '/' . $host . '.class.php'))
            include SITE_PATH . 'application/classes/' . $this->path . '/' . $host . '.class.php';
        else {
            $this->errors[] = 'No plugin for this file host';
            return false;
        }

        $accAr = Helper::getAccount(self::leechHost($host));
        if ($accAr !== false) {
            $hostclass = new FileHostGenerator($this->url, $accAr[0], $accAr[1]);
            $direct = $hostclass->getlink();
            if ($direct !== false && !$hostclass->isError) {
                if ($hostclass->filesize <= 0) {
                    $this->errors[] = 'Size of file is not valid ('.$hostclass->filesize.')';
                    return false;
                }
                if ($hostclass->filesize > (500*1024*1024*8)) {
                    $this->errors[] = 'We support max filesize 500Mb';
                    return false;
                }
                $key = Helper::rand_string(15);
                $this->db->sql_query("INSERT INTO `generator` (`key`,`link`,`direct`,`filename`,`filesize`,`cookie`,`user`,`ip`,`streaming`,`created`,`limited`) VALUES ('".$key."','".$this->url."','".$direct."','".$hostclass->filename."','".$hostclass->filesize."','".$hostclass->cookie."','1','".Helper::getIpAddress()."','".$hostclass->streaming."','".time()."','0')");
                if ($this->db->sql_affectedrows() > 0) {
                    $this->filename = $hostclass->filename;
                    $this->filesize = $hostclass->filesize;
                    return $key;
                }
            } else {
                $this->errors = $hostclass->errors;
                return false;
            }
        } else {
            $this->errors[] = 'No account for this file host';
            return false;
        }
    }

    private function leechHost($h) {

        $arr = array('fshare.vn'        =>  'fshare.vn',
                     'youtube.com'      =>  '*',
                     'tailieu.vn'       =>  'tailieu.vn',
                     'up.4share.vn'     =>  'up.4share.vn',
                     'share.vnn.vn'     =>  'share.vnn.vn',
                     'mediafire.com'    =>  '*',
                     'mp3.zing.vn'      =>  'zing.vn',
                     'me.zing.vn'       =>  'zing.vn');

        if (array_key_exists($h, $arr))
        {
            if ($arr[$h] != '*')
                return $arr[$h];
            return 'global';
        }
        return false;
    }

    public static function download($key, $row)
    {
        global $registry;

        $link = '';
        $filesize = $row['filesize'];
        $filename = $registry->config['DOWNLOAD_PREFIX'].Helper::convert_name($row['filename']);
        $directlink = urldecode($row['direct']);
        $cookie = $row['cookie'];

        $link = $directlink;
        $link = str_replace(" ","%20",$link);
        if(!$link) {
            sleep(15);
            header("HTTP/1.1 404 Not Found");
            die('Your file can\'t download');
        }
        $range = '';
        if (isset($_SERVER['HTTP_RANGE'])) {
            $range = substr($_SERVER['HTTP_RANGE'], 6);
            list($start, $end) = explode('-', $range);
            $new_length = $filesize - $start;
        }
        $port = 80;
        $schema = parse_url(trim($link));
        $host= $schema['host'];
        $scheme = "http://";
        $gach = explode("/", $link);
        list($path1, $path)  = explode($gach[2], $link);
        if(isset($schema['port'])) $port = $schema['port'];
        elseif ($schema['scheme'] == 'https') {
            $scheme = "ssl://";
            $port = 443;
        }
        if ($scheme != "ssl://") {
            $scheme = "";
        }
        $hosts = $scheme . $host . ':' . $port;
        $fp = @stream_socket_client ($hosts, $errno, $errstr, 20, STREAM_CLIENT_CONNECT );
        if (!$fp) {
            sleep(15);
            header("HTTP/1.1 404 Not Found");
            die ("HTTP/1.1 404 Not Found");
        }

        $data = "GET {$path} HTTP/1.1\r\n";
        $data .= "User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:12.0) Gecko/20100101 Firefox/12.0\r\n";
        $data .= "Host: {$host}\r\n";
        $data .= "Accept: */*\r\n";
        $data .= $cookie ? "Cookie: ".$cookie."\r\n" : '';
        if (!empty($range)) $data .= "Range: bytes={$range}\r\n";
        $data .= "Connection: Close\r\n\r\n";
        @stream_set_timeout($fp, 10);
        fputs($fp, $data);
        fflush($fp);
        $header = '';
        do {
            if(!$header) {
                $header .= stream_get_line($fp, 512);
                if(!stristr($header,"HTTP/1")) break;
            }
            else $header .= stream_get_line($fp, 512);
        }
        while (strpos($header, "\r\n\r\n" ) === false);
        // Must be fresh start
        if( headers_sent() )
            die('Headers Sent');
        // Required for some browsers
        if(ini_get('zlib.output_compression'))
            ini_set('zlib.output_compression', 'Off');
        header("Pragma: public"); // required
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false); // required for certain browsers
        header("Content-Transfer-Encoding: binary");
        header("Accept-Ranges: bytes");

        if(stristr($header,"TTP/1.0 200 OK") || stristr($header,"TTP/1.1 200 OK")) {
            if(!is_numeric($filesize)) $filesize = trim (Helper::cut_str ($header, "Content-Length:", "\n" ));
            if(stristr($header,"filename")) {
                $filename = trim (Helper::cut_str ( $header, "filename", "\n" ) );
                $filename = preg_replace("/(\"\;\?\=|\"|=|\*|UTF-8|\')/","",$filename);
                $filename = $registry->config['DOWNLOAD_PREFIX'].$filename;
            }
            if(is_numeric($filesize)) {
                header("HTTP/1.1 200 OK");
                header("Content-Type: application/force-download");
                header("Content-Disposition: attachment; filename=".$filename);
                header("Content-Length: {$filesize}");
            }
            else {
                sleep(5);
                header("HTTP/1.1 404 Not Found");
                die ("HTTP/1.1 404 Not Found");
            }
        }
        elseif(stristr($header,"TTP/1.1 206") || stristr($header,"TTP/1.0 206")) {
            sleep(2);
            header("HTTP/1.1 206 Partial Content");
            header("Content-Type: application/force-download");
            header("Content-Length: $new_length");
            header("Content-Range: bytes $range/{$filesize}");
        }
        else {
            sleep(10);
            header("HTTP/1.1 404 Not Found");
            die ("HTTP/1.1 404 Not Found");
        }

        $tmp = explode("\r\n\r\n", $header);
        $max =count($tmp);
        for($i=1;$i < $max;$i++){
            print $tmp[$i];
            if($i != $max-1) echo "\r\n\r\n";
        }
        while (!feof($fp) && (connection_status()==0)) {
            $recv = @stream_get_line($fp, 512);
            @print $recv;
            @flush();
            @ob_flush();
        }
        fclose($fp);
        $sql = $registry->db->sql_query("UPDATE `generator` SET `limited`='1' WHERE `key`='".$key."'");
        exit;
    }
}