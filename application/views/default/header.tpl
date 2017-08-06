<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>{#siteTitle#}</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="robots" content="index,follow"/>
    <meta name="googlebot" content="index,follow"/>
    <META name="description" content="{#siteDescription#}">
    <META name="keywords" content="{#siteKeyWord#}" />
    <meta name="google-site-verification" content="B5KfJPg8jk3iJkzUTDxlNPY5RavihVfcGNUtTEwuDmY" />
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="{$AssetDir}css/reset.css" />
    <link rel="stylesheet" type="text/css" href="{$AssetDir}css/style.css" />

    <script type="text/javascript">
        var SITE_URL = '{$SITE_URL}';
    </script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script type="text/javascript" src="{$AssetDir}js/jquery.collapse.js"></script>
    <script type="text/javascript" src="{$AssetDir}js/jquery.blockUI.js"></script>
    <script type="text/javascript" src="{$AssetDir}js/urlparse.js"></script>
    <script type="text/javascript" src="{$AssetDir}js/main.js"></script>
</head>
<body>
<div id="wrapper">
    <div id="header">
        <div id="top">
            <div id="nav">
                <ul>
                    <li><a {if ($Registry->controller eq 'home') and (($Registry->action eq 'index') or ($Registry->action eq 'term') or ($Registry->action eq 'privacy'))} class="active" {/if} href="{$SITE_URL}home">Home</a></li>
                    <li><a {if ($Registry->controller eq 'generator')} class="active" {/if} href="{$SITE_URL}generator">Generator</a></li>
                    <li><a {if ($Registry->controller eq 'home') and ($Registry->action eq 'howtouse')} class="active" {/if} href="{$SITE_URL}home/howtouse">How to use</a></li>
                    <li><a {if ($Registry->controller eq 'home') and ($Registry->action eq 'faq')} class="active" {/if} href="{$SITE_URL}home/faq">FAQ</a></li>
                    <li><a {if ($Registry->controller eq 'home') and ($Registry->action eq 'contact')} class="active" {/if} href="{$SITE_URL}home/contact">Contact us</a></li>
                </ul>
                <ul class="right">
                    <li class="right"><a href="#login">Login</a> / <a href="#signup">Sign up</a></li>
                </ul>
            </div>
        </div><!--end top-->
        <div id="bottom">
            <div id="logo" class="left">
                <a href="{$SITE_URL}home"><img src="{$AssetDir}images/logo.png" alt="{#siteDescription#}" title="{#siteDescription#}" /></a>
            </div><!--end logo-->
            <div id="ads" class="left">
                <p class="red bigtxt">YOUR ADS HERE</p>
                <p class="black bigtxt">270x70px</p>
            </div>
            <div id="toolbar" class="right">
                <a href="javascript:window.open('http://vipget.net/?url='+this.location);void(0);" title="Fetch It!"><img src="{$AssetDir}images/toolbar.png" alt="Fetch It!"></a>
            </div>
        </div><!--end bottom-->
        <div class="clear" ></div>
    </div><!--end header-->