<div id="bez_version">
	<div>
		<?php echo $bezlang['bez'] ?>, <?php echo $bezlang['version'] ?>: <?php echo $template['version'] ?>
	</div>
</div>
<div id="bez_info">
	<?php if ($this->model->acl->get_level() >= BEZ_AUTH_USER): ?>
		<a href="?id=<?php echo $this->id('issue_report') ?>" class="bez_start_button" id="bez_report_issue_button">
			<?php echo $bezlang['report_issue'] ?>
		</a>
	<?php endif ?>
	<a href="?id=<?php echo $this->id('issues:state:0:coordinator:'.$this->model->user_nick) ?>" class="bez_start_button" id="bez_info_issues">
		<?php echo $bezlang['close_issues'] ?>:
		<strong><?php echo $template['my_issues'] ?></strong>
	</a>
	<a href="?id=<?php echo $this->id('tasks:taskstate:0:executor:'.$this->model->user_nick) ?>" class="bez_start_button" id="bez_info_tasks">
		<?php echo $bezlang['close_tasks'] ?>:
		<strong><?php echo $template['my_tasks'] ?></strong>
	</a>
	<?php if ($this->model->acl->get_level() >= BEZ_AUTH_LEADER): ?>
		<a href="?id=<?php echo $this->id('issues:state:-proposal') ?>" class="bez_start_button" id="bez_info_proposals">
			<?php echo $bezlang['proposals'] ?>:
			<strong><?php echo $template['proposals'] ?></strong>
		</a>
	<?php endif ?>
</div>

<dl id="bds_timeline">
<?php $prev_date = '0000-00-00' ?>
<?php foreach ($template['timeline'] as $elm): ?>
    <?php if ($elm->date !== $prev_date): ?>
        <h2>	
            <?php echo $elm->date ?>
            <?php if ($elm->date === date('Y-m-d')): ?>
                <?php echo ': '.$bezlang['today'] ?>
            <?php elseif ($elm->date === date('Y-m-d', strtotime('yesterday'))): ?>
                <?php echo ': '.$bezlang['yesterday'] ?>
            <?php endif ?>
        </h2>
        <?php $prev_date = $elm->date ?>
    <?php endif ?>
    <?php
        if ($elm->author === '-proposal') {
           $author_full_name = '<i>' . $bezlang['none'] . '</i>'; 
        } else {
            $author_full_name = $this->model->users->get_user_full_name($elm->author);
        }
        
        if ($elm->class === 'issue_created') {
            $href = $this->id('issue', 'id', $elm->issue);
            $title = '<strong>#' . $elm->issue .  '</strong> ' . 
                    $bezlang['issue_added'] . ' ' .
                    '"' . $elm->title . '"' . 
                    ' <span class="author">' . $bezlang['coordinator'] . ':' . 
                    ' <strong>' . $author_full_name . '</strong></span>';
            $coord_string = $bezlang['coordinator'];
        } else if($elm->class === 'task_opened') {
            $href = $this->id('task', 'tid', $elm->id);
            
            $issue = '';
            if ($elm->issue !== '') {
                $issue = '#' . $elm->issue. ' ';
            }
            $title = '<strong>'.$issue.'#z' . $elm->id .  '</strong> ' . 
                    $bezlang['task_added'] . 
                    ' <span class="author">' . $bezlang['executor'] . ':' . 
                    ' <strong>' . $author_full_name . '</strong></span>';
            $coord_string = $bezlang['executor'];
        } else if($elm->class === 'cause') {
            $href = $this->id('issue', 'id', $elm->issue) . '#k'  . $elm->id;
            $title = $bezlang['timeline_cause_added'] . ' ' .
                    ' <span class="author">' . $bezlang['by'] . 
                    ' <strong>' . $author_full_name . '</strong></span>';
            $coord_string = $bezlang['reporter'];
        } else {
            $href = $this->id('issue', 'id', $elm->issue) . '#k'  . $elm->id;
            $title = $bezlang['timeline_comment_added'] . ' ' .
                    ' <span class="author">' . $bezlang['by'] . 
                    ' <strong>' . $author_full_name . '</strong></span>';
            $coord_string = $bezlang['reporter'];
        }
    ?>
    <dt class="<?php echo $elm->class ?>">
        <a href="?id=<?php echo $href ?>">
           <span class="time"><?php echo $elm->time ?></span>
           <?php echo $title ?>
        </a>
    </dt>
    <dd>
        <?php echo $elm->desc ?>
    </dd>
<?php endforeach ?>
</dl>
