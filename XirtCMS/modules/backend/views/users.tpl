<h1>

	Users
	<div class="btn-group create" />
		<button class="btn btn-sm btn-success btn-create">Add New</button>
	</div>

</h1>

<table id="grid-basic" class="table table-sm table-hover table-striped" data-toggle="bootgrid">
<thead>
	<tr>
		<th data-column-id="id" data-header-css-class="table-row-id align-middle text-center" data-css-class="table-row-id align-middle text-center" data-converter="identifier" data-order="asc">ID #</th>
		<th data-column-id="username" data-header-css-class="table-row-username align-middle" data-css-class="table-row-username align-middle">Username</th>
		<th data-column-id="real_name" data-header-css-class="table-row-real_name align-middle text-center" data-css-class="table-row-real_name align-middle text-center">Name</th>
		<th data-column-id="email" data-header-css-class="table-row-email align-middle text-center" data-css-class="table-row-email align-middle text-center">E-mail</th>
		<th data-column-id="usergroup" data-header-css-class="table-row-usergroup align-middle text-center" data-css-class="table-row-usergroup align-middle text-center">Usergroup</th>
		<th data-column-id="dt_created" data-header-css-class="table-row-dt_created align-middle text-center" data-css-class="table-row-dt_created align-middle text-center">Created</th>
		<th data-column-id="commands" data-header-css-class="table-row-commands" data-css-class="table-row-commands text-center" data-formatter="commands" data-visible-in-selection="false" data-sortable="false">&nbsp;</th>
	</tr>
</thead>
</table>

<div id="createModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">

	<div class="modal-dialog" role="document">

		<form id="form-create" action="backend/user/create" method="post" data-toggle="validator">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">User Creation</h5>

				</div>

				<div class="modal-body">

					<div class="form-group row">

						<label for="create_username" class="col-sm-4 col-form-label col-form-label-sm">Username</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm" id="create_username" name="user_username" placeholder="e.g. johndoe" required />
						</div>

					</div>

					<div class="form-group row">

						<label for="create_real_name" class="col-sm-4 col-form-label col-form-label-sm">Real Name</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm" id="create_real_name" name="user_real_name" placeholder="e.g. John Doe" required />
						</div>

					</div>

					<div class="form-group row">

						<label for="create_email" class="col-sm-4 col-form-label col-form-label-sm">E-mail address</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm" id="create_email" name="user_email" placeholder="e.g. yourname@yourdomain.com" required />
						</div>

					</div>

					<div class="form-group row">

						<label for="create_usergroup_id" class="col-sm-4 col-form-label col-form-label-sm">User Group</label>
						<div class="col-sm-8">
						<select class="form-control form-control-sm custom-select" id="create_usergroup_id" name="user_usergroup_id" required>
							<?php foreach ($usergroups as $usergroup) { ?>
							<option value="<?php echo $usergroup->get("id"); ?>"><?php echo $usergroup->get("name"); ?></option>
							<?php } ?>
						</select>
						</div>

					</div>

				</div>

				<div class="modal-footer">

					<button type="submit" class="btn btn-sm btn-success"><span class="fa fa-refresh fa-spin fa-1x fa-fw"></span>Create user</button>
					<button type="button" class="btn btn-sm btn-primary btn-close">Close</button>

				</div>

			</div>

		</form>

	</div>

</div>


