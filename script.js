var bez = {};
bez.ctl = {};

/* DOKUWIKI:include scripts/issue.js */
/* DOKUWIKI:include scripts/issue_report.js */
/* DOKUWIKI:include scripts/issues.js */
/* DOKUWIKI:include scripts/task_form.js */
/* DOKUWIKI:include scripts/task.js */
/* DOKUWIKI:include scripts/tasks.js */


jQuery(function () {
    'use strict';
	var getUrlVars = function() {
		var vars = {},
			parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,
			function(m,key,value) {
				vars[key] = value;
		});
		return vars;
	};
	var getNparams = function(value) {
		var nparams = [],
			params = value.split(':');
		for (let i = 0; i < params.length; i += 2) {
			let k = params[i],
				v = params[i+1];
			nparams[k] = v;
		}
		return nparams;
	}
	
	bez.rich_text_editor = function($textarea, $header) {
		//clone
		var tb = toolbar.filter(function (button) {
			if (button.type === 'autohead' ||
				button.class === 'pk_hl' ||
				button.icon === 'sig.png' ||
				button.icon === 'strike.png') {
				return false;
			}
			return true;
		});
		initToolbar($header, $textarea.attr('id'), tb);
	};
	
	jQuery.validate({
		lang: 'pl'
	});
	
	var nparams = getNparams(getUrlVars()['id']),
		ctl = nparams['bez'];
	
	if (typeof bez.ctl[ctl] === 'function') {
		bez.ctl[ctl].call(ctl);
	}
});
