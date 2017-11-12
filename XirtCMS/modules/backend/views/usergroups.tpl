<h1>

	Usergroups
	<div class="btn-group create" />
		<button class="btn btn-sm btn-success btn-create">Add New</button>
	</div>

</h1>

<table id="grid-basic" class="table table-sm table-hover table-striped" data-toggle="bootgrid">
<thead>
	<tr>
		<th data-column-id="id" data-header-css-class="table-row-id align-middle text-center" data-css-class="table-row-id align-middle text-center" data-converter="identifier">ID #</th>
		<th data-column-id="name" data-header-css-class="table-row-name align-middle" data-css-class="table-row-name align-middle" data-visible-in-selection="true">Name</th>
		<th data-column-id="authorization_level" data-header-css-class="table-row-authorization_level align-middle text-center" data-css-class="table-row-authorization_level align-middle text-center" data-visible-in-selection="true">Level</th>
		<th data-column-id="users" data-header-css-class="table-row-users align-middle text-center" data-css-class="table-row-users align-middle text-center" data-visible-in-selection="true">Users</th>
		<th data-column-id="commands" data-header-css-class="table-row-commands" data-css-class="table-row-commands text-center" data-formatter="commands" data-visible-in-selection="false">&nbsp;</th>
	</tr>
</thead>
</table>

<div id="createModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">

	<div class="modal-dialog" role="document">

		<form id="form-create" action="backend/usergroup/create" method="post" data-toggle="validator">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">Usergroup Creation</h5>

				</div>

				<div class="modal-body">

					<div class="form-group row">

						<label for="create_name" class="col-sm-4 col-form-label col-form-label-sm">Name</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm" id="create_name" name="usergroup_name" placeholder="" required />
						</div>

					</div>

					<div class="form-group row">

						<label for="create_authorization_level" class="col-sm-4 col-form-label col-form-label-sm">Level</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm" id="create_authorization_level" name="usergroup_authorization_level" placeholder="" required />
						</div>

					</div>

				</div>

				<div class="modal-footer">

					<button type="submit" class="btn btn-sm btn-success"><span class="fa fa-refresh fa-spin fa-1x fa-fw"></span>Create usergroup</button>
					<button type="button" class="btn btn-sm btn-primary btn-close">Close</button>

				</div>

			</div>

		</form>

	</div>

</div>

<div id="modifyModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">

	<div class="modal-dialog" role="document">

		<form id="form-modify" action="backend/usergroup/modify" method="post" data-toggle="validator">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">Modify Usergroup</h5>

				</div>

				<div class="modal-body">

					<div class="form-group row">

						<label class="col-sm-4 col-form-label col-form-label-sm">ID #</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm input-info" name="id" required disabled="disabled" />
						</div>

					</div>

					<div class="form-group row">

						<label for="modify_name" class="col-sm-4 col-form-label col-form-label-sm">Name</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm" id="modify_name" name="usergroup_name" placeholder="" required />
						</div>

					</div>

					<div class="form-group row">

						<label for="modify_authorization_level" class="col-sm-4 col-form-label col-form-label-sm">Level</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm" id="modify_authorization_level" name="usergroup_authorization_level" placeholder="" required />
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