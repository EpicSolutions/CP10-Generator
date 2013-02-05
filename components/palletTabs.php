<div class="palletTabs tabbable">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab5" data-toggle="tab">Form</a></li>
		<li><a href="#tab6" data-toggle="tab">Upload CSV</a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active fade in" id="tab5">
			<form id="palletForm" class="inputForm" action="php/pallets.php" method="post">
				<div class="pallets">
					<div class="pallet pallet-left">
						<div class="alertBox"></div>
						<div class="palletForm">
							<div class="control-group">
								<div class="controls">
									<input type="text" class="upc palletUPC" id="upc1" name="upc" maxlength="11" 
										placeholder="UPC" data-provide="typeahead" />									
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
					<button class="btn btn-primary addPallet">Add Pallet Signs</button>
					<button type="submit" disabled class="btn btn-primary generatePallet">Generate Pallet Signs</button>
				</div>
			</form>
		</div>
		<div class="tab-pane fade" id="tab6">
			<form id="uploadForm" action="php/uploadPallets.php" method="post" enctype="multipart/form-data">
				<h3>Select the csv file you wish to upload.</h3>
				<input type="file" name="palletFile" id="palletFile">
				<button type="submit" class="btn btn-primary">Upload</button>
			</form>
		</div>
	</div>
</div>