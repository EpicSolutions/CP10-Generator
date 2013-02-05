$(function() {
	$('.shelfTabs').css('display', 'none');
	$('.palletTabs').css('display', 'none');
	
	$('.nav-pills li a').click(function(e) {
		e.preventDefault();
		
		$('.labelTabs').css('display', 'none');
		$('.shelfTabs').css('display', 'none');
		$('.palletTabs').css('display', 'none');
		var tab = $(this).attr('class').replace('Button', '');
		$('.' + tab + 'Tabs').css('display', 'block');
		
		$('li').removeClass('active');
		$(this).parent().addClass('active');
	});

});
