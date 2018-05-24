<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>

		<atom:link href="<?php echo $channel->url; ?>" rel="self" type="application/rss+xml" />
		<link><?php echo $channel->url; ?></link>
		<title><?php echo $channel->title; ?></title>
		<description><?php echo $channel->desc; ?></description>
		<lastBuildDate><?php echo $channel->buildDate; ?></lastBuildDate>
		<pubDate><?php echo $channel->pubDate; ?></pubDate>
		<generator><?php echo $channel->generator; ?></generator>

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