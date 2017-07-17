
<ul>
<?php foreach ($menu as $node): ?>

	<?php if ($node->type == "seperator"): ?>
		<li><?php print($config->separator); ?></li>
	<?php endif; ?>

	<?php if ($node->type == "item"): ?>
		<li class="<?php print($node->classes); ?>">
		<?php print($node->link); ?>

		<?php if ($node->children): ?>
			<?php $this->view("tpl_list_level", array(
				"menu" => $node->children
			)); ?>
		<?php endif; ?>
		</li>
		
	<?php endif; ?>

<?php endforeach; ?>
</ul>
