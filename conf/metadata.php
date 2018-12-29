<?php
$meta['hidden_groups'] = array('string');

$meta['weekly_cron_day_of_the_week'] = array(
    'multichoice',
    '_choices' => array(
        'Sunday',
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        )
    );

$meta['task_remaind_days_before'] = array('string');
$meta['busy_timeout'] = array('numeric', '_min' => 0);