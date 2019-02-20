<?php /* @var \dokuwiki\plugin\bez\meta\Tpl $tpl */ ?>
<div id="plugin__bez_start_buttons">
<?php if ($tpl->factory('thread')->permission() >= BEZ_TABLE_PERMISSION_INSERT): ?>
    <a href="<?php echo $tpl->url('thread_report', 'type', 'issue') ?>" class="bez_start_button" id="bez_report_issue_button">
        <?php echo $tpl->getLang('report_threads') ?>
    </a>
<?php endif ?>
<?php if ($tpl->factory('thread')->permission() >= BEZ_TABLE_PERMISSION_INSERT): ?>
    <a href="<?php echo $tpl->url('thread_report', 'type', 'project') ?>" class="bez_start_button" id="bez_report_issue_button">
        <?php echo $tpl->getLang('report_projects') ?>
    </a>
<?php endif ?>

<?php if ($tpl->factory('task')->permission() >= BEZ_TABLE_PERMISSION_INSERT): ?>
    <a href="<?php echo $tpl->url('task_form') ?>" class="bez_start_button" id="bez_report_task_button">
        <?php echo $tpl->getLang('add_task') ?>
    </a>
<?php endif ?>

<?php if ($tpl->factory('subscription')->isMuted()): ?>
    <a href="<?php echo $tpl->url('start', array('action' => 'unmute')) ?>" class="bez_start_button">
        <span class="bez_awesome">&#xf0f3;</span>
        <?php printf($tpl->getLang('unmute_notifications'), $tpl->user_email()) ?>
    </a>
<?php else: ?>
    <a href="<?php echo $tpl->url('start', array('action' => 'mute')) ?>" class="bez_start_button">
        <span class="bez_awesome">&#xf1f6;</span>
        <?php printf($tpl->getLang('mute_notifications'), $tpl->user_email()) ?>
    </a>
<?php endif ?>
</div>

<div id="plugin__bez_start_tabs">
    <ul>
        <li><a href="#plugin__bez_tabs-0">
                <?php echo $tpl->getLang('menu_activity') ?>
            </a></li>
        <?php if ($tpl->user_acl_level() >= BEZ_AUTH_ADMIN): ?>
        <li><a href="#plugin__bez_tabs-1">
                <?php echo $tpl->getLang('proposals') ?>
                (<span class="count"><?php echo $tpl->get('proposals_count') ?></span>)
            </a></li>
        <?php endif ?>
        <li><a href="#plugin__bez_tabs-2">
                <?php echo $tpl->getLang('close_issues') ?>
                (<span class="count"><?php echo $tpl->get('my_threads_count') ?></span>)
            </a></li>
        <li><a href="#plugin__bez_tabs-3">
                <?php echo $tpl->getLang('close_tasks') ?>
                (<span class="count"><?php echo $tpl->get('my_tasks_count') ?></span>)
            </a></li>
        <li><a href="#plugin__bez_tabs-4">
                <?php echo $tpl->getLang('my_reported_threads') ?>
                (<span class="count"><?php echo $tpl->get('reported_threads_count') ?></span>)
            </a></li>
        <li><a href="#plugin__bez_tabs-5">
                <?php echo $tpl->getLang('my_reported_tasks') ?>
                (<span class="count"><?php echo $tpl->get('reported_tasks_count') ?></span>)
            </a></li>
    </ul>
    <div id="plugin__bez_tabs-0">
        <?php include 'activity.php' ?>
    </div>
    <?php $tpl->set('no_filters', true) ?>
    <?php if ($tpl->user_acl_level() >= BEZ_AUTH_ADMIN): ?>
    <div id="plugin__bez_tabs-1">
        <?php $tpl->set('threads', $tpl->get('proposals')) ?>
        <?php include 'threads.php' ?>
    </div>
    <?php endif ?>
    <div id="plugin__bez_tabs-2">
        <?php $tpl->set('threads', $tpl->get('my_threads')) ?>
        <?php include 'threads.php' ?>
    </div>
    <div id="plugin__bez_tabs-3">
        <?php $tpl->set('tasks', $tpl->get('my_tasks')) ?>
        <?php include 'tasks.php' ?>
    </div>
    <div id="plugin__bez_tabs-4">
        <?php $tpl->set('threads', $tpl->get('reported_threads')) ?>
        <?php include 'threads.php' ?>
    </div>
    <div id="plugin__bez_tabs-5">
        <?php $tpl->set('tasks', $tpl->get('reported_tasks')) ?>
        <?php include 'tasks.php' ?>
    </div>
</div>