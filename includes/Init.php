<?php
/*if (!preg_match('/www\..*?/', $_SERVER['HTTP_HOST'])) {
    @header("Location: http".(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 's':'')."://www." . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit;
}*/

if (version_compare(phpversion(), '5.1.0', '<') == true) { die ('PHP5.1 Only'); } //Framework của chúng ta làm việc trên PHP5
// Constants:
define ('DIR_SEP', DIRECTORY_SEPARATOR); //Tương thích với môi trường Win hay Unix, ta tìm dúng dấu phân cách dir là \ hay /
// Get site path
$site_path = realpath(dirname(__FILE__) . DIR_SEP . '..' . DIR_SEP) . DIR_SEP;
define ('SITE_PATH', $site_path);

/** Check if environment is development and display errors **/
function setReporting() {
    global $config;
    if ($config['DEVELOPMENT_ENVIRONMENT'] == true) {
        error_reporting(E_ALL);
        ini_set('display_errors','On');
    } else {
        error_reporting(E_ALL);
        ini_set('display_errors','Off');
        ini_set('log_errors', 'On');
        ini_set('error_log', SITE_PATH.DIR_SEP.'tmp'.DIR_SEP.'logs'.DIR_SEP.'error.log');
    }
}

function sec_session_start() {
    // Start a fresh session
    if (!isset($_SESSION)) {
        session_start();
    }
    if(!isset($_SESSION['init'])) {
        session_regenerate_id();
        $_SESSION['init'] = true;
    }

    if (!defined('YES')) define('YES', true, true);
    if (!defined('NO')) define('NO', false, true);
}

/** Check for Magic Quotes and remove them **/

function stripSlashesDeep($value) {
    $value = is_array($value) ? array_map('stripSlashesDeep', $value) : stripslashes($value);
    return $value;
}

function removeMagicQuotes() {
    if ( get_magic_quotes_gpc() ) {
        $_GET    = stripSlashesDeep($_GET   );
        $_POST   = stripSlashesDeep($_POST  );
        $_COOKIE = stripSlashesDeep($_COOKIE);
    }
}

/** Check register globals and remove them **/

function unregisterGlobals() {
    if (ini_get('register_globals')) {
        $array = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
        foreach ($array as $value) {
            foreach ($GLOBALS[$value] as $key => $var) {
                if ($var === $GLOBALS[$key]) {
                    unset($GLOBALS[$key]);
                }
            }
        }
    }
}

function __autoload($class_name) {
    $filename = $class_name . '.php';
    if (file_exists(SITE_PATH . 'libs' . DIR_SEP . 'classes' . DIR_SEP . $filename)) {
        include (SITE_PATH . 'libs' . DIR_SEP . 'classes' . DIR_SEP . $filename);
    }
    elseif(file_exists(SITE_PATH . 'application' . DIR_SEP . 'models' . DIR_SEP . $filename)) {
        include (SITE_PATH . 'application' . DIR_SEP . 'models' . DIR_SEP . $filename);
    }
    else
    {
        return false;
    }
}

setReporting();
removeMagicQuotes();
unregisterGlobals();
spl_autoload_register('__autoload');
sec_session_start();


$registry = new Registry;

$registry->config = $config;

# Load database
$db = new Database();
$db->sql_connect($config['DATABASE_HOST'], $config['DATABASE_USER'], $config['DATABASE_PASS'], $config['DATABASE_NAME']);
unset($config['DATABASE_PASS']);
$registry->db = $db;

//$me = Authentication::init();
//$me->refresh_date($me->id);
//$registry->me = $me;

//get language
if(isset($_GET['lang']))
{
    $_SESSION['language'] = $_GET['lang'];
    setcookie('language', $_GET['lang'], time() + 24 * 3600, '/');
}

if(isset($_POST['lang']))
{
    $_SESSION['language'] = $_POST['lang'];
    setcookie('language', $_POST['lang'], time() + 24 * 3600, '/');
}

if(isset($_SESSION['language']))
    $langCode = $_SESSION['language'];
elseif(isset($_COOKIE['language']))
    $langCode = $_COOKIE['language'];
else
    $langCode = $config['Lang'];

$langCode = substr($langCode, 0, 2);

//declare language variable
$lang = array();
$lang = Helper::GetLangContent(SITE_PATH . 'language'.DIR_SEP.$langCode);
$registry->lang = $lang;

# Smarty template
require_once (SITE_PATH . 'libs' . DIR_SEP . 'smarty' . DIR_SEP . 'Smarty.class.php');
$smarty = new Smarty();
//$smarty->force_compile = true;
$currentTemplate = 'default';
$smarty->debugging = false;
$smarty->caching = false;
$smarty->cache_lifetime = 5;
$smarty->compile_check = true;
$smarty->addPluginsDir(SITE_PATH . DIR_SEP . 'application' . DIR_SEP . 'plugins');
$smarty->setCacheDir(SITE_PATH . DIR_SEP . 'tmp' . DIR_SEP . 'cache');
$smarty->setCompileDir(SITE_PATH . DIR_SEP . 'tmp' . DIR_SEP . 'compiler');
$smarty->setConfigDir(SITE_PATH . DIR_SEP . 'application' . DIR_SEP . 'configs');
$smarty->configLoad($currentTemplate . '.conf');
$smarty->setTemplateDir(SITE_PATH . DIR_SEP . 'application' . DIR_SEP . 'views');
$smarty->compile_id = $currentTemplate;	//seperate compiled template file
$smarty->error_reporting = E_ALL ^ E_NOTICE;

$smarty->assign(array('SITE_PATH'       => SITE_PATH,
                      'SITE_URL'        => $config['SITE_URL'],
                      'SITE_NAME'       => $config['SITE_NAME'],
                      'Registry'        => $registry,
                      'CurrentTemplate'	=> $currentTemplate,
                      'TplDir'          => $config['SITE_URL'] . 'application/views/' . $currentTemplate . '/',
                      'AssetDir'        => $config['SITE_URL'] . 'application/assets/' . $currentTemplate . '/',
                      'Config'          => $config,
                      'DIR_SEP'         => DIR_SEP,
                      //'IsLogin'         =>  $me->is_login()
                ));

$registry->smarty = $smarty;
$registry->CurrentTemplate = $currentTemplate;