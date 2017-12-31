<h1>Categories</h1>

<table id="grid-basic" class="table table-sm table-hover table-striped xgrid-table" data-toggle="bootgrid">
<thead>
	<tr>
		<th data-column-id="id" data-header-css-class="table-row-id align-middle text-center" data-css-class="table-row-id align-middle text-center" data-order="asc">ID #</th>
		<th data-column-id="name" data-header-css-class="table-row-name align-middle" data-css-class="table-row-name align-middle" data-formatter="name">Name</th>
		<th data-column-id="ordering" data-header-css-class="table-row-ordering align-middle text-center" data-css-class="table-row-ordering align-middle text-center" data-visible="1000">Ordering</th>
		<th data-column-id="published" data-header-css-class="table-row-published align-middle text-center" data-css-class="table-row-published align-middle text-center" data-visible="1000">Published</th>
		<th data-column-id="commands" data-header-css-class="table-row-commands" data-css-class="table-row-commands text-right" data-visible-in-selection="false" data-sortable="false">&nbsp;</th>
	</tr>
</thead>
<tbody></tbody>
<tfoot>
<tr>
	<td>
		<button class="btn btn-sm btn-success btn-create">
			<i class="fa fa-plus-square"></i>
			New category
		</button>
	</td>
</tr>
</tfoot>
</table>

<div id="createModal" class="modal fade" tabindex="-1" role="dialog">

	<div class="modal-dialog" role="document">

		<form id="form-create" action="backend/category/create" method="post" data-toggle="validator">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">Category Creation</h5>

				</div>

				<div class="modal-body">

					<div class="form-group row">

						<label for="create_name" class="col-sm-4 col-form-label col-form-label-sm">Name</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm" id="create_name" name="category_name" placeholder="e.g. Blog" required />
						</div>

					</div>

					<div class="form-group row">

						<label for="create_parent_id" class="col-sm-4 col-form-label col-form-label-sm">Parent</label>
						<div class="col-sm-8">
							<select class="form-control form-control-sm" id="create_parent_id" name="category_parent_id" placeholder="">
								<option value="0">ROOT</option>
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


<div id="modifyModal" class="modal fade" tabindex="-1" role="dialog">

	<div class="modal-dialog" role="document">

		<form id="form-modify" action="backend/category/modify" method="post" data-toggle="validator">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">Category Modification</h5>

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
							<input type="text" class="form-control form-control-sm" id="modify_name" name="category_name" placeholder="e.g. Blog" required />
						</div>

					</div>

					<div class="form-group row">

						<label for="modify_parent_id" class="col-sm-4 col-form-label col-form-label-sm">Parent</label>
						<div class="col-sm-8">
							<select class="form-control form-control-sm" id="modify_parent_id" name="category_parent_id" placeholder="">
								<option value="0">ROOT</option>
							</select>
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