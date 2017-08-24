
<!-- xWidget-Articles [Start] //-->
<div class="xcms-widget x-widget-articles <?php echo $css_name; ?>">

	<?php if ($show_title): ?>
	<h2><?php echo $title; ?></h2>
	<?php endif; ?>

	<ul>
	<?php foreach ($articles as $article): ?>
		<li>
			<a href="<?php echo $article->link; ?>"><?php echo $article->title; ?></a>
		</li>
	<?php endforeach; ?>
	</ul>
	
</div>
<!-- xWidget-Articles [End] //-->
