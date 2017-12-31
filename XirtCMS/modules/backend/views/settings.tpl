<h1>System Configuration</h1>

<table id="grid-basic" class="table table-sm table-hover table-striped xgrid-table" data-toggle="bootgrid">
<thead>
	<tr>
		<th data-column-id="name" data-header-css-class="table-row-name align-middle" data-css-class="table-row-name align-middle" data-visible-in-selection="false" data-order="asc">Name</th>
		<th data-column-id="value" data-header-css-class="table-row-value align-middle" data-css-class="table-row-value align-middle" data-visible-in-selection="true">Value</th>
		<th data-column-id="commands" data-header-css-class="table-row-commands" data-css-class="table-row-commands text-right" data-visible-in-selection="false">&nbsp;</th>
	</tr>
</thead>
<tbody></tbody>
</table>

<div id="modifyModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">

	<div class="modal-dialog" role="document">

		<form id="form-modify" action="backend/setting/modify" method="post" data-toggle="validator">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">System setting</h5>

				</div>

				<div class="modal-body">

					<div class="form-group row">

						<label class="col-sm-4 col-form-label col-form-label-sm">Name</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm input-info" name="setting_name" disabled="disabled" />
						</div>

					</div>

					<div class="form-group row">

						<label for="setting_value" class="col-sm-4 col-form-label col-form-label-sm">Value</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm" id="modify_value" name="setting_value" placeholder="" />
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