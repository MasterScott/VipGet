<?php /* Smarty version Smarty-3.1.13, created on 2013-03-23 10:28:40
         compiled from "P:\xampp\htdocs\vipget.net\application\views\default\maincontent.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2025951489b31817fd4-88435019%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5b524b3a3094017b9c9242a3a1117160fec54ed0' => 
    array (
      0 => 'P:\\xampp\\htdocs\\vipget.net\\application\\views\\default\\maincontent.tpl',
      1 => 1364030917,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2025951489b31817fd4-88435019',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51489b31827c21_89743418',
  'variables' => 
  array (
    'Registry' => 0,
    'AssetDir' => 0,
    'contents' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51489b31827c21_89743418')) {function content_51489b31827c21_89743418($_smarty_tpl) {?>    <div id="content">
        <?php if (($_smarty_tpl->tpl_vars['Registry']->value->controller=='home')&&($_smarty_tpl->tpl_vars['Registry']->value->action=='index')){?>
        <div id="input">
            <form id="singleFile" action="#getFile">
                <input class="left" type="text" name="url" placeholder="Enter your file hosting download to generator premium link..." required />
                <input type="submit" value=" " />
            </form>
        </div>
            <div class="loader"><img src="<?php echo $_smarty_tpl->tpl_vars['AssetDir']->value;?>
images/loader/loader.gif" title="Your file is generating"/></div>
            <div id="feedback"></div>
        <?php }?>
        <?php echo $_smarty_tpl->tpl_vars['contents']->value;?>

    </div><?php }} ?>