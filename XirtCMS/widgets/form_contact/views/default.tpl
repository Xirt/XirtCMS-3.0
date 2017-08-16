
<!-- xFormContact [Start] //-->
<script src="assets/scripts/widgets/form_contact.js"></script>
<section class="x-widget-form_contact <?php echo $config->css_name; ?>">

    <?php if ($config->title): ?>
	<h2><?php echo $config->title; ?></h2>
    <?php endif; ?>
    
	<form id="formContactBox" action="contact/post" method="post">

		<fieldset class="box-contact">

			<div class="form-group row">
				<label for="title" class="col-sm-5 col-form-label">Title</label>
				<div class="col-sm-3">
					<select name="title" class="form-control" id="title">
						<option value="0" id="title-0">Sir</option>
						<option value="1" id="title-1">Madam</option>
					</select>
				</div>
			</div>

			<div class="form-group row">
				<label for="name" class="col-sm-5 col-form-label">Name<b>*</b></label>
				<div class="col-sm-7">
					<input type="text" class="form-control" id="name" name="name" maxlength="50" class="required" />
				</div>
			</div>

			<div class="form-group row">
				<label for="company" class="col-sm-5 col-form-label">Company</label>
				<div class="col-sm-7">
					<input type="text" class="form-control" id="company" name="company" maxlength="50" />
				</div>
			</div>

			<div class="form-group row">
				<label for="email" class="col-sm-5 col-form-label">E-mail address<b>*</b></label>
				<div class="col-sm-7">
					<input type="text" class="form-control" id="mail" name="email" maxlength="50" class="required email" />
				</div>
			</div>

			<div class="form-group row">
				<label for="phone" class="col-sm-5 col-form-label">Phone number</label>
				<div class="col-sm-7">
					<input type="text" class="form-control" id="phone" name="phone" maxlength="25" class="phone" />
				</div>
			</div>

			<div class="form-group row">
				<label for="subject" class="col-sm-5 col-form-label">Subject<b>*</b></label>
				<div class="col-sm-7">
					<input type="text" class="form-control" id="subject" name="subject" maxlength="50" class="required" />
				</div>
			</div>

			<div class="form-group row col-sm-12">
				<label for="content">Your message<b>*</b></label><br/>
				<div>
					<textarea id="content" name="content" class="form-control required"></textarea>
				</div>
			</div>

			<span class="required col-sm-12">Items marked with an asterisk (<b>*</b>) are required.</span>

		</fieldset>

		<div class="form-group row box-buttons col-sm-12">

			<button type="submit" class="btn btn-primary">Send</button>
			<button type="reset" class="btn btn-default">Reset</button>

		</div>

	</form>

</section>
<!-- xFormContact [End] //-->
