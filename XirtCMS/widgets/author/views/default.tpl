
<!-- xAuthor [Start] //-->
<div class="x-widget-author <?php echo $config->css_name; ?>">

	<h3>About the author</h3>

	<img src="<?php print_r($author->avatar); ?>" alt="<?php echo $author->name; ?>" />
	
	<?php if ($config->show_name): ?>
		<h3><?php print($author->name); ?></h3>
	<?php endif; ?>
	
	<p><?php print($author->introduction); ?></p>

</div>
<!-- xAuthor [End] //-->
