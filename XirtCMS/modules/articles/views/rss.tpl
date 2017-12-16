<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
	
		<title><?php echo $title; ?></title>
		<description></description>
		<link><?php echo $url; ?></link>
		<atom:link href="<?php echo $url; ?>" rel="self" type="application/rss+xml" />
		<lastBuildDate>Tue, 19 Oct 2004 13:39:14 -0400</lastBuildDate>
		<pubDate>Tue, 19 Oct 2004 13:38:55 -0400</pubDate>
		<generator>XirtCMS</generator>
		
		<?php foreach ($articles as $article): ?>
		<item>
			<link><?php echo $article->link; ?></link>
			<title><?php echo $article->title; ?></title>
			<pubDate><?php echo $article->pubDate; ?></pubDate>
			<description><?php echo $article->intro; ?></description>
			<guid isPermaLink="false"><?php echo $article->guid; ?></guid>
		</item>
		<?php endforeach; ?>

	</channel>
</rss>