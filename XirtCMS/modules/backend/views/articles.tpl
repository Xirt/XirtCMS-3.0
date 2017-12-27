<h1>Articles</h1>

<table id="grid-basic" class="table table-sm table-hover table-striped xgrid-table" data-toggle="bootgrid">
<thead>
<tr>
	<th data-column-id="id" data-header-css-class="table-row-id align-middle text-center" data-css-class="table-row-id align-middle text-center" data-order="asc">ID #</th>
	<th data-column-id="title" data-header-css-class="table-row-title align-middle" data-css-class="table-row-title align-middle">Title</th>
	<th data-column-id="dt_created" data-header-css-class="table-row-dt_created align-middle text-center" data-css-class="table-row-dt_created align-middle text-center" data-visible="false">Created</th>
	<th data-column-id="author" data-header-css-class="table-row-author align-middle text-center" data-css-class="table-row-author align-middle text-center" data-visible="false">Author</th>
	<th data-column-id="published" data-header-css-class="table-row-published text-center" data-css-class="table-row-published align-middle text-center" data-sortable="false" data-visible="1000">Published</th>
	<th data-column-id="commands" data-header-css-class="table-row-commands" data-css-class="table-row-commands text-right" data-visible-in-selection="false" data-sortable="false">&nbsp;</th>
</tr>
</thead>
<tbody></tbody>
<tfoot>
<tr>
	<td>
		<button class="btn btn-sm btn-success btn-create">
			<i class="fa fa-plus-square"></i>
			New article
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

				<div class="col-sm-3">

					<button class="btn btn-info btn-edit-content">
						<i class="fa fa-pencil fa-5x" aria-hidden="true"></i>
						<p>Modify content</p>
					</button>

				</div>

				<div class="col-sm-3">

					<button class="btn btn-info btn-edit-properties">
						<i class="fa fa-gears fa-5x" aria-hidden="true"></i>
						<p>Modify properties</p>
					</button>

				</div>

				<div class="col-sm-3">

					<button class="btn btn-info btn-edit-status">
						<i class="fa fa-calendar  fa-5x" aria-hidden="true"></i>
						<p>Schedule publishing</p>
					</button>

				</div>

				<div class="col-sm-3">

					<button class="btn btn-info btn-edit-categories">
						<i class="fa fa-unlock-alt fa-5x" aria-hidden="true"></i>
						<p>Relate categories</p>
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

		<form id="form-create" action="backend/article/create" method="post" data-toggle="validator">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">Article Creation</h5>

				</div>

				<div class="modal-body">

					<div class="form-group row">

						<label for="create_title" class="col-sm-4 col-form-label col-form-label-sm">Title</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm" id="create_title" name="article_title" placeholder="" required />
						</div>

					</div>

				</div>

				<div class="modal-footer">

					<button type="submit" class="btn btn-sm btn-success"><span class="fa fa-refresh fa-spin fa-1x fa-fw"></span>Create article</button>
					<button type="button" class="btn btn-sm btn-primary btn-close">Close</button>

				</div>

			</div>

		</form>

	</div>

</div>


<div id="modifyModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog article" role="document">

		<form id="form-modify" action="backend/article/modify" method="post" data-toggle="validator">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">Article Modification</h5>

				</div>

				<div class="modal-body">

					<div class="box-article-header">

						<input id="modify_title" name="article_title" class="form-control form-control-sm" />

					</div>

					<textarea id="articleArea" name="article_content" ></textarea>

				</div>

				<div class="modal-footer">

					<input type="hidden" class="form-control form-control-sm" name="article_id" />

					<button type="submit" class="btn btn-sm btn-success"><span class="fa fa-refresh fa-spin fa-1x fa-fw"></span>Save changes</button>
					<button type="button" class="btn btn-sm btn-primary btn-close">Close</button>

				</div>

			</div>

		</form>

	</div>

</div>

<div id="categoriesModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog" role="document">

		<form id="form-categories" action="backend/article/modify_categories" method="post" data-toggle="validator">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">Article Categories</h5>

				</div>

				<div class="modal-body">

					<div class="form-group row">

						<label class="col-sm-4 col-form-label col-form-label-sm">ID #</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm input-info" name="article_id" required disabled="disabled" />
						</div>

					</div>

					<div class="form-group row">

						<label class="col-sm-4 col-form-label col-form-label-sm">Title</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm input-info" name="article_title" disabled="disabled" />
						</div>

					</div>

					<div class="form-group row">

							<label for="categorySelector" class="col-sm-4 col-form-label col-form-label-sm">Categories</label>
							<div class="col-sm-8">
								<select class="form-control form-control-sm" id="categorySelector" name="article_categories[]" multiple="multiple" size="10">

									<?php foreach ($categories as $category): ?>
									<option value="<?php echo $category->get("id"); ?>"><?php echo $category->get("name"); ?></option>
									<?php endforeach; ?>

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

<div id="configModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog" role="document">

		<form id="form-config" action="backend/article/modify_config" method="post" data-toggle="validator">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">Article Configuration</h5>

				</div>

				<div class="modal-body">

					<div class="form-group row">

						<label class="col-sm-4 col-form-label col-form-label-sm">ID #</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm input-info" name="article_id" required disabled="disabled" />
						</div>

					</div>

					<div class="form-group row">

						<label class="col-sm-4 col-form-label col-form-label-sm">Title</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm input-info" name="article_title" disabled="disabled" />
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

<div id="publishModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog" role="document">

		<form id="form-publish" action="backend/article/modify_publish" method="post" data-toggle="validator">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">Article Publishment</h5>

				</div>

				<div class="modal-body">

					<div class="form-group row">

						<label class="col-sm-4 col-form-label col-form-label-sm">ID #</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm input-info" name="article_id" required disabled="disabled" />
						</div>

					</div>

					<div class="form-group row">

						<label class="col-sm-4 col-form-label col-form-label-sm">Title</label>
						<div class="col-sm-8">
							<input type="text" class="form-control form-control-sm input-info" name="article_title" disabled="disabled" />
						</div>

					</div>

					<div class="form-group row">

						<label class="col-sm-4 col-form-label col-form-label-sm">Published</label>
						<div class="col-sm-8 text-left">
							<input type="checkbox" name="article_published" id="article_published" data-on="Yes" data-off="No" data-onstyle="info" data-toggle="toggle" data-size="small" />
						</div>

					</div>

					<div class="form-group row publish-dates">

						<label class="col-sm-4 col-form-label col-form-label-sm">Publish date</label>
						<div class="col-sm-8">

							<div class="input-group date">
								<input type="text" class="form-control form-control-sm datepicker" name="article_dt_publish" id="article_dt_publish" maxlength="10" readonly />
								<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
							</div>

						</div>

					</div>

					<div class="form-group row publish-dates">

						<label class="col-sm-4 col-form-label col-form-label-sm">Unpublish date</label>
						<div class="col-sm-8">

							<div class="input-group date">
								<input type="text" class="form-control form-control-sm datepicker" name="article_dt_unpublish" id="article_dt_unpublish" maxlength="10" readonly />
								<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
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