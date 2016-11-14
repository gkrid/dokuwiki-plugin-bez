<?php
$lang['bez'] = "Risk Elimination Base";
$lang['bez_short'] = "BEZ";
$lang['bds_timeline'] = "History";
$lang['bds_issues'] = "Registers";
$lang['bds_issue_report'] = "Report a problem";
$lang['bds_task_report'] = "Add a task";
$lang['report'] = "Report";
$lang['report_open'] = 'Raport of opened';
$lang['bez_tasks'] = 'Tasks';

$lang['issues'] = "Problems";
$lang['tasks'] = "Tasks";
$lang['reports'] = 'Registers';

$lang['issue'] = 'Issue';
$lang['task'] = 'Task';

$lang['report_issue'] = "Report a problem";
$lang['id'] = "No";
$lang['_id'] = "No";
$lang['type'] = "Type of problem";
$lang['just_type'] = 'Type';
$lang['title'] = "Title";
$lang['state'] = "Status";
$lang['reporter'] = "Person reporting the problem";
$lang['executor'] = "Performer";
$lang['coordinator'] = "Coordinator";
$lang['description'] = "Description";
$lang['date'] = "Reported";
$lang['last_mod_date'] = "last change";
$lang['opened_for'] = "Open since";
$lang['last_modified'] = "Last change";
$lang['last_modified_by'] = "Last changed by";
$lang['opened_tasks'] = "Open tasks";

$lang['entity'] = "Source of report";
$lang['entities'] = "Sources of report (one per line)";
$lang['entities_confirm'] = 'New sources of report saved.';

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
$lang['error_no_permission'] = 'You do not have permission to view this page.';

$lang['vald_root_cause'] = "Select cause category.";

$lang['vald_type_required'] = "Specify a type of the problem";
$lang['vald_entity_required'] = "Choose a source of report from the list";
$lang['vald_title_required'] = "Provide a title.";
$lang['vald_title_too_long'] = "Title is too long, max: %d  characters.";
$lang['vald_title_wrong_chars'] = "Forbidden characters in the title. Only letters, numbers, spaces, dashes, floors, points and commas are allowed.";

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
$lang['vald_entity_no_ascii'] = 'Source of report contains non-ASCII charters.';


$lang['vald_type_wrong_chars'] = 'Type has to contain only numbers and letters.';
$lang['vald_type_required'] = 'You must provide type name in both languages.';

$lang['priority_marginal'] = 'marginal';
$lang['priority_important'] = 'important';
$lang['priority_crucial'] = 'crucial';

$lang['type_complaint'] = "complaint";
$lang['type_noneconformity'] = "nonconformity";
$lang['type_noneconformity_internal'] = 'internal noneconformity';
$lang['type_noneconformity_customer'] = 'customer noneconformity';
$lang['type_noneconformity_supplier'] = 'supplier noneconformity';
$lang['type_opportunity'] = 'opportunity';
$lang['type_threat'] = 'threat';

$lang['state_proposal'] = "proposal";
$lang['state_opened'] = "open";
$lang['state_done'] = "done";
$lang['state_rejected'] = "rejected";
$lang['state_closed'] = "closed";

$lang['reject_issue'] = 'Reject';
$lang['close_issue'] = 'Close';

$lang['just_now'] = "minute before";
$lang['seconds'] = "seconds";
$lang['minutes'] = "minutes";
$lang['hours'] = "hours";
$lang['days'] = "days";
$lang['ago'] = "ago";

$lang['issue_closed_com'] = "Problem was closed %d, futhure changes are disabled.";
$lang['reopen_issue'] = "Change a status of the problem";
$lang['add'] = "Add";

$lang['class'] = 'Task type';

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
$lang['added'] = 'Added';


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
$lang['correction_h'] = 'Correction';
$lang['corrective_action'] = "Corrective action";
$lang['preventive_action'] = "Preventive action";

$lang['correction_add'] = 'Add correction';
$lang['corrective_action_add'] = 'Add corrective action';
$lang['preventive_action_add'] = 'Add preventive action';

$lang['show_all_tasks'] = 'Show all tasks';

$lang['none_comment'] = "none(comment)";
$lang['manpower'] = "Manpower";
$lang['method'] = "Method";
$lang['machine'] = "Machine";
$lang['material'] = "Material";
$lang['managment'] = "Management";
$lang['measurement'] = "Measurement";
$lang['money'] = "Money";
$lang['environment'] = "Environment";
$lang['communication'] = "Communication";

$lang['task_opened'] = "Open";
$lang ['task_outdated'] = 'Outdated';
$lang ['task_done'] = "Done";
$lang ['task_plan'] = "Plan";
$lang ['task_rejected'] = "Rejected";

$lang ['task_do'] = 'Done';
$lang ['task_reject'] = 'Reject';
$lang ['task_reopen'] = 'Reopen';

$lang['reason_reopen'] = "Reason for reopening ";
$lang['reason_done']  = "Reason for closing";
$lang['reason_reject'] = "Reason for rejecting";

$lang['issue_created'] = "Reported";

$lang['issue_closed'] = "Problem has been closed";
$lang['issue_reopened'] = "Problem has been reopened";

$lang['issue_reopen'] = 'Reopen issue';

$lang['today'] = "Today";
$lang['yesterday'] = "Yesterday";

$lang['task_for'] = "for";
$lang['content'] = "Description";

$lang['8d_report'] = "8D Report";
$lang['8d_report_for'] = "for";
$lang['open_date'] = "Open date";
$lang['1d'] = "1D - Team";
$lang['2d'] = "2D - Problem";
$lang['3d'] = "3D - Correction";
$lang['4d'] = "4D - Cause";
$lang['5d'] = "5D - Corrective action";
$lang['6d'] = "6D - Preventive action";
$lang['7d'] = "7D - Effectiveness evaluation";
$lang['8d'] = "8D - Conclusion";

