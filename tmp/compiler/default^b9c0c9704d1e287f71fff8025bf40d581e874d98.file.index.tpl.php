<?php /* Smarty version Smarty-3.1.12, created on 2013-03-19 16:49:21
         compiled from "P:\xampp\htdocs\vipget.net\application\assets\default\_controller\home\index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2764251488901261e57-80446664%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b9c0c9704d1e287f71fff8025bf40d581e874d98' => 
    array (
      0 => 'P:\\xampp\\htdocs\\vipget.net\\application\\assets\\default\\_controller\\home\\index.tpl',
      1 => 1363708015,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2764251488901261e57-80446664',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'ImgDir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_514889015c8aa4_93549993',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_514889015c8aa4_93549993')) {function content_514889015c8aa4_93549993($_smarty_tpl) {?>        <div id="input">
            <form method="POST" action="#">
                <input class="left" type="text" name="url" placeholder="Enter your file hosting download to generator premium link..." required />
                <input type="submit" value=" " />
            </form>
        </div>
        <div id="advertisment">
            <div id="top-ads">
            </div>
            <div id="main-ads">
            </div>
        </div>
        <div id="main-content">
            <h1>WELCOME TO VIPGET.NET !</h1>
            <p>VipGet.net is an downloader help you download with max speed and instant from any paid file host on internet.</p>
            <p>With our service, you say no waiting, no paid, unlimited bandwith with free-ads</p>
            <p>Other way, you can pay a cheapest cost to keep service alive and more deals such as: no ads, unlimited download,...</p>
            <div id="features">
                <div class="feature left">
                    <span>Speed</span>
                    <p><a href="#"><img src="<?php echo $_smarty_tpl->tpl_vars['ImgDir']->value;?>
speed.png" alt="" /></a></p>
                    <p>Download at max speed connection</p>
                </div>
                <div class="feature left">
                    <span>Unlimited</span>
                    <p><a href="#"><img src="<?php echo $_smarty_tpl->tpl_vars['ImgDir']->value;?>
unlimited.png" alt="" /></a></p>
                    <p>Download without any limit</p>
                </div>
                <div class="feature left">
                    <span>Price</span>
                    <p><a href="#"><img src="<?php echo $_smarty_tpl->tpl_vars['ImgDir']->value;?>
price.png" alt="" /></a></p>
                    <p>Our service are FREE</p>
                    <p>We also provide paid to keep alive and premium account</p>
                </div>
            </div>
        </div><?php }} ?>