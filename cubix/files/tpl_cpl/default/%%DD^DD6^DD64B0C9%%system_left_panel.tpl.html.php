<?php /* Smarty version 2.6.10, created on 2014-12-08 09:50:01
         compiled from system_left_panel.tpl.html */ ?>
<?php 
if($this->_tpl_vars['left_menu']){
	$left_menu = $this->_tpl_vars['left_menu'];
}else{
	$left_menu = "menu.widget.ApplicationMenu";
}

$obj = Openbiz\Openbiz::getObject($left_menu);
$left_menu_content = $obj->render();
//$left_menu_content = $left_menu;

$this->assign('system_left_menu_content', 	$left_menu_content);
 ?>

	<?php if ($this->_tpl_vars['system_left_top_content']):  echo $this->_tpl_vars['system_left_top_content'];  endif; ?>

	<!-- left menu block start -->
	<div class="content_block">
		<div class="header"></div>
		<div class="content" >								
			<?php echo $this->_tpl_vars['system_left_menu_content']; ?>

		</div>
		<div class="footer"></div>						
	</div>
	<div class="v_spacer"></div>
	<!-- left menu block end -->
	
	<?php $_from = $this->_tpl_vars['forms']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['formname'] => $this->_tpl_vars['form']):
?>	
		<?php if (preg_match ( "/LeftWidget$/si" , $this->_tpl_vars['formname'] )): ?>						    
		<div class="content_block">
			<div class="header"></div>
			<div class="content" >	
		 	
			<div>
			 	<?php echo $this->_tpl_vars['form']; ?>

			</div>
							
			</div>
			<div class="footer"></div>						
		</div>
		<div class="v_spacer"></div>
		<?php endif; ?>
	<?php endforeach; endif; unset($_from); ?>		

	<?php $_from = $this->_tpl_vars['widgets']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['formname'] => $this->_tpl_vars['form']):
?>	
		<?php if (preg_match ( "/LeftWidget$/si" , $this->_tpl_vars['formname'] )): ?>						    
		<div class="content_block">
			<div class="header"></div>
			<div class="content" >	
		 	
			<div>
			 	<?php echo $this->_tpl_vars['form']; ?>

			</div>
							
			</div>
			<div class="footer"></div>						
		</div>
		<div class="v_spacer"></div>
		<?php endif; ?>
	<?php endforeach; endif; unset($_from); ?>	
											
	<!-- left help block start -->
<div>
		<?php   // echo Openbiz\Openbiz::getObject('help.form.HelpWidgetListForm',1)->render(); ?>
</div>				

	<div class="v_spacer"></div>
	<!-- left help block end -->