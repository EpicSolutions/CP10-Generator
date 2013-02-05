<div class="labelTabs tabbable">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab1" data-toggle="tab">Form</a></li>
		<li><a href="#tab2" data-toggle="tab">Upload CSV</a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active fade in" id="tab1">
			<form id="labelForm" class="inputForm" action="php/labels.php" method="post">
				<div class="labels">
					<div class="label label-left">
						<div class="alertBox"></div>
						<div class="labelForm">
							<div class="control-group">
								<div class="controls">
									<input type="text" class="upc labelUPC" id="upc1" name="upc" maxlength="11" 
										placeholder="UPC" data-provide="typeahead" data-items="4" />									
								</div>
							</div>
							<div class="control-group">
								<div class="controls">
							<input type="text" class="man" id="man1" name="man" maxlength="28" placeholder="Manufacturer" />
								</div>
							</div>
							<div class="control-group">
								<div class="controls">
							<input type="text" class="desc" id="desc1" name="desc" maxlength="28" placeholder="Description" />
								</div>
							</div>
							<div class="control-group">
								<div class="controls">						
							<input type="text" class="units" id="units1" name="units" placeholder="Units" />
								</div>
							</div>
							<div class="control-group">
								<div class="controls">
							<input type="text" class="size" id="size1" name="size" placeholder="Size" />
								</div>
							</div>
							<div class="control-group">
								<div class="controls">
									<input type="text" class="price" id="price1" name="price" placeholder="Price" />
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="buttons">
					<button class="btn btn-primary addLabel">Add label</button>
					<button type="submit" disabled class="btn btn-primary generateLabel">Generate Labels</button>
				</div>
			</form>
		</div>
		<div class="tab-pane fade" id="tab2">
			<form id="uploadForm" action="php/uploadLabels.php" method="post" enctype="multipart/form-data">
				<h3>Select the csv file you wish to upload.</h3>
				<input type="file" name="file" id="file">
				<button type="submit" class="btn btn-primary">Upload</button>
			</form>
		</div>
	</div>
</div>