<h1>

	Module Configurations
	<div class="btn-group create" />
		<button class="btn btn-sm btn-success btn-create">Add New</button>
	</div>

</h1>

<table id="grid-basic" class="table table-sm table-hover table-striped" data-toggle="bootgrid">
<thead>
	<tr>
		<th data-column-id="id" data-header-css-class="table-row-item_id align-middle text-center" data-css-class="table-row-item_id align-middle text-center" data-converter="identifier" data-order="asc">ID #</th>
		<th data-column-id="name" data-header-css-class="table-row-name align-middle" data-css-class="table-row-name align-middle" data-formatter="name" data-visible-in-selection="false">Name</th>
		<th data-column-id="type" data-header-css-class="table-row-type align-middle text-center" data-css-class="table-row-type align-middle text-center">Type</th>
		<th data-column-id="default" data-header-css-class="table-row-default align-middle text-center" data-css-class="table-row-default align-middle text-center" data-formatter="default" >Default</th>
		<th data-column-id="commands" data-header-css-class="table-row-commands" data-css-class="table-row-commands text-right" data-formatter="commands" data-visible-in-selection="false">&nbsp;</th>
	</tr>
</thead>
</table>

<div id="optionsModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">

	<div class="modal-dialog" role="document">

		<div class="modal-content">

			<div class="modal-header">

				<h5 class="modal-title">What do you want to do?</h5>

			</div>

			<div class="modal-body row">

				<div class="col-sm-6">

					<button class="btn btn-info btn-edit-main">
						<i class="fa fa-user-circle-o fa-5x" aria-hidden="true"></i>
						<p>Modify user</p>
					</button>

				</div>

				<div class="col-sm-6">

					<button class="btn btn-info btn-edit-config">
						<i class="fa fa-gears fa-5x" aria-hidden="true"></i>
						<p>Modify configuration</p>
					</button>

				</div>

			</div>

			<div class="modal-footer">

				<button type="button" class="btn btn-sm btn-default btn-close">Cancel</button>

			</div>

		</div>

	</div>

</div>

<div id="createModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">

	<div class="modal-dialog" role="document">

		<form id="form-create" action="backend/moduleconfiguration/create" method="post" data-toggle="validator">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">Module Configuration Creation</h5>

				</div>

				<div class="modal-body">

					<div class="form-group row">

						<label for="create_name" class="col-sm-4 col-form-label col-form-label-sm">Name</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm" id="create_name" name="configuration_name" placeholder="" required />
						</div>

					</div>

					<div class="form-group row">

						<label for="create_type" class="col-sm-4 col-form-label col-form-label-sm">Module Type</label>
						<div class="col-sm-8">

							<select class="form-control form-control-sm" id="create_type" name="configuration_type">

								<?php foreach ($moduleTypes as $moduleType => $moduleName): ?>
								<option value="<?php echo $moduleType; ?>"><?php echo $moduleName; ?></option>
								<?php endforeach; ?>

							</select>

						</div>

					</div>

				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-sm btn-success"><span class="fa fa-refresh fa-spin fa-1x fa-fw"></span>Create item</button>
					<button type="button" class="btn btn-sm btn-default btn-close">Close</button>
				</div>

			</div>

		</form>

	</div>

</div>

<div id="modifyModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">

	<div class="modal-dialog" role="document">

		<form id="form-modify" action="backend/moduleconfiguration/modify" method="post" data-toggle="validator">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">Model Identifier</h5>

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
							<input type="text" class="form-control form-control-sm" id="modify_name" name="configuration_name" placeholder="" required />
						</div>

					</div>

				</div>

				<div class="modal-footer">

					<button type="submit" class="btn btn-sm btn-success"><span class="fa fa-refresh fa-spin fa-1x fa-fw"></span>Save changes</button>
					<button type="button" class="btn btn-sm btn-default btn-close">Close</button>

				</div>

			</div>

		</form>

	</div>

</div>

<div id="configModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">

	<div class="modal-dialog" role="document">

		<form id="form-config" action="backend/moduleconfiguration/modify_settings" method="post" data-toggle="validator">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">Model Configuration</h5>

				</div>

				<div class="modal-body">

					<div class="form-group row">

						<label class="col-sm-4 col-form-label col-form-label-sm">ID #</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm input-info" name="id" disabled="disabled" />
						</div>

					</div>

					<div class="form-group row">

						<label class="col-sm-4 col-form-label col-form-label-sm">Name</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm input-info" name="name" disabled="disabled" />
						</div>

					</div>

					<div id="settingsBox"></div>

				</div>

				<div class="modal-footer">

					<button type="submit" class="btn btn-sm btn-success"><span class="fa fa-refresh fa-spin fa-1x fa-fw"></span>Save changes</button>
					<button type="button" class="btn btn-sm btn-default btn-close">Close</button>

				</div>

			</div>

		</form>

	</div>

</div>