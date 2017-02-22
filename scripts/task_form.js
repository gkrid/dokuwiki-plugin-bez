bez.ctl.task_form = function() {
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
    bez.rich_text_editor($task_form.find('textarea'), $task_form.find('.bez_toolbar'));
};