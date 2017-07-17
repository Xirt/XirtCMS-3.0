
<!-- xMenu [Start] //-->
<nav class='x-mod-menu <?php echo $config->css_name; ?>'>

	<a href="#" class="menu-toggle"></a>

	<?php foreach ($menu as $node): ?>
	
		<?php if ($node->type == "seperator"): ?>
			<div class="menu-seperator">
			<?php print($config->separator); ?>
			</div>
		<?php else: ?>
			<div class="menu-item menu-item-<?php print($node->node_id); ?> <?php print($node->active ? "active" : "inactive"); ?>">
			<?php print($node->link); ?>
			</div>
		<?php endif; ?>
		
	<?php endforeach; ?>

</nav>
<!-- xMenu [End] //-->