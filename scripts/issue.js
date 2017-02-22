
bez.ctl.issue = function() {

	jQuery('.bez_commcause_delete_prompt').click('on', function (event) {
		event.preventDefault();

		if (window.confirm(LANG.plugins.bez.remove_confirm)) {
			var kid = jQuery(this).data('kid'),
				$block = jQuery(this).parents('.bez_comment');
			
			
			
			jQuery.post(
				DOKU_BASE + 'lib/exe/ajax.php?time='+Date.now(),
				{
					call: 'plugin_bez', 
					action: 'commcause_delete',
					kid:	kid
				},
				function(data) {
					// data is array you returned with action.php
					if (data.state === 'ok') {
						$block.hide('slow', function(){ $block.remove(); });
					}
				},
				'json'
			)
			.fail(function(d) {
				console.log(d.responseText);
			});
		}
	});
		
	var $bez_comment_form = jQuery('.bez_comment_form'),
		$tabs = $bez_comment_form.find('.bez_tabs');
	if ($tabs.length > 0) {
		var $links = $tabs.find('a'),
			$active = $tabs.find('li.active a'),
			active	= $active[0],
			$comment_type_input = jQuery(".bez_comment_type"),
			$cause_type_div = jQuery(".bez_cause_type");

		var setActive = function($a) {
			$tabs.find('.active').removeClass('active');
			$a.parent().addClass('active');
		}
		
		var activateComment = function () {		
			$comment_type_input.removeAttr("disabled");
			$cause_type_div.hide();
			$cause_type_div.find("input").attr("disabled", "disabled");
			
			$bez_comment_form.removeClass('bez_cause');
		};
		
		var activateCause = function() {
			$comment_type_input.attr("disabled", "disabled");
			$cause_type_div.show();
			$cause_type_div.find("input").removeAttr("disabled");
			
			$bez_comment_form.addClass('bez_cause');
		};

		if (active.hash === '#comment') {
			setActive($active);
			activateComment();
		} else if (active.hash === '#cause') {
			setActive($active);
			activateCause();
		}

		
		$tabs.on('click', 'a', function (e) {
			e.preventDefault();
			setActive(jQuery(this));
		});
		
		$tabs.on('click', 'a[href="#comment"]', activateComment);
		$tabs.on('click', 'a[href="#cause"]', activateCause);
		
		//textareas
		var $textarea = $bez_comment_form.find("textarea");
		var $header = $bez_comment_form.find(".bez_toolbar");
		bez.rich_text_editor($textarea, $header);
	}

	//show/hide comments
	var $show_comments = jQuery(".bez_show_comments"),
		$hide_comments = jQuery(".bez_hide_comments");
	
	var bez_show_comments = function() {	
		$show_comments.hide();
		$hide_comments.show();
		localStorage.setItem('comments_are_hidden', '0');
		jQuery('.bez_type_0').show();
	};
	
	var bez_hide_comments = function() {
		$hide_comments.hide();
		$show_comments.show();
		localStorage.setItem('comments_are_hidden', '1');
		jQuery('.bez_type_0').hide();
	};
	
	var comments_are_hidden = localStorage.getItem('comments_are_hidden');
	if (comments_are_hidden === null || comments_are_hidden === '0') {
		bez_show_comments();
	} else {
		bez_hide_comments();
	}
	
	$show_comments.on('click', function(e) { e.preventDefault(); bez_show_comments() });
	$hide_comments.on('click', function(e) { e.preventDefault(); bez_hide_comments() });
	
	if (jQuery('.bez_task_form').length > 0) {
		var $task_form = jQuery('.bez_task_form');
		//date picker
		jQuery("input[name=plan_date]").datepicker({
			dateFormat: "yy-mm-dd"
			});
		if (jQuery("input[name=all_day_event]").is(":checked")) {
			jQuery('#task_datapair').hide();
		}
		jQuery("input[name=all_day_event]").on('change', function() {
			if (jQuery(this).is(":checked")) {
				jQuery('#task_datapair').hide();
			} else {
				jQuery('#task_datapair').show();
			}
		});
		
		//time picker
		jQuery('#task_datapair .time').timepicker({
				'showDuration': true,
				'timeFormat': 'H:i'
			});
		var timeDatepair = new Datepair(jQuery('#task_datapair').get(0));
		
		//cost
		//~ jQuery('input[name=cost]').spinner({
			//~ min: 0,
			//~ max: 100000,
			//~ step: 50
		//~ });
		
		bez.rich_text_editor($task_form.find('textarea'), $task_form.find('.bez_toolbar'));
		
		//~ $task_form.validetta({
			
		//~ });
	}
	
	if (jQuery('#opinion').length > 0) {
		bez.rich_text_editor(jQuery('#opinion'), jQuery('.bez_opinion_toolbar'));
	}
	
	if (jQuery('#reason').length > 0) {
		bez.rich_text_editor(jQuery('#reason'), jQuery('.bez_reason_toolbar'));
	}
	
	//tooltips
	jQuery(document).tooltip({
		items: '#issue_participants a[title]',
		position: {
			my: "left top+15",
			at: "left bottom",
			collision: "flipfit"
		},
		content: function() {
			var $this = jQuery(this);
				name = $this.find('.bez_name').text(),
				content = '<div style="margin-bottom: 3px;">'+name+'</div>';
			$this.find('.bez_awesome').each(function() {
				var $this = jQuery(this);
				content += '<div>'+$this.get(0).outerHTML+' '+$this.attr('title')+'</div>';
			});
			
			return content;
		}
	});

};
