<div class="shelfTabs tabbable">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab3" data-toggle="tab">Form</a></li>
		<li><a href="#tab4" data-toggle="tab">Upload CSV</a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active fade in" id="tab3">
			<form id="shelfForm" class="inputForm" action="php/shelfs.php" method="post">
				<div class="shelfs">
					<div class="shelf shelf-left">
						<div class="alertBox"></div>
						<div class="shelfForm">
							<div class="control-group">
								<div class="controls">
									<input type="text" class="upc shelfUPC" id="upc1" name="upc" maxlength="11" 
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
							<input type="text" class="size" id="size1" name="size" placeholder="Size" />
								</div>
							</div>
							<div class="control-group">
								<div class="controls">
									<input type="text" class="price" id="price1" name="price" />
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="buttons">
					<button class="btn btn-primary addShelf">Add Shelf Talker</button>
					<button type="submit" disabled class="btn btn-primary generateShelf">Generate Shelf Talkers</button>
				</div>
			</form>
		</div>
		<div class="tab-pane fade" id="tab4">
			<form id="uploadForm" action="php/uploadShelfs.php" method="post" enctype="multipart/form-data">
				<h3>Select the csv file you wish to upload.</h3>
				<input type="file" name="shelfFile" id="shelfFile">
				<button type="submit" class="btn btn-primary">Upload</button>
			</form>
		</div>
	</div>
</div>