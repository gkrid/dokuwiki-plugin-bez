bez.ctl.tasks = function() {
  	//bez_show_desc
	jQuery(".bez_desc_row").hide();
	jQuery("#bez_show_desc").on('click', function(e) {
		if (jQuery(this).find(".show").is(":visible")) {
			jQuery(".bez_desc_row").show();
			jQuery(this).find(".hide").show();
			jQuery(this).find(".show").hide();
		} else {
			jQuery(".bez_desc_row").hide();
			jQuery(this).find(".hide").hide();
			jQuery(this).find(".show").show();
		}
		e.preventDefault();
	});
	
	jQuery(".bez_show_single_desc").on('click', function(e) {
		var $row = jQuery(this).parents('tr'),
			row_id = $row.data('bez-row-id'),
			$desc_rows = $row.parents('table').find('.task'+row_id);
			
		if ($desc_rows.is(":visible")) {
			$desc_rows.hide();
		} else {
			$desc_rows.show();
		}
		e.preventDefault();
	});
	
	//highlight filters
	var highlight = function() {
		var $this = jQuery(this);
		if ($this.find('option:selected').index() !== 0) {
			$this.css('outline', '2px solid #FB5A5A');
		} else {
			$this.css('outline', 'none');
		}
	};
	var highlight_input = function() {
		var $this = jQuery(this);
		if ($this.val().length > 0) {
			$this.css('outline', '2px solid #FB5A5A');
		} else {
			$this.css('outline', 'none');
		}
	};
	jQuery(".bez_filter_form select").change(highlight).each(highlight);
	jQuery(".bez_filter_form input:text").blur(highlight_input).each(highlight_input);

	jQuery(".plugin__bez_bulk_checkbox").on('change', function(e) {
		if (jQuery(".plugin__bez_bulk_checkbox:checked").length > 0) {
			jQuery("#plugin__bez_bulk_actions_box").show();
		} else {
			jQuery("#plugin__bez_bulk_actions_box").hide();
		}
	});

	jQuery('#plugin__bez_bulk_actions_box button[value=bulk_delete]').click('on', function (event) {
		if (!window.confirm(LANG.plugins.bez.bulk_delete_confirm)) {
			event.preventDefault();
		}
	});

	jQuery('#plugin__bez_bulk_actions_box button[value=bulk_move]').click('on', function (event) {
		if (!window.confirm(LANG.plugins.bez.bulk_move_confirm)) {
			event.preventDefault();
		}
	});
};