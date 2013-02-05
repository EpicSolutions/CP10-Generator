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
	
	$('.palletUPC').typeahead(typeahead_opt);
	
	/**********************************************************************************************
	 * Validation Initialization
	 *********************************************************************************************/
	// Array of validaitons
	var palletFinalCheck = [];
	
	// Validation class
	function palletValidation() {
		this.upc   = false;
		this.man   = false;
		this.desc  = false;
		this.size  = false;
		this.price = false;
		this.validCheck = function() {
			for(var i = 0; i < palletFinalCheck.length; i++) {
				var ready = true;
				if(!(palletFinalCheck[i].upc && palletFinalCheck[i].man && palletFinalCheck[i].desc 
				  && palletFinalCheck[i].size && palletFinalCheck[i].price)) {
					$('.generatePallet').addClass('disabled');
					$('.generatePallet').prop('disabled', true);
					ready = false;
				}
				if(!ready) {
					return ready;
				}
			}
			
			$('.generatePallet').removeClass('disabled');
			$('.generatePallet').prop('disabled', false);
			return ready;
		};
	}
	
	// Add initial validation for initial label	
	palletFinalCheck.push(new palletValidation());
		
	// UPC (Also includes form population)
	$('.upc').on('change', function(e) {
		var self = $(this);
		var value = self.val();
		palletFinalCheck[0].upc = palletValidateUPC(self, value, 0);
		palletFinalCheck[0].validCheck();
	});
	
	// Manufacturer
	$('.man').on('change', function(e) {
		var self = $(this);
		var value = self.val();
		palletFinalCheck[0].man = palletValidateMan(self, value);
		palletFinalCheck[0].validCheck();
	});
	
	// Description
	$('.desc').on('change', function(e) {
		var self = $(this);
		var value = self.val();
		palletFinalCheck[0].desc = palletValidateDesc(self, value);
		palletFinalCheck[0].validCheck();
	});
	
	// Size
	$('.size').on('change', function(e) {
		var self = $(this);
		var value = self.val();
		palletFinalCheck[0].size = palletValidateSize(self, value);
		palletFinalCheck[0].validCheck();
	});
	
	// Price
	$('.price').on('change', function(e) {
		var self = $(this);
		var value = self.val();
		palletFinalCheck[0].price = palletValidatePrice(self, value);
		palletFinalCheck[0].validCheck();
	});
	
	/**********************************************************************************************
	 * Validation Processing
	 *********************************************************************************************/
	// UPC
	function palletValidateUPC(self, value, index) {
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
				
				palletFinalCheck[index].upc = true;
				palletFinalCheck[index].man = true;
				palletFinalCheck[index].desc = true;
				palletFinalCheck[index].units = true;
				palletFinalCheck[index].size = true;
				palletFinalCheck[index].price = true;
				palletFinalCheck[index].validCheck();
				
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
	function palletValidateMan(self, value) {
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
	function palletValidateDesc(self, value) {
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
	function palletValidateSize(self, value) {
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
	function palletValidatePrice(self, value) {
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
		else if(value >= 100) {
			self.parent().parent().addClass('error');
			var alertBox = self.parent().parent().parent().parent().find('.alertBox');
			alertBox.html('<div class="alert alert-error">The price must be less than $100.</div>');
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
	$('.addPallet').click(function(e) {
		e.preventDefault();
		
		$('.pallets').append(
			'<div class="pallet pallet-' + ((count % 2 == 0)? 'left' : 'right') + '">' +
				'<div class="alertBox"></div>' + 
				'<div class="palletForm">' +
					'<div class="control-group">' +
						'<div class="controls">' +
							'<input type="text" class="upc palletUPC" id="upc' + (count + 1) + '" name="upc" ' + 
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
								'" name="price' + (count + 1) + '" />' +
						'</div>' +
					'</div>' +
				'</div>' +
			'</div>'
		);
		
		/******************************************************************************************
		 * Add validation 
		 *****************************************************************************************/
		palletFinalCheck.push(new palletValidation());
		palletFinalCheck[0].validCheck();
		
		// UPC (Also includes form population)
		$('.upc').on('change', function(e) {
			var self = $(this);
			var value = self.val();
			var index = parseInt(self.attr('id').replace(/^\D+/g, '')) - 1;
			palletFinalCheck[index].upc = palletValidateUPC(self, value, index);
			palletFinalCheck[index].validCheck();
		});
		
		// Manufacturer
		$('.man').on('change', function(e) {
			var self = $(this);
			var value = self.val();
			var index = parseInt(self.attr('id').replace(/^\D+/g, '')) - 1;
			palletFinalCheck[index].man = palletValidateMan(self, value);
			palletFinalCheck[index].validCheck();
		});
		
		// Description
		$('.desc').on('change', function(e) {
			var self = $(this);
			var value = self.val();
			var index = parseInt(self.attr('id').replace(/^\D+/g, '')) - 1;
			palletFinalCheck[index].desc = palletValidateDesc(self, value);
			palletFinalCheck[index].validCheck();
		});
		
		// Units
		$('.units').on('change', function(e) {
			var self = $(this);
			var value = self.val();
			var index = parseInt(self.attr('id').replace(/^\D+/g, '')) - 1;
			palletFinalCheck[index].units = validateUnits(self, value);
			palletFinalCheck[index].validCheck();
		});
		
		// Size
		$('.size').on('change', function(e) {
			var self = $(this);
			var value = self.val();
			var index = parseInt(self.attr('id').replace(/^\D+/g, '')) - 1;
			palletFinalCheck[index].size = palletValidateSize(self, value);
			palletFinalCheck[index].validCheck();
		});
		
		// Price
		$('.price').on('change', function(e) {
			var self = $(this);
			var value = self.val();
			var index = parseInt(self.attr('id').replace(/^\D+/g, '')) - 1;
			palletFinalCheck[index].price = palletValidatePrice(self, value);
			palletFinalCheck[index].validCheck();
		});
		
		// Typeahead
		$('.palletUPC').typeahead(typeahead_opt);
		
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
	$('.generatePallet').click(function(e) {
		e.preventDefault();
		
		var labels = {};
		var count = 0;
		$('.pallets .pallet').each(function(index) {
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
		$('#palletForm').append('<input type="hidden" id="pallets" name="pallets" />');
		$('#pallets').val(labels);
		$('#palletForm').submit();
		
		//$(window).load("php/labels.php", {'labels': labels});
	});
});
