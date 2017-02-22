bez.ctl.issues = function() {
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
};