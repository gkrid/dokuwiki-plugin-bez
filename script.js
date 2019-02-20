var bez = {};
bez.ctl = {};

/* DOKUWIKI:include scripts/thread.js */
/* DOKUWIKI:include scripts/thread_report.js */
/* DOKUWIKI:include scripts/threads.js */
/* DOKUWIKI:include scripts/task_form.js */
/* DOKUWIKI:include scripts/task.js */
/* DOKUWIKI:include scripts/tasks.js */
/* DOKUWIKI:include scripts/projects.js */
/* DOKUWIKI:include scripts/activity_report.js */
/* DOKUWIKI:include scripts/start.js */
/* DOKUWIKI:include scripts/report.js */



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
		//lang
		if (params[0] !== 'bez') {
			params.shift();
		}
		for (var i = 0; i < params.length; i += 2) {
            var k = params[i],
				v = params[i+1];
			nparams[k] = v;
		}
		return nparams;
	};
	
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
        form: '.bez_form, .bez_form_blank',
		lang: 'pl'
	});



	var urlParams = getUrlVars();

    //userewrite == '0'
	if ('id' in urlParams) {
		var id = urlParams['id'];
	//userewrite == '1', '2'
	} else {
		var found = window.location.href.match(/([^?\/]*)\??[^?\/]*$/);
		var id = found[1];
	}
	
	var nparams = getNparams(id),
		ctl = nparams['bez'];
    
	if (typeof bez.ctl[ctl] === 'function') {
		bez.ctl[ctl].call(ctl);
	}
});
