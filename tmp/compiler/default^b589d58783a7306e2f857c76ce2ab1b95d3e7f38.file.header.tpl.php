<?php /* Smarty version Smarty-3.1.12, created on 2013-03-19 16:49:21
         compiled from "P:\xampp\htdocs\vipget.net\application\assets\default\_controller\header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:17416514889016c2ba9-56432632%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b589d58783a7306e2f857c76ce2ab1b95d3e7f38' => 
    array (
      0 => 'P:\\xampp\\htdocs\\vipget.net\\application\\assets\\default\\_controller\\header.tpl',
      1 => 1363707698,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17416514889016c2ba9-56432632',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'SITE_NAME' => 0,
    'TplDir' => 0,
    'SITE_URL' => 0,
    'ImgDir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_51488901704729_82177165',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51488901704729_82177165')) {function content_51488901704729_82177165($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title><?php echo $_smarty_tpl->tpl_vars['SITE_NAME']->value;?>
</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="robots" content="index,follow"/>
    <meta name="googlebot" content="index,follow"/>
    <META name="description" content="">
    <META name="keywords" content="" />
    <meta name="google-site-verification" content="vfmO1QQ29UcXAnSXR4rFzmll0EdnHk4NwL5RrWxPmBU" />
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['TplDir']->value;?>
css/reset.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['TplDir']->value;?>
css/style.css" />

    <script type="text/javascript">
        var SITE_URL = <?php echo $_smarty_tpl->tpl_vars['SITE_URL']->value;?>
;
    </script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['TplDir']->value;?>
js/main.js"></script>
</head>
<body>
<div id="wrapper">
    <div id="header">
        <div id="top">
            <div id="nav">
                <ul>
                    <li><a class="active" href="#">Home</a></li>
                    <li><a href="#">Generator</a></li>
                    <li><a href="#">How to use</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Contact us</a></li>
                </ul>
                <ul class="right">
                    <li class="right"><a href="#login">Login</a> / <a href="#signup">Sign up</a></li>
                </ul>
            </div>
        </div><!--end top-->
        <div id="bottom">
            <div id="logo" class="left">
                <a href="#"><img src="<?php echo $_smarty_tpl->tpl_vars['ImgDir']->value;?>
logo.png" alt="Logo" /></a>
            </div><!--end logo-->
            <div id="ads" class="left">
                <p class="red bigtxt">YOUR ADS HERE</p>
                <p class="black bigtxt">270x70px</p>
            </div>
            <div id="toolbar" class="right">
            </div>
        </div><!--end bottom-->
        <div class="clear" ></div>
    </div><!--end header--><?php }} ?>