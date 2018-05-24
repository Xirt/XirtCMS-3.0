<h1>Widget Configurations</h1>

<table id="grid-basic" class="table table-sm table-hover table-striped xgrid-table" data-toggle="bootgrid">
<thead>
	<tr>
		<th data-column-id="id" data-header-css-class="table-row-item_id align-middle text-center" data-css-class="table-row-item_id align-middle text-center">ID #</th>
		<th data-column-id="name" data-header-css-class="table-row-name align-middle" data-css-class="table-row-name align-middle" data-visible-in-selection="false">Name</th>
		<th data-column-id="type" data-header-css-class="table-row-type align-middle text-center" data-css-class="table-row-type align-middle text-center">Type</th>
		<th data-column-id="position" data-header-css-class="table-row-position align-middle text-center" data-css-class="table-row-position align-middle text-center">Position</th>
		<th data-column-id="published" data-header-css-class="table-row-published align-middle text-center" data-css-class="table-row-published align-middle text-center">Published</th>
		<th data-column-id="commands" data-header-css-class="table-row-commands" data-css-class="table-row-commands text-right" data-visible-in-selection="false">&nbsp;</th>
	</tr>
</thead>
<tbody></tbody>
<tfoot>
<tr>
	<td>

		<button class="btn btn-sm btn-success btn-create">
			<i class="fa fa-plus-square"></i>
			New configuration
		</button>

	</td>
</tr>
</tfoot>
</table>

<div id="optionsModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">

	<div class="modal-dialog" role="document">

		<div class="modal-content">

			<div class="modal-header">

				<h5 class="modal-title">What do you want to do?</h5>

			</div>

			<div class="modal-body row">

				<div class="col col-sm-3">

					<button class="btn btn-info btn-edit-main">
						<i class="far fa-edit fa-5x" aria-hidden="true"></i>
						<p>Modify appearance</p>
					</button>

				</div>

				<div class="col col-sm-3">

					<button class="btn btn-info btn-edit-attributes">
						<i class="fas fa-cogs fa-5x" aria-hidden="true"></i>
						<p>Modify configuration</p>
					</button>

				</div>

				<div class="col col-sm-3">

					<button class="btn btn-info btn-edit-priorities" disabled>
						<i class="fas fa-cogs fa-5x" aria-hidden="true"></i>
						<p>Modify priorities</p>
					</button>

				</div>

				<div class="col-sm-3">

					<button class="btn btn-info btn-edit-status">
						<i class="far fa-calendar-alt fa-5x" aria-hidden="true"></i>
						<p>Schedule publishing</p>
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

		<form id="form-create" action="backend/widgets/widget/create" method="post" data-toggle="validator">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">Widget Creation</h5>

				</div>

				<div class="modal-body">

					<div class="form-group row">

						<label for="create_name" class="col-sm-4 col-form-label col-form-label-sm">Name</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm" id="create_name" name="widget_name" placeholder="" required />
						</div>

					</div>

					<div class="form-group row">

						<label for="create_type" class="col-sm-4 col-form-label col-form-label-sm">Widget Type</label>
						<div class="col-sm-8">
							<select class="form-control form-control-sm custom-select" id="create_type" name="widget_type" required>

								<?php foreach ($types as $type => $name): ?>
								<option value="<?php echo $type; ?>"><?php echo $name; ?></option>
								<?php endforeach; ?>

							</select>
						</div>

					</div>

					<div class="form-group row">

						<label for="create_position" class="col-sm-4 col-form-label col-form-label-sm">Template Position</label>
						<div class="col-sm-8">
							<select class="form-control form-control-sm custom-select" id="create_position" name="widget_position" required>

								<?php foreach ($positions as $position): ?>
								<option value="<?php echo $position; ?>"><?php echo $position; ?></option>
								<?php endforeach; ?>

							</select>
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

		<form id="form-modify" action="backend/widgets/widget/modify" method="post" data-toggle="validator">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">Widget Modification</h5>

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
							<input type="text" class="form-control form-control-sm" id="modify_name" name="widget_name" placeholder="" required />
						</div>

					</div>

					<div class="form-group row">

						<label for="modify_position" class="col-sm-4 col-form-label col-form-label-sm">Template Position</label>
						<div class="col-sm-8">
							<select class="form-control form-control-sm custom-select" id="modify_position" name="widget_position" required>

								<?php foreach ($positions as $position): ?>
								<option value="<?php echo $position; ?>"><?php echo $position; ?></option>
								<?php endforeach; ?>

							</select>
						</div>

					</div>

					<div class="form-group row">

						<label for="modify_page_module" class="col-sm-4 col-form-label col-form-label-sm">Module requirement</label>
						<div class="col-sm-8">
							<select class="form-control form-control-sm custom-select" id="modify_page_module" name="widget_page_module" required>

								<option value="0"></option>
								<?php foreach ($configurations as $configuration): ?>
								<option value="<?php echo $configuration->get("id"); ?>"><?php echo $configuration->get("name"); ?></option>
								<?php endforeach; ?>

							</select>
						</div>

					</div>

					<div class="form-group row">

						<label for="page-selector" class="col-sm-4 col-form-label col-form-label-sm">Page Visibility</label>
						<div class="col-sm-8">

							<select class="form-control form-control-sm" id="page-selector" name="widget_pages[]" multiple="multiple" size="10">

								<?php foreach ($menus as $menu): ?>

									<optgroup label="<?php echo $menu->get("name"); ?>">

									<?php foreach ($menuEntries[$menu->get("id")] as $entry): ?>
									<option value="<?php echo $entry->value; ?>"><?php echo $entry->label; ?></option>
									<?php endforeach; ?>

									</optgroup>

								<?php endforeach; ?>

							</select>

							<label for="opt-page-default" class="form-check-label">
								<input type="checkbox" class="form-check-input" id="opt-page-default" name="widget_page_default" value="1" />
								Show only if no other widget present
							</label>

							<label for="opt-toggle-page" class="form-check-label">
								<input type="checkbox" class="form-check-input" id="opt-toggle-page" name="widget_page_all" value="1" />
								Show widget on all pages
							</label>

						</div>

					</div>

					<div class="form-group row">

						<label class="col-sm-4 col-form-label col-form-label-sm" for="widget_cache_slider">Caching</label>
						<div class="col-sm-5">
							<input type="range" min="0" max="13" value="0" class="form-control form-control-sm" name="widget_cache_slider" id="widget_cache_slider" placeholder="" style="width: 100%;" data-display="cache_display" data-holder="widget_cache" required value="" />
							<input type="hidden" value="0" name="widget_cache" id="widget_cache" />
                        </div>
						<div class="col-sm-3">
							<input type="text" class="form-control form-control-sm input-info" id="cache_display" style="width: 100%;" disabled />
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

