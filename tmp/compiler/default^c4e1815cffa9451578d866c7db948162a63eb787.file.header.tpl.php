<?php /* Smarty version Smarty-3.1.13, created on 2013-03-24 19:40:15
         compiled from "P:\xampp\htdocs\vipget.net\application\views\default\header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:8251489b3170fae9-44663716%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c4e1815cffa9451578d866c7db948162a63eb787' => 
    array (
      0 => 'P:\\xampp\\htdocs\\vipget.net\\application\\views\\default\\header.tpl',
      1 => 1364150281,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8251489b3170fae9-44663716',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51489b31750271_38000512',
  'variables' => 
  array (
    'AssetDir' => 0,
    'SITE_URL' => 0,
    'Registry' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51489b31750271_38000512')) {function content_51489b31750271_38000512($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title><?php echo $_smarty_tpl->getConfigVariable('siteTitle');?>
</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="robots" content="index,follow"/>
    <meta name="googlebot" content="index,follow"/>
    <META name="description" content="<?php echo $_smarty_tpl->getConfigVariable('siteDescription');?>
">
    <META name="keywords" content="<?php echo $_smarty_tpl->getConfigVariable('siteKeyWord');?>
" />
    <meta name="google-site-verification" content="" />
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['AssetDir']->value;?>
css/reset.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['AssetDir']->value;?>
css/style.css" />

    <script type="text/javascript">
        var SITE_URL = '<?php echo $_smarty_tpl->tpl_vars['SITE_URL']->value;?>
';
    </script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['AssetDir']->value;?>
js/jquery.collapse.js"></script>
    <script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['AssetDir']->value;?>
js/jquery.blockUI.js"></script>
    <script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['AssetDir']->value;?>
js/urlparse.js"></script>
    <script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['AssetDir']->value;?>
js/main.js"></script>
</head>
<body>
<div id="wrapper">
    <div id="header">
        <div id="top">
            <div id="nav">
                <ul>
                    <li><a <?php if (($_smarty_tpl->tpl_vars['Registry']->value->controller=='home')&&(($_smarty_tpl->tpl_vars['Registry']->value->action=='index')||($_smarty_tpl->tpl_vars['Registry']->value->action=='term')||($_smarty_tpl->tpl_vars['Registry']->value->action=='privacy'))){?> class="active" <?php }?> href="<?php echo $_smarty_tpl->tpl_vars['SITE_URL']->value;?>
home">Home</a></li>
                    <li><a <?php if (($_smarty_tpl->tpl_vars['Registry']->value->controller=='generator')){?> class="active" <?php }?> href="<?php echo $_smarty_tpl->tpl_vars['SITE_URL']->value;?>
generator">Generator</a></li>
                    <li><a <?php if (($_smarty_tpl->tpl_vars['Registry']->value->controller=='home')&&($_smarty_tpl->tpl_vars['Registry']->value->action=='howtouse')){?> class="active" <?php }?> href="<?php echo $_smarty_tpl->tpl_vars['SITE_URL']->value;?>
home/howtouse">How to use</a></li>
                    <li><a <?php if (($_smarty_tpl->tpl_vars['Registry']->value->controller=='home')&&($_smarty_tpl->tpl_vars['Registry']->value->action=='faq')){?> class="active" <?php }?> href="<?php echo $_smarty_tpl->tpl_vars['SITE_URL']->value;?>
home/faq">FAQ</a></li>
                    <li><a <?php if (($_smarty_tpl->tpl_vars['Registry']->value->controller=='home')&&($_smarty_tpl->tpl_vars['Registry']->value->action=='contact')){?> class="active" <?php }?> href="<?php echo $_smarty_tpl->tpl_vars['SITE_URL']->value;?>
home/contact">Contact us</a></li>
                </ul>
                <ul class="right">
                    <li class="right"><a href="#login">Login</a> / <a href="#signup">Sign up</a></li>
                </ul>
            </div>
        </div><!--end top-->
        <div id="bottom">
            <div id="logo" class="left">
                <a href="<?php echo $_smarty_tpl->tpl_vars['SITE_URL']->value;?>
home"><img src="<?php echo $_smarty_tpl->tpl_vars['AssetDir']->value;?>
images/logo.png" alt="<?php echo $_smarty_tpl->getConfigVariable('siteDescription');?>
" title="<?php echo $_smarty_tpl->getConfigVariable('siteDescription');?>
" /></a>
            </div><!--end logo-->
            <div id="ads" class="left">
                <p class="red bigtxt">YOUR ADS HERE</p>
                <p class="black bigtxt">270x70px</p>
            </div>
            <div id="toolbar" class="right">
                <a href="javascript:window.open('http://vipget.net/?url='+this.location);void(0);" title="Fetch It!"><img src="<?php echo $_smarty_tpl->tpl_vars['AssetDir']->value;?>
images/toolbar.png" alt="Fetch It!"></a>
            </div>
        </div><!--end bottom-->
        <div class="clear" ></div>
    </div><!--end header--><?php }} ?>