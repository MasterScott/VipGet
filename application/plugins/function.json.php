<?php

function smarty_function_json($params, &$smarty)
{
    if (empty($params['data'])) {
        return;
    }

    $content = array();
    $data = $params['data'];

    if(!is_callable('json_decode')) {
        require_once 'JSON.php';
        $json = new Services_JSON();
        $content = $json->decode( trim($data) );
    } else {
        $content = json_decode(trim($data));
    }

    if($params['debug']===true) {
        echo "<pre>";
        print_r($content);
        echo "</pre>";
    }

    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'],$content);
    } else {
        return $content;
    }
}