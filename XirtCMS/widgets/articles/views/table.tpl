
<!-- xWidget-Articles [Start] //-->
<div class="xcms-widget x-widget-articles <?php echo $css_name; ?>">

	<?php if ($show_title): ?>
	<h2><?php echo $title; ?></h2>
	<?php endif; ?>

	<table>
	<?php foreach ($articles as $article): ?>
	<tr>
		<td class="title"><a href="<?php echo $article->link; ?>"><?php echo $article->title; ?></a></td>
		<td class="date"><?php echo $article->date; ?></td>
	</tr>
	<?php endforeach; ?>
	</table>
    
	<?php if ($show_more): ?>
	<a href="<?php echo $show_more; ?>" title="Show more articles" class="show-more">Show more articles &raquo;</a>
	<?php endif; ?>
	
</div>
<!-- xWidget-Articles [End] //-->
