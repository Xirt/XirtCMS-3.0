
<!-- xShowcase [Start] //-->
<div class="x-mod-showcase <?php echo $config->css_name; ?>">

	<?php foreach ($images as $thumb => $img): ?>
	<a href="<?php echo $config->folder; ?><?php echo $img; ?>" data-lightbox="lb_img_<?php echo $id; ?>" target="new">
	   <img src="<?php echo $thumb; ?>" alt="<?php echo $config->alt; ?>" />
	</a>
   <?php endforeach; ?>

</div>
<!-- xShowcase [End] //-->
