<?xml version="1.0" encoding="UTF-8" ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

	<?php foreach ($items as $item): ?>
		<?php if ($item->level < 10): ?>

		<url>
			<loc><?php echo $baseURL . $item->target; ?></loc>
			<priority><?php echo max(1.0 - $item->level / 10, 0.0); ?></priority>
		</url>

		<?php endif; ?>
	<?php endforeach; ?>
	
</urlset>