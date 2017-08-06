<?php /* Smarty version Smarty-3.1.12, created on 2013-03-19 16:49:21
         compiled from "P:\xampp\htdocs\vipget.net\application\assets\default\_controller\index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2193151488901629f18-88600193%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd8a3b23f27abab577e362ad0e8b923ae08478e74' => 
    array (
      0 => 'P:\\xampp\\htdocs\\vipget.net\\application\\assets\\default\\_controller\\index.tpl',
      1 => 1356628706,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2193151488901629f18-88600193',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'smartyControllerContainerRoot' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5148890166d3d6_29189212',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5148890166d3d6_29189212')) {function content_5148890166d3d6_29189212($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['smartyControllerContainerRoot']->value)."header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['smartyControllerContainerRoot']->value)."maincontent.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['smartyControllerContainerRoot']->value)."footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>