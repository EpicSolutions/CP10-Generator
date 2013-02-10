$(function() {
	$('.shelfTabs').css('display', 'none');
	$('.palletTabs').css('display', 'none');
	$('.produceTabs').css('display', 'none');
	
	$('.nav-pills li a').click(function(e) {
		e.preventDefault();
		
		$('.labelTabs').css('display', 'none');
		$('.shelfTabs').css('display', 'none');
		$('.palletTabs').css('display', 'none');
		$('.produceTabs').css('display', 'none');
		var tab = $(this).attr('class').replace('Button', '');
		var tab = '.' + tab + 'Tabs';
		$(tab).css('display', 'block');
		
		$('.nav-stacked li').removeClass('active');
		$(this).parent().addClass('active');
	});

});
