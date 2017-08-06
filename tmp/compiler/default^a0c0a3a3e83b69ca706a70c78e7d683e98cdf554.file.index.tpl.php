<?php /* Smarty version Smarty-3.1.13, created on 2013-03-19 18:16:04
         compiled from "P:\xampp\htdocs\vipget.net\application\views\default\index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:329351489b31680797-17559553%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a0c0a3a3e83b69ca706a70c78e7d683e98cdf554' => 
    array (
      0 => 'P:\\xampp\\htdocs\\vipget.net\\application\\views\\default\\index.tpl',
      1 => 1356628706,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '329351489b31680797-17559553',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51489b316c2cb1_05851444',
  'variables' => 
  array (
    'smartyControllerContainerRoot' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51489b316c2cb1_05851444')) {function content_51489b316c2cb1_05851444($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['smartyControllerContainerRoot']->value)."header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['smartyControllerContainerRoot']->value)."maincontent.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['smartyControllerContainerRoot']->value)."footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>