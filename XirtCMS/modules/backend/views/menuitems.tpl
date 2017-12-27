<h1><?php echo $menu_name; ?></h1>

<table id="grid-basic" class="table table-sm table-hover table-striped xgrid-table" data-toggle="bootgrid">
<thead>
	<tr>
		<th data-column-id="item_id" data-header-css-class="table-row-item_id align-middle text-center" data-css-class="table-row-item_id align-middle text-center">ID #</th>
		<th data-column-id="name" data-header-css-class="table-row-name align-middle" data-css-class="table-row-name align-middle" data-formatter="name" data-visible-in-selection="false">Name</th>
		<th data-column-id="ordering" data-header-css-class="table-row-ordering align-middle text-center" data-css-class="table-row-ordering align-middle text-center" data-visible="1000">Ordering</th>
		<th data-column-id="sitemap" data-header-css-class="table-row-sitemap align-middle text-center" data-css-class="table-row-sitemap align-middle text-center" data-visible="1000">Sitemap</th>
		<th data-column-id="published" data-header-css-class="table-row-published align-middle text-center" data-css-class="table-row-published align-middle text-center" data-visible="1000">Published</th>
		<th data-column-id="commands" data-header-css-class="table-row-commands" data-css-class="table-row-commands text-right" data-visible-in-selection="false">&nbsp;</th>
	</tr>
</thead>
<tbody></tbody>
<tfoot>
<tr>
	<td>
		<button class="btn btn-sm btn-success btn-create">
			<i class="fa fa-plus-square"></i>
			New menu item
		</button>
	</td>
</tr>
</tfoot>
</table>

<div id="createModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">

	<div class="modal-dialog" role="document">

		<form id="form-create" action="backend/menuitem/create" method="post" data-toggle="validator">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">Menuitem Creation</h5>

				</div>

				<div class="modal-body">

					<div class="form-group row">

						<label for="create_name" class="col-sm-4 col-form-label col-form-label-sm">Name</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm" id="create_name" name="menuitem_name" placeholder="" required />
						</div>

					</div>

					<div class="form-group row">

						<label for="create_parent_id" class="col-sm-4 col-form-label col-form-label-sm">Parent</label>
						<div class="col-sm-8">
							<select class="form-control form-control-sm" id="create_parent_id" name="menuitem_parent_id">
								<option value="0">ROOT</option>
							</select>
						</div>

					</div>

				</div>

				<div class="modal-footer">

					<input type="hidden" name="menu_id" value="" />

					<button type="submit" class="btn btn-sm btn-success"><span class="fa fa-refresh fa-spin fa-1x fa-fw"></span>Create item</button>
					<button type="button" class="btn btn-sm btn-primary btn-close">Close</button>

				</div>

			</div>

		</form>

	</div>

</div>

<div id="modifyModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">

	<div class="modal-dialog" role="document">

		<form id="form-modify" action="backend/menuitem/modify" method="post" data-toggle="validator">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">Menuitem Modification</h5>

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
							<input type="text" class="form-control form-control-sm" id="modify_name" name="menuitem_name" placeholder="" required />
						</div>

					</div>

					<div class="form-group row">

						<label for="modify_parent_id" class="col-sm-4 col-form-label col-form-label-sm">Parent</label>
						<div class="col-sm-8">
							<select class="form-control form-control-sm" id="modify_parent_id" name="menuitem_parent_id">
								<option value="0">ROOT</option>
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

<div id="configModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">

	<div class="modal-dialog configuration" role="document">

		<form id="form-config" action="backend/menuitem/modify_settings" method="post" data-toggle="validator">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">Menuitem Configuration</h5>

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

					<div class="form-group row tabs">

						<label class="col-sm-4 col-form-label col-form-label-sm">Link Type</label>
						<div class="col-sm-8">
							<ul class="nav nav-tabs" role="tablist">
								<li class="nav-item"><a href="#box-internal" id="type-internal" class="nav-link" data-toggle="tab" role="tab">Internal</a></li>
								<li class="nav-item"><a href="#box-external" id="type-external" class="nav-link"data-toggle="tab" role="tab">External</a></li>
								<li class="nav-item"><a href="#box-anchor"id="type-anchor" class="nav-link"data-toggle="tab" role="tab">Anchor</a></li>
							</ul>
						</div>

					</div>

					<div class="tab-content">

						<input type="hidden" name="menuitem_type" id="inp-type" value="" />

						<div id="box-internal" class="tab-pane fade in">

							<div class="form-group row">

								<label for="inp-public_url" class="col-sm-4 col-form-label col-form-label-sm">URI</label>
								<div class="col-sm-8">

									<input type="text" class="form-control form-control-sm" name="menuitem_public_url" id="inp-public_url" placeholder="" />

									<div class="box-notify bg-info text-white" id="box-relations">

										<i class="fa fa-info-circle text-white"></i>
										<i>This is an existing URI: any changes to its related target URI will have effect globally.</i>

									</div>

								</div>

							</div>

							<div class="form-group row">

								<label for="txt-int-anchor" class="col-sm-4 col-form-label col-form-label-sm">Anchor Name</label>
								<div class="col-sm-8">
									<input type="text" class="form-control form-control-sm" id="txt-int-anchor" name="menuitem_extension" placeholder="" />
								</div>

							</div>

							<div class="form-group row">

								<label for="inp-target_url" class="col-sm-4 col-form-label col-form-label-sm">Target URI</label>
								<div class="col-sm-8">

									<input type="text" class="form-control form-control-sm" name="menuitem_target_url" id="inp-target_url" placeholder="" disabled="disabled" />
									<button type="button" class="btn btn-sm btn-sm btn-primary" id="btn-editor">Toggle editor</button>

								</div>

							</div>

							<div id="box-link">


								<div class="form-group row">

									<label for="sel-module-type" class="col-sm-4 col-form-label col-form-label-sm">Module Type</label>
									<div class="col-sm-8">

										<select class="form-control form-control-sm custom-select" name="menuitem_module_type" id="sel-module-type" required>

											<?php foreach ($moduleTypes as $moduleType => $moduleName): ?>
											<option value="<?php echo $moduleType; ?>"><?php echo $moduleName; ?></option>
											<?php endforeach; ?>

										</select>

									</div>

								</div>

								<div class="form-group row">

									<label for="sel-module-config" class="col-sm-4 col-form-label col-form-label-sm">Module Configuration</label>
									<div class="col-sm-8">

										<select class="form-control form-control-sm custom-select" name="menuitem_module_config" id="sel-module-config" required >
										</select>

									</div>

								</div>

								<div id="box-params"></div>

							</div>

						</div>

						<div id="box-external" class="tab-pane fade in">

							<div class="form-group row">

								<label for="inp-ext_url" class="col-sm-4 col-form-label col-form-label-sm">URI</label>
								<div class="col-sm-8">
									<input type="text" class="form-control form-control-sm" name="menuitem_uri" id="inp-ext_url" placeholder="e.g. http://www.google.com" />
								</div>

							</div>

						</div>

						<div id="box-anchor" class="tab-pane fade in">

							<div class="form-group row">

								<label for="txt-anchor" class="col-sm-4 col-form-label col-form-label-sm">Anchor Name</label>
								<div class="col-sm-8">
									<input type="text" class="form-control form-control-sm" id="txt-anchor" name="menuitem_anchor" placeholder="" />
								</div>

							</div>

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