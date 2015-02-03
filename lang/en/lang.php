<?php
$lang['bez'] = "Risk Elimination Base";
$lang['bez_short'] = "BEZ";
$lang['bds_timeline'] = "History";
$lang['bds_issues'] = "Registers";
$lang['bds_issue_report'] = "Report a problem";
$lang['bds_reports'] = "Reports";

$lang['issues'] = "Problems";
$lang['tasks'] = "Tasks";
$lang['reports'] = 'Registers';

$lang['report_issue'] = "Report a problem";
$lang['id'] = "No";
$lang['_id'] = "No";
$lang['type'] = "Type of problem";
$lang['title'] = "Title";
$lang['state'] = "Status";
$lang['reporter'] = "Person reporting the problem";
$lang['executor'] = "Performer";
$lang['coordinator'] = "Coordinator";
$lang['description'] = "Description";
$lang['date'] = "Reported";
$lang['last_mod_date'] = "last change";
$lang['opened_for'] = "Open since";
$lang['last_modified'] = "last change";
$lang['last_modified_by'] = "Last changed by";
$lang['opened_tasks'] = "Open tasks";

$lang['entity'] = "Source of report";
$lang['entities'] = "Sources of report (one per line)";

$lang['opinion'] = "Effectiveness evaluation";
$lang['root_cause'] = "Cause category";
$lang['causes'] = "Causes";

$lang['save'] = "Save";
$lang['proposal'] = "Proposal";
$lang['reported_by'] = "Reported by";
$lang['executor_not_specified'] = "Unspecified";
$lang['account_removed'] = "Removed account";
$lang['none'] = "none";

$lang['changes_history'] = "Change history";
$lang['add_comment'] = "Add comment";
$lang['add_task'] = "Add task";
$lang['change_issue'] = "Change the report of problem ";

$lang['changed'] = "It has been changed";
$lang['changed_field'] = "It has been changed";
$lang['by'] = "by";
$lang['from'] = "from";
$lang['to'] = "to";
$lang['diff'] = "Differences";
$lang['comment'] = "Add";
$lang['replay'] = "Reply";
$lang['edit'] = "Edit";
$lang['change_task_state'] = "Change a status of task";
$lang['replay_to'] = "Reply to";
$lang['quoted_in'] = "Replies";

$lang['error_issue_id_not_specifed'] = "You didn't provide the row number, you are trying to read. ";
$lang['error_issue_id_unknown'] = "Row you want to read does not exist.";
$lang['error_db_connection'] = "Cannot connect to the database.";
$lang['error_issue_insert'] = "The problem cannot be added. ";
$lang['error_task_add'] = "You cannot add tasks. Access denied.";
$lang['error_table_unknown'] = "The table dosn't exist.";
$lang['error_report_unknown'] = "The report dosn't exist";
$lang['error_issue_report'] = 'You cannot add new issues.';
$lang['error_entity'] = 'You cannot add new soruces of report.';
$lang['error_issues'] = 'You cannot view issues.';

$lang['vald_root_cause'] = "Select cause category.";

$lang['vald_type_required'] = "Specify a type of the problem";
$lang['vald_entity_required'] = "Choose a source of report from the list";
$lang['vald_title_required'] = "Provide a title.";
$lang['vald_title_too_long'] = "Title is too long, max: %d  characters.";
$lang['vald_title_wrong_chars'] = "Forbidden characters in the title. Only letters, numerals, space, dasches, poins, commas are allowed.";
$lang['vald_executor_required'] = "Choose the existing user or leave a problem unspecified.";
$lang['vald_coordinator_required'] = "Coordinator has to be wiki user.";

$lang['vald_desc_required'] = "Describe a problem";
$lang['vald_desc_too_long'] = "Too long description, max %d  characters.";
$lang['vald_opinion_too_long'] = "Effectiveness of evaluation is too long, max: %d characters.";
$lang['vald_opinion_required'] = 'Effectiveness of evaluation is required.';
$lang['vald_cannot_give_opinion'] = "You cannot evaluate effectiveness when a problem is still open.";
$lang['vald_cannot_give_reason'] = "You didn't change a status of the task.";

