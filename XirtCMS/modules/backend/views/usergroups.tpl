<h1>Usergroups</h1>

<table id="grid-basic" class="table table-sm table-hover table-striped xgrid-table" data-toggle="bootgrid">
<thead>
	<tr>
		<th data-column-id="id" data-header-css-class="table-row-id align-middle text-center" data-css-class="table-row-id align-middle text-center">ID #</th>
		<th data-column-id="name" data-header-css-class="table-row-name align-middle" data-css-class="table-row-name align-middle" data-visible-in-selection="true">Name</th>
		<th data-column-id="authorization_level" data-header-css-class="table-row-authorization_level align-middle text-center" data-css-class="table-row-authorization_level align-middle text-center" data-visible-in-selection="true">Level</th>
		<th data-column-id="users" data-header-css-class="table-row-users align-middle text-center" data-css-class="table-row-users align-middle text-center" data-visible-in-selection="true" data-visible="1000">Users</th>
		<th data-column-id="commands" data-header-css-class="table-row-commands" data-css-class="table-row-commands text-right" data-visible-in-selection="false"></th>
	</tr>
</thead>
<tbody></tbody>
<tfoot>
<tr>
	<td>
		<button class="btn btn-sm btn-success btn-create">
			<i class="fa fa-plus-square"></i>
			New usergroup
		</button>
	</td>
</tr>
</tfoot>
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

					<button type="submit" class="btn btn-sm btn-success">
						<span class="fas fa-spinner fa-spin fa-1x fa-fw"></span>
						<!-- <i class="fas fa-save"></i> //-->
						Create
					</button>

					<button type="button" class="btn btn-sm btn-primary btn-close">
						<!-- <i class="fas fa-1x fa-undo"></i> //-->
						Cancel
					</button>

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

					<button type="submit" class="btn btn-sm btn-success">
						<span class="fas fa-spinner fa-spin fa-1x fa-fw"></span>
						<!-- <i class="fas fa-save"></i> //-->
						Save
					</button>

					<button type="button" class="btn btn-sm btn-primary btn-close">
						<!-- <i class="fas fa-1x fa-undo"></i> //-->
						Cancel
					</button>

				</div>

			</div>

		</form>

	</div>

</div>