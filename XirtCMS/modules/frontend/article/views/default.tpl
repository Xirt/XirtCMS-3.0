
<!-- xArticle [Start] //-->
<article class="xcms-module xcms-article <?php echo $css_name; ?>">

	<?php if ($show_title): ?>
	<h1><?php echo $article->title; ?></h1>
	<?php endif; ?>
	
	<?php XCMS_RenderEngine::widget("module_top"); ?>

	<section class="author">
		<?php if ($show_author): ?>
			By <span itemprop="author"><?php echo $author->name; ?></span><?php if ($show_author && $show_created): ?>,<?php endif; ?>
		<?php endif; ?>

		<?php if ($show_created): ?>
			<span itemprop="datePublished" content="<? echo $article->dt_created; ?>"><?php echo $article->dt_created->format("l j F Y H:i"); ?></span>
		<?php endif; ?>
	</section>
	
	<?php echo $article->content; ?>
	
	<?php XCMS_RenderEngine::widget("module_bottom"); ?>

</article>
<!-- xArticle [End] //-->
