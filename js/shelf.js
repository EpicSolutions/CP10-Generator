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
	
	$('.shelfUPC').typeahead(typeahead_opt);
	
	/**********************************************************************************************
	 * Validation Initialization
	 *********************************************************************************************/
	// Array of validaitons
	var shelfFinalCheck = [];
	
	// Validation class
	function shelfValidation() {
		this.upc   = false;
		this.man   = false;
		this.desc  = false;
		this.size  = false;
		this.price = false;
		this.validCheck = function() {
			for(var i = 0; i < shelfFinalCheck.length; i++) {
				var ready = true;
				if(!(shelfFinalCheck[i].upc && shelfFinalCheck[i].man && shelfFinalCheck[i].desc 
				  && shelfFinalCheck[i].size && shelfFinalCheck[i].price)) {
					$('.generateShelf').addClass('disabled');
					$('.generateShelf').prop('disabled', true);
					ready = false;
				}
				if(!ready) {
					return ready;
				}
			}
			
			$('.generateShelf').removeClass('disabled');
			$('.generateShelf').prop('disabled', false);
			return ready;
		};
	}
	
	// Add initial validation for initial label	
	shelfFinalCheck.push(new shelfValidation());
		
	// UPC (Also includes form population)
	$('.upc').on('keyup', function(e) {
		var self = $(this);
		var value = self.val();
		shelfFinalCheck[0].upc = validateUPC(self, value, 0);
		shelfFinalCheck[0].validCheck();
	});
	
	// Manufacturer
	$('.man').on('keyup', function(e) {
		var self = $(this);
		var value = self.val();
		shelfFinalCheck[0].man = validateMan(self, value);
		shelfFinalCheck[0].validCheck();
	});
	
	// Description
	$('.desc').on('keyup', function(e) {
		var self = $(this);
		var value = self.val();
		shelfFinalCheck[0].desc = validateDesc(self, value);
		shelfFinalCheck[0].validCheck();
	});
	
	// Size
	$('.size').on('keyup', function(e) {
		var self = $(this);
		var value = self.val();
		shelfFinalCheck[0].size = validateSize(self, value);
		shelfFinalCheck[0].validCheck();
	});
	
	// Price
	$('.price').on('keyup', function(e) {
		var self = $(this);
		var value = self.val();
		shelfFinalCheck[0].price = validatePrice(self, value);
		shelfFinalCheck[0].validCheck();
	});
	
	/**********************************************************************************************
	 * Validation Processing
	 *********************************************************************************************/
	// UPC
	function validateUPC(self, value, index) {
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
				
				shelfFinalCheck[index].upc = true;
				shelfFinalCheck[index].man = true;
				shelfFinalCheck[index].desc = true;
				shelfFinalCheck[index].units = true;
				shelfFinalCheck[index].size = true;
				shelfFinalCheck[index].price = true;
				shelfFinalCheck[index].validCheck();
				
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
	function validateMan(self, value) {
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
	function validateDesc(self, value) {
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
	function validateSize(self, value) {
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
	function validatePrice(self, value) {
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
	$('.addShelf').click(function(e) {
		e.preventDefault();
		
		$('.shelfs').append(
			'<div class="shelf shelf-' + ((count % 2 == 0)? 'left' : 'right') + '">' +
				'<div class="alertBox"></div>' + 
				'<div class="shelfForm">' +
					'<div class="control-group">' +
						'<div class="controls">' +
							'<input type="text" class="upc shelfUPC" id="upc' + (count + 1) + '" name="upc" ' + 
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
		shelfFinalCheck.push(new shelfValidation());
		shelfFinalCheck[0].validCheck();
		
		// UPC (Also includes form population)
		$('.upc').on('change', function(e) {
			var self = $(this);
			var value = self.val();
			var index = parseInt(self.attr('id').replace(/^\D+/g, '')) - 1;
			shelfFinalCheck[index].upc = validateUPC(self, value, index);
			shelfFinalCheck[index].validCheck();
		});
		
		// Manufacturer
		$('.man').on('change', function(e) {
			var self = $(this);
			var value = self.val();
			var index = parseInt(self.attr('id').replace(/^\D+/g, '')) - 1;
			shelfFinalCheck[index].man = validateMan(self, value);
			shelfFinalCheck[index].validCheck();
		});
		
		// Description
		$('.desc').on('change', function(e) {
			var self = $(this);
			var value = self.val();
			var index = parseInt(self.attr('id').replace(/^\D+/g, '')) - 1;
			shelfFinalCheck[index].desc = validateDesc(self, value);
			shelfFinalCheck[index].validCheck();
		});
		
		// Units
		$('.units').on('change', function(e) {
			var self = $(this);
			var value = self.val();
			var index = parseInt(self.attr('id').replace(/^\D+/g, '')) - 1;
			shelfFinalCheck[index].units = validateUnits(self, value);
			shelfFinalCheck[index].validCheck();
		});
		
		// Size
		$('.size').on('change', function(e) {
			var self = $(this);
			var value = self.val();
			var index = parseInt(self.attr('id').replace(/^\D+/g, '')) - 1;
			shelfFinalCheck[index].size = validateSize(self, value);
			shelfFinalCheck[index].validCheck();
		});
		
		// Price
		$('.price').on('change', function(e) {
			var self = $(this);
			var value = self.val();
			var index = parseInt(self.attr('id').replace(/^\D+/g, '')) - 1;
			shelfFinalCheck[index].price = validatePrice(self, value);
			shelfFinalCheck[index].validCheck();
		});
		
		// Typeahead
		$('.shelfUPC').typeahead(typeahead_opt);
		
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
	$('.generateShelf').click(function(e) {
		e.preventDefault();
		
		var labels = {};
		var count = 0;
		$('.shelfs .shelf').each(function(index) {
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
		$('#shelfForm').append('<input type="hidden" id="shelfs" name="shelfs" />');
		$('#shelfs').val(labels);
		$('#shelfForm').submit();
		
		//$(window).load("php/labels.php", {'labels': labels});
	});
});
