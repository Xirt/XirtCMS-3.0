
<!-- xSitemap [Start] //-->
<div class="xcms-module xcms-sitemap <?php echo $css_name; ?>">

<?php if ($show_title): ?>
    <h1>Sitemap</h1>
<?php endif; ?>

<?php foreach ($menus as $menu): ?>

	<h2><?php echo $menu->get("name"); ?></h2>
	<ul>
		<?php foreach ($menu->items as $item): ?>
		<li><a href="<?php echo $item->target; ?>"><?php echo $item->name; ?></a></li>
		<?php endforeach; ?>
	</ul>

<?php endforeach; ?>

</div>
<!-- xSitemap [End] //-->
