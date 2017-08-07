<div id="bez_version">
	<div>
		<?php echo $tpl->getLang('bez') ?>, <?php echo $tpl->getLang('version') ?>: <?php echo $tpl->get('version') ?>
	</div>
</div>
<div id="bez_info">
	<?php if ($tpl->get_dummy_of('issues')->acl_of('id') >= BEZ_PERMISSION_CHANGE): ?>
		<a href="<?php echo $tpl->url('issue_report') ?>" class="bez_start_button" id="bez_report_issue_button">
			<?php echo $tpl->getLang('report_issue') ?>
		</a>
	<?php endif ?>
	<a href="<?php echo $tpl->url('issues', 'state', '0', 'coordinator', $tpl->get('client')) ?>" class="bez_start_button" id="bez_info_issues">
		<?php echo $tpl->getLang('close_issues') ?>:
		<strong><?php echo $tpl->get('my_issues_conut') ?></strong>
	</a>
	<a href="<?php echo $tpl->url('tasks', 'taskstate', '0', 'executor', $tpl->get('client')) ?>" class="bez_start_button" id="bez_info_tasks">
		<?php echo $tpl->getLang('close_tasks') ?>:
		<strong><?php echo $tpl->get('my_tasks_conut') ?></strong>
	</a>
	<?php if ($tpl->get_dummy_of('issues')->acl_of('coordinator') >= BEZ_PERMISSION_CHANGE): ?>
		<a href="<?php echo $tpl->url('issues', 'state', '-proposal') ?>" class="bez_start_button" id="bez_info_proposals">
			<?php echo $tpl->getLang('proposals') ?>:
			<strong><?php echo $tpl->get('proposals_count') ?></strong>
		</a>
	<?php endif ?>
</div>

<dl id="bds_timeline">
<?php $prev_date = '0000-00-00' ?>
<?php foreach ($tpl->get('timeline') as $elm): ?>
    <?php if ($elm->date !== $prev_date): ?>
        <h2>	
            <?php echo $elm->date ?>
            <?php if ($elm->date === date('Y-m-d')): ?>
                <?php echo ': '.$tpl->getLang('today') ?>
            <?php elseif ($elm->date === date('Y-m-d', strtotime('yesterday'))): ?>
                <?php echo ': '.$tpl->getLang('yesterday') ?>
            <?php endif ?>
        </h2>
        <?php $prev_date = $elm->date ?>
    <?php endif ?>
    <?php
        if ($elm->author === '-proposal') {
           $author_full_name = '<i>' . $tpl->getLang('none') . '</i>'; 
        } else {
            $author_full_name = $tpl->user_name($elm->author);
        }
        
        if ($elm->class === 'issue_created') {
            $href = $tpl->url('issue', 'id', $elm->issue);
            $title = '<strong>#' . $elm->issue .  '</strong> ' . 
                    $tpl->getLang('issue_added') . ' ' .
                    '"' . $elm->title . '"' . 
                    ' <span class="author">' . $tpl->getLang('coordinator') . ':' . 
                    ' <strong>' . $author_full_name . '</strong></span>';
            $coord_string = $tpl->getLang('coordinator');
        } else if($elm->class === 'task_opened') {
            $href = $tpl->url('task', 'tid', $elm->id);
            
            $issue = '';
            if ($elm->issue !== '') {
                $issue = '#' . $elm->issue. ' ';
            }
            $title = '<strong>'.$issue.'#z' . $elm->id .  '</strong> ' . 
                    $tpl->getLang('task_added') . 
                    ' <span class="author">' . $tpl->getLang('executor') . ':' . 
                    ' <strong>' . $author_full_name . '</strong></span>';
            $coord_string = $tpl->getLang('executor');
        } else if($elm->class === 'cause') {
            $href = $tpl->url('issue', 'id', $elm->issue) . '#k'  . $elm->id;
            $title = $tpl->getLang('timeline_cause_added') . ' ' .
                    ' <span class="author">' . $tpl->getLang('by') . 
                    ' <strong>' . $author_full_name . '</strong></span>';
            $coord_string = $tpl->getLang('reporter');
        } else {
            $href = $tpl->url('issue', 'id', $elm->issue) . '#k'  . $elm->id;
            $title = $tpl->getLang('timeline_comment_added') . ' ' .
                    ' <span class="author">' . $tpl->getLang('by') . 
                    ' <strong>' . $author_full_name . '</strong></span>';
            $coord_string = $tpl->getLang('reporter');
        }
    ?>
    <dt class="<?php echo $elm->class ?>">
        <a href="<?php echo $href ?>">
           <span class="time"><?php echo $elm->time ?></span>
           <?php echo $title ?>
        </a>
    </dt>
    <dd>
        <?php echo $elm->desc ?>
    </dd>
<?php endforeach ?>
</dl>
