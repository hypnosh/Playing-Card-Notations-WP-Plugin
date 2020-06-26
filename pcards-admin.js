// pcards-admin.js

jQuery(function($){
	$(".pcards-options").on('click', function() {
		$(this).find("input").attr('checked', 'true').change();
	}); // .pcards-options click
}); // $