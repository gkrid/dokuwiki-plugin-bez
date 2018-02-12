bez.ctl.start = function() {
    "use strict";
    var active = 0;
    var $active = jQuery('#plugin__bez_start_tabs li span.count').filter(function() {
        return jQuery(this).text() != '0';
    });

    if ($active.length > 0) {
        active = $active.first().parents('li').index();
    }
    jQuery("#plugin__bez_start_tabs").tabs({active: active});

    //bez_show_desc
    jQuery(".bez_desc_row").hide();
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

};