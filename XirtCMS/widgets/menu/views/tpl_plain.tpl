
<!-- xMenu [Start] //-->
<nav class='x-mod-menu <?php echo $config->css_name; ?>'>

	<a href="#" class="menu-toggle"></a>

	<?php foreach ($menu as $menuEntry): ?>
	
		<?php if ($menuEntry->type == "seperator"): ?>
			<?php print($config->separator); ?>
		<?php endif; ?>
	
		<?php if ($menuEntry->type == "item"): ?>
			<?php print($menuEntry->link); ?>
		<?php endif; ?>
	
	<?php endforeach; ?>

</nav>
<!-- xMenu [End] //-->