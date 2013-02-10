$(function(){
	// Placeholder for legacy browsers
	$('input[placeholder]').placeholder();

	
	/**********************************************************************************************
	 * UPC typeahead
	 *********************************************************************************************/ 
	var typeahead_opt = {
	    source: function (query, process) {
	        return $.post('php/labelTypeahead.php', { query: query }, function (data) {
	            return process(data);
	       });
	    },
	    items: 5
	};
	
	$('.produceUPC').typeahead(typeahead_opt);
	
	/**********************************************************************************************
	 * Validation Initialization
	 *********************************************************************************************/
	// Array of validaitons
	var produceFinalCheck = [];
	
	// Validation class
	function produceValidation() {
		this.upc   = false;
		this.man   = false;
		this.desc  = false;
		this.size  = false;
		this.price = false;
		this.validCheck = function() {
			for(var i = 0; i < produceFinalCheck.length; i++) {
				var ready = true;
				if(!(produceFinalCheck[i].upc && produceFinalCheck[i].man && produceFinalCheck[i].desc 
				  && produceFinalCheck[i].size && produceFinalCheck[i].price)) {
					$('.generateProduce').addClass('disabled');
					$('.generateProduce').prop('disabled', true);
					ready = false;
				}
				if(!ready) {
					return ready;
				}
			}
			
			$('.generateProduce').removeClass('disabled');
			$('.generateProduce').prop('disabled', false);
			return ready;
		};
	}
	
	// Add initial validation for initial label	
	produceFinalCheck.push(new produceValidation());
		
	// UPC (Also includes form population)
	$('.upc').on('change', function(e) {
		var self = $(this);
		var value = self.val();
		produceFinalCheck[0].upc = produceValidateUPC(self, value, 0);
		produceFinalCheck[0].validCheck();
	});
	
	// Manufacturer
	$('.man').on('change', function(e) {
		var self = $(this);
		var value = self.val();
		produceFinalCheck[0].man = produceValidateMan(self, value);
		produceFinalCheck[0].validCheck();
	});
	
	// Description
	$('.desc').on('change', function(e) {
		var self = $(this);
		var value = self.val();
		produceFinalCheck[0].desc = produceValidateDesc(self, value);
		produceFinalCheck[0].validCheck();
	});
	
	// Size
	$('.size').on('change', function(e) {
		var self = $(this);
		var value = self.val();
		produceFinalCheck[0].size = produceValidateSize(self, value);
		produceFinalCheck[0].validCheck();
	});
	
	// Price
	$('.price').on('change', function(e) {
		var self = $(this);
		var value = self.val();
		produceFinalCheck[0].price = produceValidatePrice(self, value);
		produceFinalCheck[0].validCheck();
	});
	
	/**********************************************************************************************
	 * Validation Processing
	 *********************************************************************************************/
	// UPC
	function produceValidateUPC(self, value, index) {
		if(!isNaN(value)) {
			if(value.length < 11) {
				self.parent().parent().addClass('error');
				var alertBox = self.parent().parent().parent().parent().find('.alertBox');
				alertBox.html('<div class="alert alert-error">The UPC must contain 11 digits.</div>');
				return false;
			}	
			else {
				self.parent().parent().removeClass('error');
				var alertBox = self.parent().parent().parent().parent().find('.alertBox');
				alertBox.html('');
				
				// Populate rest of label
				$.post('php/popLabel.php', {upc: value}, function(data) {
					if(data.man != "empty") {
						var label = self.parent().parent().parent();
						label.find('.man').val(data.man);
						label.find('.desc').val(data.desc);
						label.find('.units').val(data.units);
						label.find('.size').val(data.size);
						label.find('.price').val(data.price);
					}
				});
				
				produceFinalCheck[index].upc = true;
				produceFinalCheck[index].man = true;
				produceFinalCheck[index].desc = true;
				produceFinalCheck[index].units = true;
				produceFinalCheck[index].size = true;
				produceFinalCheck[index].price = true;
				produceFinalCheck[index].validCheck();
				
				return true;
			}
		}
		else {
			self.parent().parent().addClass('error');
			var alertBox = self.parent().parent().parent().parent().find('.alertBox');
			alertBox.html('<div class="alert alert-error">The UPC must only contain numbers.</div>');
			return false;
		}	
	}
	
	// Manufacturer
	function produceValidateMan(self, value) {
		if(value.length < 1) {
			self.parent().parent().addClass('error');
			var alertBox = self.parent().parent().parent().parent().find('.alertBox');
			alertBox.html('<div class="alert alert-error">You must enter a manufacturer.</div>');
			return false;
		}	
		else if(value.length > 28) {
			self.parent().parent().addClass('error');
			var alertBox = self.parent().parent().parent().parent().find('.alertBox');
			alertBox.html('<div class="alert alert-error">Manufacturer must be less than 24 characters.</div>');
			return false;
		}
		else {
			self.parent().parent().removeClass('error');
				var alertBox = self.parent().parent().parent().parent().find('.alertBox');
				alertBox.html('');
			return true;
		}	
	}
	
	// Description
	function produceValidateDesc(self, value) {
		if(value.length < 1) {
			self.parent().parent().addClass('error');
			var alertBox = self.parent().parent().parent().parent().find('.alertBox');
			alertBox.html('<div class="alert alert-error">You must enter a description.</div>');
			return false;
		}	
		else if(value.length > 28) {
			self.parent().parent().addClass('error');
			var alertBox = self.parent().parent().parent().parent().find('.alertBox');
			alertBox.html('<div class="alert alert-error">Description must be less than 24 characters.</div>');
			return false;
		}
		else {
			self.parent().parent().removeClass('error');
				var alertBox = self.parent().parent().parent().parent().find('.alertBox');
				alertBox.html('');
			return true;
		}	
	}
	
	// Units
	function validateUnits(self, value) {
		if(value.length < 1) {
			self.parent().parent().addClass('error');
			var alertBox = self.parent().parent().parent().parent().find('.alertBox');
			alertBox.html('<div class="alert alert-error">You must enter the number of units.</div>');
			return false;
		}	
		else {
			self.parent().parent().removeClass('error');
				var alertBox = self.parent().parent().parent().parent().find('.alertBox');
				alertBox.html('');
			return true;
		}	
	}
	
	// Size
	function produceValidateSize(self, value) {
		if(value.length < 1) {
			self.parent().parent().addClass('error');
			var alertBox = self.parent().parent().parent().parent().find('.alertBox');
			alertBox.html('<div class="alert alert-error">You must enter the size.</div>');
			return false;
		}	
		else {
			self.parent().parent().removeClass('error');
				var alertBox = self.parent().parent().parent().parent().find('.alertBox');
				alertBox.html('');
			return true;
		}	
	}
	
	// Units
	function produceValidatePrice(self, value) {
		if(value.length < 1) {
			self.parent().parent().addClass('error');
			var alertBox = self.parent().parent().parent().parent().find('.alertBox');
			alertBox.html('<div class="alert alert-error">You must enter a price.</div>');
			return false;
		}	
		else if(isNaN(value)) {
			self.parent().parent().addClass('error');
			var alertBox = self.parent().parent().parent().parent().find('.alertBox');
			alertBox.html('<div class="alert alert-error">The price must be a number.</div>');
			return false;
		}
		else if(value >= 1000) {
			self.parent().parent().addClass('error');
			var alertBox = self.parent().parent().parent().parent().find('.alertBox');
			alertBox.html('<div class="alert alert-error">The price must be less than $1000.</div>');
			return false;
		}
		else {
			self.parent().parent().removeClass('error');
				var alertBox = self.parent().parent().parent().parent().find('.alertBox');
				alertBox.html('');
			return true;
		}	
	}
	
	/**********************************************************************************************
	 * Add new labels 
	 *********************************************************************************************/
	var count = 1;
	$('.addProduce').click(function(e) {
		e.preventDefault();
		
		$('.produces').append(
			'<div class="produce produce-' + ((count % 2 == 0)? 'left' : 'right') + '">' +
				'<div class="alertBox"></div>' + 
				'<div class="produceForm">' +
					'<div class="control-group">' +
						'<div class="controls">' +
							'<input type="text" class="upc produceUPC" id="upc' + (count + 1) + '" name="upc" ' + 
								'placeholder="UPC" data-provide="typeahead" data-items="4" autocomplete="off" />' +
						'</div>' +
					'</div>' +
					'<div class="control-group">' +
						'<div class="controls">' +
							'<input type="text" class="man" id="man' + (count + 1) + 
								'" name="man' + (count + 1) + '" placeholder="Manufacturer" />' +
						'</div>' +
					'</div>' +
					'<div class="control-group">' +
						'<div class="controls">' +
							'<input type="text" class="desc" id="desc' + (count + 1) + 
								'" name="desc' + (count + 1) + '" placeholder="Description" />' +
						'</div>' +
					'</div>' +
					'<div class="control-group">' +
						'<div class="controls">' +	
							'<input type="text" class="size" id="size' + (count + 1) + 
								'" name="size' + (count + 1) + '" placeholder="Size" />' +
						'</div>' +
					'</div>' +
					'<div class="control-group">' +
						'<div class="controls">' +
							'<input type="text" class="price" id="price' + (count + 1) + 
								'" name="price' + (count + 1) + '" placeholder="Price" "/>' +
						'</div>' +
					'</div>' +
				'</div>' +
			'</div>'
		);
		
		/******************************************************************************************
		 * Add validation 
		 *****************************************************************************************/
		produceFinalCheck.push(new produceValidation());
		produceFinalCheck[0].validCheck();
		
		// UPC (Also includes form population)
		$('.upc').on('change', function(e) {
			var self = $(this);
			var value = self.val();
			var index = parseInt(self.attr('id').replace(/^\D+/g, '')) - 1;
			produceFinalCheck[index].upc = produceValidateUPC(self, value, index);
			produceFinalCheck[index].validCheck();
		});
		
		// Manufacturer
		$('.man').on('change', function(e) {
			var self = $(this);
			var value = self.val();
			var index = parseInt(self.attr('id').replace(/^\D+/g, '')) - 1;
			produceFinalCheck[index].man = produceValidateMan(self, value);
			produceFinalCheck[index].validCheck();
		});
		
		// Description
		$('.desc').on('change', function(e) {
			var self = $(this);
			var value = self.val();
			var index = parseInt(self.attr('id').replace(/^\D+/g, '')) - 1;
			produceFinalCheck[index].desc = produceValidateDesc(self, value);
			produceFinalCheck[index].validCheck();
		});
		
		// Units
		$('.units').on('change', function(e) {
			var self = $(this);
			var value = self.val();
			var index = parseInt(self.attr('id').replace(/^\D+/g, '')) - 1;
			produceFinalCheck[index].units = validateUnits(self, value);
			produceFinalCheck[index].validCheck();
		});
		
		// Size
		$('.size').on('change', function(e) {
			var self = $(this);
			var value = self.val();
			var index = parseInt(self.attr('id').replace(/^\D+/g, '')) - 1;
			produceFinalCheck[index].size = produceValidateSize(self, value);
			produceFinalCheck[index].validCheck();
		});
		
		// Price
		$('.price').on('change', function(e) {
			var self = $(this);
			var value = self.val();
			var index = parseInt(self.attr('id').replace(/^\D+/g, '')) - 1;
			produceFinalCheck[index].price = produceValidatePrice(self, value);
			produceFinalCheck[index].validCheck();
		});
		
		// Typeahead
		$('.produceUPC').typeahead(typeahead_opt);
		
		// Keyup = Change
		$('input').on('keyup', function(e) {
			e.preventDefault();
			$(this).change();
		});
		
		// Increment count
		count++;
	});
	
	/**********************************************************************************************
	 * Process Labels
	 *********************************************************************************************/
	$('.generateProduce').click(function(e) {
		e.preventDefault();
		
		var labels = {};
		var count = 0;
		$('.produces .produce').each(function(index) {
			var label = {};
			label.upc = $(this).find('.upc').val();
			label.manufacturer = $(this).find('.man').val();
			label.description = $(this).find('.desc').val();
			label.size = $(this).find('.size').val();
			label.price = $(this).find('.price').val();
			labels[count] = label;
			count++;
		});
		
		labels = JSON.stringify(labels);
		$('#produceForm').append('<input type="hidden" id="produces" name="produces" />');
		$('#produces').val(labels);
		$('#produceForm').submit();
		
		//$(window).load("php/labels.php", {'labels': labels});
	});
});
