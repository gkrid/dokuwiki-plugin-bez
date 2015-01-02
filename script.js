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

	//show/hide opinion
	var $form = jQuery("#bez_issue_report.update"); 
	if ($form.length > 0) {
		var $coordinator = $form.find("select[name=coordinator]");
		var $opinion_row = $form.find("textarea[name=opinion]").parents("div[class=row]");
		var $status_row = $form.find("label[for=state]").parents("div[class=row]");
		var $state = $form.find("input[name=state]");

		/*state.length == 0 -> nie możemy zmieniać statusu*/
		if ($state.length == 0)
			$opinion_row.hide();

		var cval = $coordinator.val();
		if (cval == '-proposal' || cval == '-rejected' ) {
			$status_row.hide();
			$opinion_row.hide();
		}
		

		$coordinator.change(function () {
			var cval = $coordinator.val();
			if (cval == '-proposal' || cval == '-rejected') {
				$status_row.hide();
				$opinion_row.hide();
			} else {
				$status_row.show();
				if ($form.find("input[name=state]:checked").val() == "1")
					$opinion_row.show();
			}
		});
		

		if ($form.find("input[name=state]:checked").val() == "0")
			$opinion_row.hide();

		$state.change(function() {
			$this = jQuery(this);
			if ($this.val() == "0")
				$opinion_row.hide();
			else
				$opinion_row.show();
		});
	}

	//show/hide reason
	$reason_row = jQuery("#bez_tasks textarea[name=reason]").parents("div[class=row]");
	
	if ($reason_row.length > 0) {
		$select = jQuery("#bez_tasks select[name=state]");

		if ($select.val() == "0" || $select.val() == "1")
			$reason_row.hide();

		$select.change(function() {
			if (jQuery(this).val() == "0" || jQuery(this).val() == "1")
				$reason_row.hide();
			else
				$reason_row.show();
		});
	}

	var show = function() {
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
			if (hash.indexOf("k") === 0) {
				var showed = "bez_comments";
			} else if (hash.indexOf("p") === 0) {
				var showed = "bez_causes";
			} else if (hash.indexOf("z") === 0) {
				var showed = "bez_tasks";
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

	//entities sort
	jQuery("#entities_form input[type=button]").click(function() {
		var textarea = jQuery(this).parents("form").find("textarea");
		var lines = jQuery.trim(textarea.val()).split("\n");
		lines.sort();
		textarea.val(lines.join("\n"));
	});

});
