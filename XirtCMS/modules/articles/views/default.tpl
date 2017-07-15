
<!-- xArticles [Start] //-->
<div class="xcms-module xcms-articles <?php echo $css_name; ?>">

	<?php if ($show_title): ?>
	<h1><?php echo $title; ?></h1>
	<?php endif; ?>
	
	<?php XCMS_RenderEngine::widget("module_top"); ?>

	<?php foreach ($articles as $article): ?>
	<article>

		<h2><?php echo $article->title; ?></h2>
		<section>

			<?php echo $article->introduction; ?>
			<a href="<?php echo $article->link; ?>">Read more &raquo;</a>

		</section>

	</article>
	<?php endforeach; ?>
	
	<?php XCMS_RenderEngine::widget("module_bottom"); ?>

</div>
<!-- xArticles [End] //-->
