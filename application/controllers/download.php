<?php

class Download extends ControllerBase
{
    function index($args)
    {
        $key = (isset($_GET['key'])) ? $_GET['key'] : '';
        include SITE_PATH . 'application/classes/generator.class.php';

        error_reporting (0);
        $sql = $this->registry->db->sql_query("SELECT * FROM generator WHERE `key`='".$key."'");
        if ($this->registry->db->sql_affectedrows() <= 0) {
            sleep(15);
            header("HTTP/1.1 404 Not Found");
            die("Your download key is not valid");
        }
        $row = $this->registry->db->sql_fetchrow($sql);
        if ((Helper::getIpAddress() != $row['ip']) && ($row['limited']==1)) {
            sleep(15);
            die("You are not owned this file");
        }
        if (time() - $row['created'] > 86400) {
            sleep(15);
            die("Your file is storage more than 24h");
        }
        if ($row['streaming'] == 0) {
            Helper::redirect($row['direct']);
            exit();
        }

        Generation::download($key, $row);
    }

}