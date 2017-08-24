
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
	
</div>
<!-- xWidget-Articles [End] //-->