<div id="configModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">

	<div class="modal-dialog" role="document">

		<form id="form-config" action="backend/widgets/widget/modify_settings" method="post" data-toggle="validator">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">Widget Configuration</h5>

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

<div id="priorityModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">

	<div class="modal-dialog" role="document">

		<form id="form-priorities" action="backend/widgets/widget/modify_priority" method="post" data-toggle="validator">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">Widget Priority</h5>

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

<div id="publishModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog" role="document">

		<form id="form-publish" action="backend/articles/article/modify_publish" method="post" data-toggle="validator">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">Publishing</h5>

				</div>

				<div class="modal-body">

					<div class="form-group row">

						<label class="col-sm-4 col-form-label col-form-label-sm">ID #</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm input-info" name="widget_id" required disabled="disabled" />
						</div>

					</div>

					<div class="form-group row">

						<label class="col-sm-4 col-form-label col-form-label-sm">Name</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm input-info" name="widget_name" disabled="disabled" />
						</div>

					</div>

					<div class="form-group row">

						<label class="col-sm-4 col-form-label col-form-label-sm">Published</label>
						<div class="col-sm-8 text-left">
							<input type="checkbox" name="widget_published" id="widget_published" data-on="Yes" data-off="No" data-onstyle="info" data-toggle="toggle" data-size="small" />
						</div>

					</div>

					<div class="form-group row publish-dates">

						<label class="col-sm-4 col-form-label col-form-label-sm">Publish date</label>
						<div class="col-sm-8">

							<div class="input-group date">
								<input type="text" class="form-control form-control-sm datepicker" name="widget_dt_publish" id="widget_dt_publish" maxlength="10" readonly />
								<div class="input-group-append">
									<div class="input-group-text"><i class="fa fa-calendar"></i></div>
        						</div>
							</div>

						</div>

					</div>

					<div class="form-group row publish-dates">

						<label class="col-sm-4 col-form-label col-form-label-sm">Unpublish date</label>
						<div class="col-sm-8">

							<div class="input-group date">
								<input type="text" class="form-control form-control-sm datepicker" name="widget_dt_unpublish" id="widget_dt_unpublish" maxlength="10" readonly />
								<div class="input-group-append">
									<div class="input-group-text"><i class="fa fa-calendar"></i></div>
        						</div>
							</div>

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