$lang['vald_content_required'] = "Insert a text";
$lang['vald_content_too_long'] = "Text is too long, max: %d";
$lang['vald_replay_to_not_exists'] = "Comment dosn't exist";
$lang['vald_state_required'] = "Specified a status of problem";
$lang['vald_state_tasks_not_closed'] = 'You cannot change issue state unless all tasks are closed.';

$lang['vald_task_state_required'] = "Specified a status of task";
$lang['vald_task_state_tasks_not_closed'] = "You cannot close the problem before closing all the tasks. Open tasks: %t";

$lang['vald_executor_not_exists'] = "Specified performer is nor a user of REB.";
$lang['vald_cost_too_big'] = "Cost is too high, max: %d";
$lang['vald_cost_wrong_format'] = "Cost should be integer.";
$lang['vald_action_required'] = "Provide an action of the task";

$lang['vald_days_should_be_numeric'] = "Days have to be decimal.";

$lang['vald_entity_too_long'] = 'Single source of report can have not have more than %d characters.';


$lang['type_complaint'] = "complaint";
$lang['type_noneconformity'] = "nonconformity";
$lang['type_risk'] = "risk";

$lang['state_proposal'] = "proposal";
$lang['state_opened'] = "open";
$lang['state_rejected'] = "rejected";
$lang['state_closed'] = "closed";


$lang['just_now'] = "minute before";
$lang['seconds'] = "seconds";
$lang['minutes'] = "minutes";
$lang['hours'] = "hours";
$lang['days'] = "days";
$lang['ago'] = "ago";

$lang['issue_closed_com'] = "Problem was closed %d, futhure changes are disabled.";
$lang['reopen_issue'] = "Change a status of the problem";
$lang['add'] = "Add";

$lang['action'] = "Action";

$lang['open'] = "Open";
$lang['closed'] = "Closed";

$lang['cost'] = "Cost (PLN)";
$lang['executor'] = "Performer";

$lang['task_state'] = "Status";
$lang['reason'] = "Rejection reason";

$lang['task_added'] = "Task has been added";
$lang['task_changed'] = "Task has been changed";
$lang['task_rejected_header'] = "Task has been rejected";
$lang['task_closed'] = "Task has been completed";
$lang['task_reopened'] = "Task has been reopend";
$lang['comment_added'] = "Comment has been added";
$lang['comment_changed'] = "Comment has been changed";

$lang['cause_added'] = "Cause added";
$lang['cause_noun'] = "Cause";
$lang['change_cause_button'] = "Correct the cause";


$lang['replay_by_task'] = "Add task in replay";
$lang['change_made'] = "Change has been made";

$lang['change_comment'] = "Correct the comment";
$lang['change_comment_button'] = "Correct the comment";
$lang['change_task'] = "Change the task";
$lang['change_task_button'] = "Change the task";

$lang['preview'] = "preview";
$lang['next'] = "next";

$lang['version'] = "Version";

$lang['comment_noun'] = "Comment";
$lang['change'] = "Change";
$lang['task'] = "Task";

$lang['change_state_button'] = "Change the status";


$lang['correction'] = "Correction";
$lang['corrective_action'] = "Corrective action";
$lang['preventive_action'] = "Preventive action";

$lang['none_comment'] = "none(comment)";
$lang['manpower'] = "Manpower";
$lang['method'] = "Method";
$lang['machine'] = "Machine";
$lang['material'] = "Material";
$lang['managment'] = "Management";
$lang['measurement'] = "Measurement";
$lang['money'] = "Money";
$lang['environment'] = "Environment";

$lang['task_opened'] = "Open";
$lang ['task_done'] = "Done";
$lang ['task_rejected'] = "Rejected";

