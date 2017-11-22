
<?php foreach ($menu as $node): ?>

	<?php if ($node->type == "seperator"): ?>
	<li class="seperator"><?php print($config->separator); ?></li>
	<?php elseif ($node->active): ?>
    <li class="nav-item active">
        <?php print($node->link); ?>
    </li>
    <?php else: ?>
    <li class="nav-item">
        <?php print($node->link); ?>
    </li>    
    <?php endif; ?>
    
<?php endforeach; ?>
