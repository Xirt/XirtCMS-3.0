<h1>

	Routes
	<div class="btn-group create" />
		<button class="btn btn-sm btn-success btn-create">Add New</button>
	</div>

</h1>

<table id="grid-basic" class="table table-sm table-hover table-striped" data-toggle="bootgrid">
<thead>
	<tr>
		<th data-column-id="id" data-header-css-class="table-row-id align-middle text-center" data-css-class="table-row-id align-middle text-center" data-converter="identifier" data-order="asc">ID #</th>
		<th data-column-id="public_url" data-header-css-class="table-row-public_url align-middle" data-css-class="table-row-public_url align-middle" data-visible-in-selection="true" data-order="asc">SEF URI</th>
		<th data-column-id="target_url" data-header-css-class="table-row-target_url align-middle" data-css-class="table-row-target_url align-middle" data-visible-in-selection="true">Internal URI</th>
		<th data-column-id="menu_item_id" data-header-css-class="table-row-menu_item_id align-middle text-center" data-css-class="table-row-menu_item_id align-middle text-center" data-formatter="menu_id" data-visible-in-selection="true">Menu</th>
		<th data-column-id="commands" data-header-css-class="table-row-commands" data-css-class="table-row-commands text-right" data-formatter="commands" data-visible-in-selection="false">&nbsp;</th>
	</tr>
</thead>
</table>

<div id="createModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">

	<div class="modal-dialog" role="document">

		<form id="form-create" action="backend/route/create" method="post" data-toggle="validator">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">Route Creation</h5>

				</div>

				<div class="modal-body">

					<div class="form-group row">

						<label for="inp-url" class="col-sm-4 col-form-label col-form-label-sm">Public URI</label>
						<div class="col-sm-8">
						
							<input type="text" class="form-control form-control-sm" id="inp-url" name="route_public_url" placeholder="" required />
							
							<div class="box-notify bg-danger text-white" id="box-exists">

								<i class="fa fa-warning text-white"></i>
								<i>This chosen URI is already in use. Please chose a different URI or modify the existing one.</i>

							</div>
							
						</div>

					</div>

				</div>

				<div class="modal-footer">

					<button type="submit" class="btn btn-sm btn-success"><span class="fa fa-refresh fa-spin fa-1x fa-fw"></span>Create route</button>
					<button type="button" class="btn btn-sm btn-primary btn-close">Close</button>

				</div>

			</div>

		</form>

	</div>

</div>

<div id="modifyModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">

	<div class="modal-dialog configuration" role="document">

		<form id="form-modify" action="backend/route/modify" method="post" data-toggle="validator">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">Route Configuration</h5>

				</div>

				<div class="modal-body">

					<div class="form-group row">

						<label class="col-sm-4 col-form-label col-form-label-sm">ID #</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm input-info" name="id" required disabled="disabled" />
						</div>

					</div>

					<div class="form-group row">

						<label for="modify_master" class="col-sm-4 col-form-label col-form-label-sm">Master</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm" id="modify_master" name="route_master" placeholder="" />
						</div>

					</div>

					<div class="form-group row">

						<label for="inp-public_url" class="col-sm-4 col-form-label col-form-label-sm">Public URI</label>
						<div class="col-sm-8">

							<input type="text" class="form-control form-control-sm" name="route_public_url" id="inp-public_url" placeholder="" />

							<div class="box-notify bg-warning text-white" id="box-relations">

								<i class="fa fa-warning text-white"></i>
								<i>This is an existing URI: any changes to its related target URI will have effect globally.</i>

							</div>

						</div>

					</div>

					<div class="form-group row">

						<label for="txt-int-anchor" class="col-sm-4 col-form-label col-form-label-sm">Anchor Name</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm" id="txt-int-anchor" name="route_extension" placeholder="" />
						</div>

					</div>

					<div class="form-group row">

						<label for="inp-target_url" class="col-sm-4 col-form-label col-form-label-sm">Target URI</label>
						<div class="col-sm-8">

							<input type="text" class="form-control form-control-sm" name="route_target_url" id="inp-target_url" placeholder="" disabled="disabled" />
							<button type="button" class="btn btn-sm btn-sm btn-primary" id="btn-editor">Toggle editor</button>

						</div>

					</div>

					<div id="box-link">


						<div class="form-group row">

							<label for="sel-module-type" class="col-sm-4 col-form-label col-form-label-sm">Module Type</label>
							<div class="col-sm-8">

								<select class="form-control form-control-sm custom-select" name="route_module_type" id="sel-module-type" required>

									<?php foreach ($moduleTypes as $moduleType => $moduleName): ?>
									<option value="<?php echo $moduleType; ?>"><?php echo $moduleName; ?></option>
									<?php endforeach; ?>

								</select>

							</div>

						</div>

						<div class="form-group row">

							<label for="sel-module-config" class="col-sm-4 col-form-label col-form-label-sm">Module Configuration</label>
							<div class="col-sm-8">

								<select class="form-control form-control-sm custom-select" name="route_module" id="sel-module-config" required >
								</select>

							</div>

						</div>

						<div id="box-params"></div>

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