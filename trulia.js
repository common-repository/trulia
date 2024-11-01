jQuery(document).ajaxSuccess(function(e, xhr, settings) {
	var widget_id_base = 'trulia';

	if(settings.data.search('action=save-widget') != -1 && settings.data.search('id_base=' + widget_id_base) != -1) {
		jQuery('ul.ac_results').remove();
		jQuery('input.city_state').trigger('load');
		// do other stuff
	}
});

jQuery(document).ready(function($) {
	function rgb2hex(sentRGB) {
		rgb = sentRGB.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+))?\)$/);
		if(!rgb) { return sentRGB; }
		function hex(x) {
		    return ("0" + parseInt(x).toString(16)).slice(-2);
		}
		return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
	}			

	/*
	// For future use to auto-update map when settings have been changed.
	$('.trulia_container input,.trulia_container select,.trulia_container textarea').live('change', function() {					
		
	});
	*/
	
	$('.swatch').live('click', function() {
		var $that = $(this);
		
		$that.parents('.trulia_container').find('td.swatch_picker_cell.active_swatch_picker span.swatch_picker').css('background-color', $that.css('background-color'));
		if($('td.background_picker_cell', $that.parents('.trulia_container')).is('.active_swatch_picker')) {
			$that.parents('.trulia_container').find('input.map_background_color').val(rgb2hex($that.css('background-color')));
			$that.parents('.trulia_container').find('.truliamap').css('background-color', $that.css('background-color'));
			
			// Update the logo color to match.
			$that.removeClass('swatch');
			var logoColor = $that.attr('class');
			$that.addClass('swatch');
			
			if(logoColor && logoColor !== '') {
				$that.parents('.trulia_container').find('input.logo_color').val(logoColor);
				var $logo = $('.truliamap .map_logo img', $('.trulia_container'));
				$logo.css('background-image', $logo.css('background-image').replace(/logo\-(.*)\.gif/, 'logo-'+logoColor+'.gif'));
			}
		} else {
			$that.parents('.trulia_container').find('input.map_text_color').val(rgb2hex($that.css('background-color')));
			$that.parents('.trulia_container').find('.truliamap_header').css('color', false).css('color',$that.css('background-color'));
		}
	});
	
	$('.background_label').live('click', function() { $('.background_picker_cell').click(); });
	$('.text_label').live('click', function() { $('.text_picker_cell').click(); });
	
	$('td.swatch_picker_cell').live('click', function() {
		var $that = $(this);
		$('td.swatch_picker_cell', $that.parents('.trulia_container')).removeClass('active_swatch_picker');
		$that.addClass('active_swatch_picker');
		if($that.is('.background_picker_cell')) { 
			$('.background_label').css('font-weight', 'bold').css('font-size', '.9em');
			$('.text_label').css('font-weight', 'normal').css('font-size', '1em');
			$('input.active_swatch_picker').val('background');
		} else {
			$('.background_label').css('font-weight', 'normal').css('font-size', '1em');
			$('.text_label').css('font-weight', 'bold').css('font-size', '.9em');
			$('input.active_swatch_picker').val('text');
		}
	});
	
	$('input.city_state').live('load', function(e) {			
		$(this).suggest(ajaxurl + "?action=trulia_locations");
	});
	$('input.city_state').trigger('load');
});