
<!-- xMenu [Start] //-->
<nav class='x-mod-menu <?php echo $config->css_name; ?>'>

	<label class="menu-toggle" for="x-mod-menu-<?php echo $config->css_name; ?>"></label>
	<input type="checkbox" id="x-mod-menu-<?php echo $config->css_name; ?>" />

	<?php $this->view("tpl_list_level"); ?>

</nav>
<!-- xMenu [End] //-->