$lang['number'] = 'Quantity';
$lang['true_date'] = "Date";


$lang['report_issues'] = 'Issues';
$lang['report_tasks'] = 'Tasks';
$lang['report_causes'] = 'Causes';
$lang['report_priority'] = 'Raction time';
$lang['report_subhead'] = 'for closed issues and tasks';

$lang['priority'] = 'Priority';
$lang['average'] = 'Average';

$lang['totalcost'] = 'Value';
$lang['report_total'] = 'Total';

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

$lang['types_manage'] = 'Types of issues';

$lang['sort'] = 'Sort';
$lang['cancel'] = 'Cancel';

$lang['menu_close_task'] = 'My tasks (%d)';
$lang['menu_close_issue'] = 'My issues(%d)';
$lang['menu_comment_issue'] = 'Opened issues (%d)';

$lang['do_you_want_remove'] = 'Do you really want to remove this row?';
$lang['yes'] = 'Yes';
$lang['no'] = 'No';

$lang['all'] = 'everything';
$lang['year'] = 'Year';
$lang['in_year'] = 'in year';
$lang['filter'] = 'Filter';

$lang['close_issues'] = 'My issues';
$lang['close_tasks'] = 'My tasks';

$lang['proposals'] = 'Proposals';

$lang['show_tasks_hidden'] = 'Show closed and rejected tasks';
$lang['add_task'] = 'Add new task';

$lang['save_without_changing_date'] = 'Save without changing date';

$lang['sort_by_open_date'] = 'Sort by open date';

$lang['issue_is_proposal'] = 'You cannot add causes and tasks until this issue is porposal.';

$lang['casue_cant_remove'] = 'You cannot remove cause with related tasks.';

$lang['add_cause'] = 'Add cause';
$lang['add_correction'] = 'Add correction';

$lang['cause'] = 'cause';
$lang['pontential_cause'] = 'potential cause';

$lang['cause_type'] = 'Cause type';

$lang['type_default'] = 'normal';
$lang['type_potential'] = 'potential';

$lang['evaluation'] = 'Ocena';

$lang['rr_report'] = 'RR Report';

$lang['rr_team'] = 'Risk evaluation team';
$lang['rr_desc'] = 'Risk description';
$lang['rr_eval'] = 'Risk evaluacion';
$lang['rr_suceval'] = 'Effectiveness evalutation';

$lang['correction_nav'] = 'Corrections';
$lang['closed_tasks'] = 'Closed tasks';
$lang['root_causes'] = 'Cause categories';

$lang['version'] = 'version';

$lang['issues_juxtaposition'] = 'Juxtaposition of issues';
$lang['tasks_juxtaposition'] = 'Juxtaposition of tasks';

$lang['issue_unclosed_tasks'] = 'You cannot close issues until some tasks are not closed.';
$lang['issue_is_proposal'] = 'You cannot add causes and tasks until issue is proposal.';
$lang['issue_no_tasks'] = 'You cannot close the issue until it has no tasks.';
$lang['cause_without_task'] = 'You cannot close the issue if you have some causes without tasks.';

$lang['number_of_open'] = 'Number of open';
$lang['number_of_close'] = 'Number of closed';
$lang['number_of_close_on_time'] = 'Number of closed on time';
$lang['number_of_close_off_time'] = 'Number of closed off time';
$lang['diffirence'] = 'Diffirence';

$lang['cost_of_open'] = 'Cost of open';

$lang['average_of_close'] = 'Avarage close time';

$lang['plan_date'] = 'Plan date';
$lang['all_day_event'] = 'All day event';
$lang['start_time'] = 'Start time';
$lang['finish_time'] = 'Finish time';

$lang['vald_valid_date_required'] = 'Valid date required.';
$lang['vald_valid_start_hour_required'] = 'Valid start time required.';
$lang['vald_valid_finish_hour_required'] = 'Valid finish time required.';

$lang['download_in_icalendar'] = 'iCalendar';

$lang['task_types'] = 'Task types';
$lang['task_type'] = 'Typ zadań';

$lang['programme'] = 'Programme';

$lang['tasks_no_type'] = '--- without type ---';

$lang ['plan'] = "Plan";

$lang ['task_plan'] = "Plan";
$lang ['task_realization'] = "Realization";

$lang ['month'] = "Month";

$lang ['report_date'] = "Report date";
$lang ['close_date'] = "Close date";

$lang['jan'] = 'January';
$lang['feb'] = 'February';
$lang['mar'] = 'March';
$lang['apr'] = 'April';
$lang['may'] = 'May';
$lang['june'] = 'June';
$lang['july'] = 'July';
$lang['aug'] = 'August';
$lang['sept'] = 'September';
$lang['oct'] = 'October';
$lang['nov'] = 'November';
$lang['dec'] = 'December';

$lang['hours_no'] = 'Number of hours';

$lang['show_desc'] = 'Show descriptions';
$lang['show_desc_and_eval'] = 'Show descrpitions and evaluations';

$lang['hide_desc'] = 'Hide descripitons';
$lang['hide_desc_and_eval'] = 'Hide descripitons and evaluations';

$lang['users'] = 'users';
$lang['groups'] = 'groups';

$lang['duplicate'] = 'Duplicate';

$lang['show_issue'] = 'Show issue';

$lang['select'] = 'select';

$lang['not_relevant'] = 'not relevant';

$lang['validate_is_null'] = 'cannot be empty.';
$lang['validate_validate_iso_date'] = 'bad date format.';

$lang['comment_last_activity'] = 'Last activity';
$lang['comment_participants'] = 'Participants';

