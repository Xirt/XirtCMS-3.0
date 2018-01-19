<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?php XCMS_Page::getInstance()->addscript("assets/third-party/bootstrap/js/popper.min.js", true); ?>
<?php XCMS_Page::getInstance()->addscript("assets/third-party/bootstrap/js/bootstrap.min.js", true); ?>
<?php XCMS_Page::getInstance()->addscript("assets/third-party/lightbox/js/lightbox.min.js", true); ?>
<?php XCMS_Page::getInstance()->addscript("assets/third-party/prism/js/prism.min.js", true); ?>
<?php XCMS_Page::getInstance()->addStylesheet("assets/third-party/bootstrap/css/bootstrap.min.css"); ?>
<?php XCMS_Page::getInstance()->addStylesheet("assets/third-party/lightbox/css/lightbox.min.css"); ?>
<?php XCMS_Page::getInstance()->addStylesheet("assets/third-party/prism/css/prism.min.css"); ?>
<?php XCMS_Page::getInstance()->addStylesheet("assets/css/main.min.css"); ?>
<?php XCMS_RenderEngine::header(); ?>

<?php XCMS_RenderEngine::widget("head"); ?>

</head>

<body>

	<div class="container">

		<div class="box-container">

			<div class="box-header">

				<?php XCMS_RenderEngine::widget("menu_top"); ?>

			</div>

			<div class="box-shade"></div>

			<div class="box-body row">

				<!-- xContent [Start] //-->

				<?php XCMS_RenderEngine::module(); ?>
				<?php XCMS_RenderEngine::widget("content"); ?>

				<!-- xContent [End] //-->

				<div class="col col-md-4">

					<!-- xSidebar [Start] //-->
					<div class="x-sidebar">

					<?php XCMS_RenderEngine::widget("sidebar"); ?>

					</div>
					<!-- xSidebar [End] //-->

				</div>

			</div>

		</div>

		<div class="box-bottom">
			XirtCMS - Copyright &copy; 2018. All rights reserved.
			<span class="no-mobile"> | <a href="http://www.gnu.org/licenses/gpl.html" title="GNU General Public License v3.0" class="external">GNU General Public License v3.0</a></span>

			<?php XCMS_RenderEngine::widget("menu_bottom"); ?>

		</div>

	</div>

	<?php XCMS_RenderEngine::footer(); ?>

</body>

</html>