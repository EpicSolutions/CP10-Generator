<div class="produceTabs tabbable">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab7" data-toggle="tab">Form</a></li>
		<li><a href="#tab8" data-toggle="tab">Upload CSV</a></li>
	</ul>
	<div class="tab-content produceContent">
		<div class="tab-pane active fade in" id="tab7">
			<form id="produceForm" class="inputForm" action="php/produces.php" method="post">
				<div class="produces">
					<div class="produce produce-left">
						<div class="alertBox"></div>
						<div class="produceForm">
							<div class="control-group">
								<div class="controls">
									<input type="text" class="upc produceUPC" id="upc1" name="upc" maxlength="11" 
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
									<input type="text" class="price" id="price1" name="price" placeholder="Price" />
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="buttons">
					<button class="btn btn-primary addProduce">Add Produce Labels</button>
					<button type="submit" disabled class="btn btn-primary generateProduce">Generate Produce Labels</button>
				</div>
			</form>
		</div>
		<div class="tab-pane fade" id="tab8">
			<form id="uploadForm" action="php/uploadProduce.php" method="post" enctype="multipart/form-data">
				<h3>Select the csv file you wish to upload.</h3>
				<input type="file" name="produceFile" id="produceFile">
				<button type="submit" class="btn btn-primary">Upload</button>
			</form>
		</div>
	</div>
</div>