$lang['reason_reopen'] = "Reason for reopening ";
$lang['reason_done']  = "Reason for closing";
$lang['reason_reject'] = "Reason for rejecting";

$lang['issue_created'] = "Reported";

$lang['issue_closed'] = "Problem has been closed";
$lang['issue_reopened'] = "Problem has been reopened";

$lang['today'] = "Today";
$lang['yesterday'] = "Yesterday";

$lang['task_for'] = "for";
$lang['content'] = "Description";

$lang['8d_report'] = "8d Report";
$lang['8d_report_for'] = "for";
$lang['open_date'] = "Open date";
$lang['2d'] = "1D – Team";
$lang['2d'] = "2D – Problem";
$lang['3d'] = "3D – Cause";
$lang['4d'] = "4D - Correction";
$lang['5d'] = "5D - Corrective action";
$lang['6d'] = "6D - Preventive action";
$lang['7d'] = "7D - Effectiveness evaluation";
$lang['8d'] = "8D - Conclusion";

$lang['cost_total'] = "Total cost (PLN)";
$lang['number'] = 'Quantity';
$lang['true_date'] = "Date";

$lang['newest_to_oldest'] = "Open problems from latest to earliest";
$lang['issues_newest_to_oldest'] = "";
$lang['tasks_newest_to_oldest'] = "Open tasks from latest to earliest";
$lang['tasks_newest_than_rep'] = "Tasks for %d";

$lang['newest_than'] = "All for";
$lang['newest_than_cost'] = "Cost statement for";
$lang['issues_newest_than'] = "Problems for ";
$lang['issues_cost_statement'] = 'Cost statement for %d';

$lang['my_opened_issues'] = "Open problems which I am involved";
$lang['my_opened'] = "Open, which I am involved";

$lang['me_executor'] = "My";
$lang['task_me_executor'] = "My tasks";

$lang['issues_newest_than'] = "All for";
$lang['issues_newest_than_rep'] = "Problems for %d";
$lang['newest_than_cost_rep'] = "Cost statement for %d";

$lang['report_issues'] = 'Register of problems for';
$lang['report_issues_from'] = 'Register of problems for %d';
$lang['report_tasks'] = 'Register of tasks for';
$lang['report_tasks_from'] = 'Register of tasks for %d';
$lang['report_causes'] = 'Register of causes for';
$lang['report_causes_from'] = 'Register of causes for %d';

$lang['show'] = "show";

$lang['by_last_activity'] = "Open, from latest to earliest";
$lang['issues_by_last_activity'] = "Open problems, from latest to earliest";

$lang['ns'] = "NA";

$lang['ended'] = "Completed";

//Mail
$lang['new_task'] = "New task";
$lang['new_issue'] = "New issue";
$lang['send_mail'] = "Send";

$lang['average_days'] = "Average time of task closing(days)";

$lang['bds_switch_lang'] = "Wersja polska";

$lang['comments'] = 'Comments';

$lang['delete'] = 'Delete';
$lang['info_no_causes_added'] = 'You have to add cause in order to add corrective and preventive actions.';
$lang['info_no_all_tasks_closed'] = 'You have to close all tasks before closing the problem.';

$lang['entity_manage'] = 'Sources of report';

$lang['sort'] = 'Sort';
$lang['cancel'] = 'Cancel';

$lang['menu_close_task'] = 'My tasks (%d)';
$lang['menu_close_issue'] = 'My issues(%d)';
$lang['menu_comment_issue'] = 'Opened issues (%d)';

$lang['do_you_want_remove'] = 'Do you really want to remove this row?';
$lang['yes'] = 'Yes';
$lang['no'] = 'No';
$lang['year'] = 'Year';
$lang['filter'] = 'Filter';

$lang['close_issues'] = 'My issues';
$lang['close_tasks'] = 'My tasks';

$lang['proposals'] = 'Proposals';
