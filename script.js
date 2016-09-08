var bds = {};

bds.gup = function (name) {
    'use strict';
    var regexS = "[\\?&]" + name + "=([^&#]*)",
        regex = new RegExp(regexS),
        results = regex.exec(window.location.href);
    if (results === null) {
        return "";
    } else {
        return results[1];
    }
};

jQuery(document).ready(function () {
    'use strict';
	var ids = ['description', 'cause', 'content', 'task', 'reason', 'opinion'];
    
	for (var i = 0; i < ids.length; i++) {
		var textarea = jQuery("#" + ids[i]);
		if (textarea.length > 0) {
			textarea.before('<div id="toolbar'+ids[i]+'"></div>');
			if (textarea.parents("form").find("input[name=id]").length === 0) {
				textarea.before('<input type="hidden" name="id" value="'+bds.gup('id')+'" />');
			}
			initToolbar('toolbar'+ids[i], ids[i], toolbar);
		}
	}

	var $conf = jQuery("#bez_removal_confirm");
	$conf.find(".no").click(function(e) {
		e.preventDefault();
		$conf.hide();
	});

	//delete_button 
	var $delete_buts = jQuery("#bez_comments, #bez_causes").find(".bez_delete_button");
	jQuery("body").bind("click", function (e) {
		var $target = jQuery(e.target);
		if (!$target.is($delete_buts)) {
			$conf.hide();
        }
	});

	$delete_buts.each(function() {
		jQuery(this).click(function(e) {
			e.preventDefault();
			var $click = jQuery(this);
			var off = $click.offset();
			$conf.appendTo("body");
			$conf.css({
					'position': 'absolute',
					'left':		off.left-$conf.width()+$click.width(),
					'top':	off.top+2,
				});
			$conf.find("input").unbind("click");
			$conf.find("input").bind("click", function(e) {
				e.preventDefault();
				window.location = $click.attr("href");
			});
			$conf.show();
		});
	});

	//entities sort
	jQuery("#entities_form input[type=button]").click(function() {
		var textarea = jQuery(this).parents("form").find("textarea");
		var lines = jQuery.trim(textarea.val()).split("\n");
		lines.sort();
		textarea.val(lines.join("\n"));
	});

	jQuery("input[name=plan_date]").datepicker({
		dateFormat: "yy-mm-dd"
		});
	if (jQuery("input[name=all_day_event]").is(":checked")) {
		jQuery("input[name=start_time]").prop( "disabled", true );
		jQuery("input[name=finish_time]").prop( "disabled", true );
	}
	jQuery("input[name=all_day_event]").on('change', function() {
		if (jQuery(this).is(":checked")) {
			jQuery("input[name=start_time]").prop( "disabled", true );
			jQuery("input[name=finish_time]").prop( "disabled", true );
		} else {
			jQuery("input[name=start_time]").prop( "disabled", false );
			jQuery("input[name=finish_time]").prop( "disabled", false );
		}
	});
	//timepicker
	
	var hours = ["00:30", "1:00", "1:30", "2:00", "2:30", "3:00", "3:30", "4:00",
			"4:30", "5:00", "5:30", "6:00", "6:30", "7:00", "7:30", "8:00", "8:30", "9:00", "9:30", "10:00", "10:30", "11:00", "11:30",
			"12:00", "12:30", "13:00", "13:30", "14:00", "14:30", "15:00", "15:30",
			"16:00", "16:30", "17:00", "17:30", "18:00", "18:30", "19:00", "19:30",
			"20:00", "20:30", "21:00", "21:30", "22:00", "22:30", "23:00", "23:30",
			"24:00" ];
	
	//ukrywanie niepotrzebnych godzin zależnie od godziny rozpoczęcia
	var hide_unneeded_hours = function ($this) {
		var hour = $this.val(),
            index = hours.indexOf(hour),
            $finish_time_li = jQuery("#bez_timepicker_finish_time li");
		
		$finish_time_li.show();
		$finish_time_li.eq(index).prevAll().hide();
		
		if (jQuery("input[name=finish_time]").val() === '') {
			jQuery("input[name=finish_time]").val(hour);
        }
	};
	jQuery("input[name=start_time]").blur(function () {
		hide_unneeded_hours(jQuery(this));
	});

	
	var autoFill = function (hour) {
		if (hour.indexOf(":") === -1) {
			hour += ":00";
        } else if (hour.match(/^[0-9]{1,2}:$/g)) {
			hour += "00";
		} else if (hour.match(/^[0-9]{1,2}:(0|3)$/g)) {
			hour += "0";
		} else if (hour.match(/^[0-9]{1,2}:(1|2)$/g)) {
			hour = hour.slice(0,-1)+"00";
		} else if (hour.match(/^[0-9]{1,2}:[4-9]$/g)) {
			hour = hour.slice(0,-1)+"30";
		} else if (hour.match(/^[0-9]{1,2}:(0|3)[1-9]$/g)) {
			hour = hour.slice(0,-1)+"0";
        }
		return hour;
	};
	var listScrool = function($list, hour) {
		hour = autoFill(hour);
		var index = hours.indexOf(hour);
		if (index === -1) { 
            index = 0;
        }
		var $li = $list.find("li:first");
		//hidden lis
		var $hid_lis = $list.find("li:hidden");
		$list.scrollTop((index - $hid_lis.length - 1) * $li.outerHeight());
		$list.find("li").removeClass("selected");
		$list.find("li").eq(index).addClass("selected");
		
	};

	jQuery(".bez_timepicker").each(function() {
		var $this = jQuery(this);

		var id = "bez_timepicker_"+$this.attr("name"),
		    $wrapper = jQuery(document.createElement('div'))
                .css('position', 'absolute').addClass('bez_timepicker_wrapper')
		        .hide().attr("id", id).appendTo("body");
		
		var offset = $this.offset();
		offset.top += $this.outerHeight() + 1;
		$wrapper.offset(offset);
		
		var $ul = jQuery(document.createElement("ul")).appendTo($wrapper);

		
		for (var h in hours) {
			var hour = hours[h],
			    $li = jQuery(document.createElement("li"));
			$li.text(hour);
			$ul.append($li);
		}
       $ul.on('mousedown', 'li', function(event) {
				var id = jQuery(this).parents("div").attr("id"),
				    name = id.replace("bez_timepicker_", '');
				jQuery("input[name="+name+"]").val(jQuery(this).text());
				
				jQuery(this).siblings().removeClass("selected");
				jQuery(this).addClass("selected");
			});

		$this.focus(function() {
			var $this = jQuery(this);
			var id = "bez_timepicker_"+$this.attr("name");
			$wrapper = jQuery("#"+id);
			$wrapper.show();
			listScrool($wrapper, $this.val());
			
		});
		$this.blur(function() {
			var $this = jQuery(this);
			var id = "bez_timepicker_"+$this.attr("name");
			$wrapper = jQuery("#"+id);
			$wrapper.hide();
		});
		$this.change(function() {
			var $this = jQuery(this);
			var id = "bez_timepicker_"+$this.attr("name");
			$wrapper = jQuery("#"+id);
			$this.val($wrapper.find("li.selected").text());
			$wrapper.hide();
		});
		$this.on('keyup', function() {
			var $this = jQuery(this);
			var id = "bez_timepicker_"+$this.attr("name");
			$wrapper = jQuery("#"+id);
			listScrool($wrapper, $this.val());
		});
	});
	hide_unneeded_hours(jQuery("input[name=start_time]"));
	
	jQuery("#bez_task_context").hide();
	jQuery("#bez_task_context_show_button").show().click(function () {
		jQuery("#bez_task_context").show();
		jQuery(this).hide();
	});
	
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
});
