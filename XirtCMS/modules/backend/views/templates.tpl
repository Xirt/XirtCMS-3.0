<h1>

	Templates
	<div class="btn-group create" />
		<button class="btn btn-sm btn-success btn-create">Add New</button>
	</div>

</h1>

<table id="grid-basic" class="table table-sm table-hover table-striped" data-toggle="bootgrid">
<thead>
	<tr>
		<th data-column-id="id" data-header-css-class="table-row-item_id align-middle text-center" data-css-class="table-row-item_id align-middle text-center" data-converter="identifier" data-order="asc">ID #</th>
		<th data-column-id="name" data-header-css-class="table-row-name align-middle" data-css-class="table-row-name align-middle" data-visible-in-selection="false">Name</th>
		<th data-column-id="folder" data-header-css-class="table-row-folder align-middle text-center" data-css-class="table-row-folder align-middle text-center" data-visible-in-selection="false">Folder</th>
		<th data-column-id="published" data-header-css-class="table-row-published align-middle text-center" data-css-class="table-row-published align-middle text-center" data-formatter="published">Active</th>
		<th data-column-id="commands" data-header-css-class="table-row-commands" data-css-class="table-row-commands text-center" data-formatter="commands" data-visible-in-selection="false">&nbsp;</th>
	</tr>
</thead>
</table>

<div id="createModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">

	<div class="modal-dialog" role="document">

		<form id="form-create" action="backend/template/create" method="post" data-toggle="validator">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">Template Creation</h5>

				</div>

				<div class="modal-body">

					<div class="form-group row">

						<label for="create_name" class="col-sm-4 col-form-label col-form-label-sm">Name</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm" id="create_name" name="template_name" placeholder="E.g. My Template" required />
						</div>

					</div>

					<div class="form-group row">

						<label for="create_folder" class="col-sm-4 col-form-label col-form-label-sm">Folder</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm" id="create_folder" name="template_folder" placeholder="E.g. custom" required />
						</div>

					</div>

				</div>

				<div class="modal-footer">

					<button type="submit" class="btn btn-sm btn-success"><span class="fa fa-refresh fa-spin fa-1x fa-fw"></span>Create item</button>
					<button type="button" class="btn btn-sm btn-primary btn-close">Close</button>

				</div>

			</div>

		</form>

	</div>

</div>

<div id="modifyModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">

	<div class="modal-dialog" role="document">

		<form id="form-modify" action="backend/template/modify" method="post" data-toggle="validator">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">Template Modification</h5>

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
							<input type="text" class="form-control form-control-sm" id="modify_name" name="template_name" placeholder="E.g. My Template" required />
						</div>

					</div>

					<div class="form-group row">

						<label for="modify_folder" class="col-sm-4 col-form-label col-form-label-sm">Folder</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm" id="modify_folder" name="template_folder" placeholder="E.g. custom" required />
						</div>

					</div>

					<div class="form-group row">

						<label for="inp-position" class="col-sm-4 col-form-label col-form-label-sm">Positions</label>
						<div class="col-sm-8">

							<div class="row">

								<div class="col-sm-8">
									<input type="text" class="form-control form-control-sm" id="inp-position" placeholder="" />
								</div>
								<div class="col-sm-4">
									<button type="button" class="btn btn-sm btn-success" id="btn-add-position">Add &raquo;</button>
								</div>

							</div>

							<div class="row">

								<div class="col-sm-12">

									<ul class="list-group" id="gui-positions"></ul>

								</div>

							</div>

						</div>

					</div>

				</div>

				<div class="modal-footer">

					<input type="hidden" name="positions_list" id="inp-positions" />

					<button type="submit" class="btn btn-sm btn-success"><span class="fa fa-refresh fa-spin fa-1x fa-fw"></span>Save changes</button>
					<button type="button" class="btn btn-sm btn-primary btn-close">Close</button>

				</div>

			</div>

		</form>

	</div>

</div>