
<?php foreach ($metaTags as $name => $content): ?>
<meta name="<?php echo $name; ?>" content="<?php echo $content; ?>" />
<?php endforeach; ?>

<title><?php echo $title; ?></title>

<base href="<?php echo $base_url; ?>" />
<link rel="shortcut icon" href="<?php echo $base_url; ?>assets/images/favicon.ico" type="image/x-icon" />

<!-- CSS / JS Includes [Start]-->
<?php foreach ($styleSheets as $stylesheet): ?>
<link type="text/css" href="<?php echo $stylesheet; ?>" rel="stylesheet" />
<?php endforeach; ?>
<!-- CSS / JS Includes [End] -->