<div id="modifyModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">

	<div class="modal-dialog" role="document">

		<form id="form-modify" action="backend/user/modify" method="post" data-toggle="validator">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">User Modification</h5>

				</div>

				<div class="modal-body">

					<div class="form-group row">

						<label class="col-sm-4 col-form-label col-form-label-sm">ID #</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm input-info" name="user_id" required disabled="disabled" />
						</div>

					</div>

					<div class="form-group row">

						<label for="modify_username" class="col-sm-4 col-form-label col-form-label-sm">Username</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm" id="modify_username" name="user_username" placeholder="e.g. johndoe" required />
						</div>

					</div>

					<div class="form-group row">

						<label for="modify_real_name" class="col-sm-4 col-form-label col-form-label-sm">Real Name</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm" id="modify_real_name" name="user_real_name" placeholder="e.g. John Doe" required />
						</div>

					</div>

					<div class="form-group row">

						<label for="modify_email" class="col-sm-4 col-form-label col-form-label-sm">E-mail address</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm" id="modify_email" name="user_email" placeholder="e.g. yourname@yourdomain.com" required />
						</div>

					</div>

					<div class="form-group row">

						<label for="modify_usergroup_id" class="col-sm-4 col-form-label col-form-label-sm">User Group</label>
						<div class="col-sm-8">
						<select class="form-control form-control-sm custom-select" id="modify_usergroup_id" name="user_usergroup_id" required>
							<?php foreach ($usergroups as $usergroup) { ?>
							<option value="<?php echo $usergroup->get("id"); ?>"><?php echo $usergroup->get("name"); ?></option>
							<?php } ?>
						</select>
						</div>

					</div>

				</div>

				<div class="modal-footer">

					<button type="submit" class="btn btn-sm btn-success"><span class="fa fa-refresh fa-spin fa-1x fa-fw"></span>Save changes</button>
					<button type="button" class="btn btn-sm btn-primary btn-close">Close</button>

				</div>

			</div>

		</form>

	</div>

</div>

<div id="attrModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog" role="document">

		<form id="form-attr" action="backend/user/modify_attributes" method="post" data-toggle="validator">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">User Attributes</h5>

				</div>

				<div class="modal-body">

					<div class="form-group row">

						<label class="col-sm-4 col-form-label col-form-label-sm">ID #</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm input-info" name="user_id" required disabled="disabled" />
						</div>

					</div>

					<div class="form-group row">

						<label class="col-sm-4 col-form-label col-form-label-sm">Username</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm input-info" name="user_username" disabled="disabled" />
						</div>

					</div>

					<div id="attrBox"></div>

				</div>

				<div class="modal-footer">

					<button type="submit" class="btn btn-sm btn-success"><span class="fa fa-refresh fa-spin fa-1x fa-fw"></span>Save changes</button>
					<button type="button" class="btn btn-sm btn-primary btn-close">Close</button>

				</div>

			</div>

		</form>

	</div>

</div>

<div id="passwordModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">

	<div class="modal-dialog" role="document">

		<form id="form-password" action="backend/user/reset_password" method="post" data-toggle="validator">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">Password Modification</h5>

				</div>

				<div class="modal-body">

					<div class="form-group row">

						<label class="col-sm-4 col-form-label col-form-label-sm">ID #</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm input-info" name="user_id" required disabled="disabled" />
						</div>

					</div>

					<div class="form-group row">

						<label class="col-sm-4 col-form-label col-form-label-sm">Username</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm input-info" name="user_username" disabled="disabled" />
						</div>

					</div>

					<div class="form-group row">

						<label for="user_password" class="col-sm-4 col-form-label col-form-label-sm">New password</label>
						<div class="col-sm-8">
							<input type="password" class="form-control form-control-sm" id="user_password" name="user_password" />
						</div>

					</div>

					<div class="form-group row">

						<label for="user_password_check" class="col-sm-4 col-form-label col-form-label-sm">Confirm Password</label>
						<div class="col-sm-8">
							<input type="password" class="form-control form-control-sm" id="user_password_check" name="user_password_check" />
						</div>

					</div>

				</div>

				<div class="modal-footer">

					<button type="submit" class="btn btn-sm btn-success"><span class="fa fa-refresh fa-spin fa-1x fa-fw"></span>Save changes</button>
					<button type="button" class="btn btn-sm btn-primary btn-close">Close</button>

				</div>

			</div>

		</form>

	</div>

</div>