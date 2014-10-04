/* DOKUWIKI:include jquery.dataTables.js */
/* DOKUWIKI:include jquery.dataTables.yadcf.js */

bds = {};

bds.gup = function (name) {
    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regexS = "[\\?&]" + name + "=([^&#]*)";
    var regex = new RegExp(regexS);
    var results = regex.exec(window.location.href);
    if (results == null)
        return "";
    else
        return results[1];
};

jQuery(document).ready(function() {
	var ids = ['description', 'cause', 'content', 'task'];

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

	//show/hide opinion
	$opinion_row = jQuery("#bds_change_issue textarea[name=opinion]").parents("div[class=row]");
	
	if ($opinion_row.length > 0) {
		var $select = jQuery("#bds_change_issue select[name=state]"); 
		switch ($select.val()) {
			case "0":
			case "1":
				$opinion_row.hide();
			break;

			case "2":
			case "3":
			case "4":
				$opinion_row.show();
			break;
		}
		$select.change(function() {
			switch (jQuery(this).val()) {
				case "0":
				case "1":
					$opinion_row.hide();
				break;

				case "2":
				case "3":
				case "4":
					$opinion_row.show();
				break;
			}
		});
	}

	//show/hide reason
	$reason_row = jQuery("#task_form textarea[name=reason]").parents("div[class=row]");
	
	if ($reason_row.length > 0) {
		$reason_row.hide();
		$select = jQuery("#task_form select[name=state]");
		var prev_val = $select.val();
		$select.change(function() {
			if (jQuery(this).val() === prev_val) {
				$reason_row.hide();
			} else {
				$reason_row.show();
			}
		});
	}

	var show = function() {
			console.log(this);
			jQuery(this).siblings(".bds_block_content").show();
			jQuery(this).find(".toggle").css("background-image", "url(lib/plugins/bds/images/expanded.png)");
		};
	var hide = function() {
			jQuery(this).siblings(".bds_block_content").hide();
			jQuery(this).find(".toggle").css("background-image", "url(lib/plugins/bds/images/collapsed.png)");
		};

	jQuery(".bds_block")
		.each(function() {
			$h1 = jQuery(this).find("h1").html(
				function(index, oldhtml) {
					return '<span class="toggle">'+oldhtml+'</span>';
				});

			$h1.find(".toggle").css(
				{
					'background': 'url("lib/plugins/bds/images/collapsed.png") no-repeat scroll 4px 50% rgba(0, 0, 0, 0)',
					'border': 'medium none',
					'border-radius': '0.3em',
					'box-shadow': '0.1em 0.1em 0.3em 0 #BBBBBB',
					'color': '#222222',
					'padding': '0.3em 0.5em 0.3em 20px',
					'text-shadow': '0.1em 0.1em #FCFCFC',
					'cursor': 'pointer'
				});


			var hash = window.location.hash.substring(1);
			if (hash.indexOf("bez_comment") !== -1) {
				var showed = "bez_comment";
			} else if (hash.indexOf("bez_cause") !== -1) {
				var showed = "bez_cause";
			} else if (hash.indexOf("bez_task") !== -1) {
				var showed = "bez_task";
			} else if (hash === "bds_change_issue") {
				var showed = "bds_change_issue";
			}

			if (jQuery(this).attr("id") === showed) {
				jQuery(this).find(".toggle").css("background-image", "url(lib/plugins/bds/images/expanded.png)");
				jQuery(this).find("h1").toggle(hide, show);
			} else {
				jQuery(this).find(".bds_block_content").hide();
				jQuery(this).find("h1").toggle(show, hide);
			}
		});
	jQuery(".bds_block .history_anchor").click(function() {
		show.call(jQuery("#bds_history h1")[0]);
	});

});
