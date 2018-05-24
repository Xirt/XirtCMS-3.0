<form id="form-login" action="backend/authentication/authenticate" class="modal-content" method="post">

	<div class="modal-header">

		<h5 class="modal-title">Authentication</h5>

	</div>

	<div class="modal-body">

		<div class="form-group row">

			<label for="user_name" class="col-sm-4 col-form-label col-form-label-sm">Username</label>
			<div class="col-sm-8">
				<input type="text" class="form-control form-control-sm" id="user_name" name="user_name" placeholder="Username" />
			</div>

		</div>

		<div class="form-group row">

			<label for="user_password" class="col-sm-4 col-form-label col-form-label-sm">Password</label>
			<div class="col-sm-8">
				<input type="password" class="form-control form-control-sm" id="user_password" name="user_password" placeholder="Password" />
			</div>

		</div>

		<div class="form-group row">

			<label for="user_cookies" class="col-sm-4 col-form-label col-form-label-sm">Type</label>
			<div class="col-sm-8">
				<input type="checkbox" name="user_cookies" id="user_cookies" data-on="Persistent login" data-off="Single session" data-onstyle="default" data-offstyle="default" data-toggle="toggle" data-size="small" />
			</div>

		</div>

	</div>

	<div class="modal-footer">

		<button type="submit" class="btn btn-sm btn-success">
			<span class="fas fa-spinner fa-spin fa-1x fa-fw"></span>
			<!-- <i class="fas fa-save"></i> //-->
			Login
		</button>

		<button type="button" class="btn btn-sm btn-primary btn-request">
			<!-- <i class="fas fa-1x fa-question-circle"></i> //-->
			Request Password
		</button>

	</div>

</form>

<form id="form-request" action="backend/authentication/reset_password" class="modal-content" method="post" style="display: none;">

	<div class="modal-header">

		<h5 class="modal-title">Request Password</h5>

	</div>

	<div class="modal-body">

		<div class="form-group row">

			<label for="request_name" class="col-sm-4 col-form-label col-form-label-sm">Username</label>
			<div class="col-sm-8">
				<input type="text" class="form-control form-control-sm" id="request_name" name="request_name" placeholder="Username" required />
			</div>

		</div>

		<div class="form-group row">

			<label for="request_email" class="col-sm-4 col-form-label col-form-label-sm">E-mail address</label>
			<div class="col-sm-8">
				<input type="text" class="form-control form-control-sm" id="request_email" name="request_email" placeholder="E-mail address" required />
			</div>

		</div>

	</div>

	<div class="modal-footer">

		<button type="submit" class="btn btn-sm btn-success">
			<span class="fas fa-spinner fa-spin fa-1x fa-fw"></span>
			<!-- <i class="fas fa-sign-out-alt"></i> //-->
			Request
		</button>

		<button type="button" class="btn btn-sm btn-primary btn-login">
			<!-- <i class="fas fa-1x fa-undo"></i> //-->
			Cancel
		</button>

	</div>

</form>