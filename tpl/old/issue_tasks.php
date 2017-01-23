<?php include "issue_box.php" ?>

<div class="bds_block" id="bez_tasks">
	<h1><?php echo $bezlang['correction_nav'] ?> <span>(<?php echo count($template['tasks']) ?>)</span></h1>
	<div class="bds_block_content">
		<?php foreach ($template['tasks'] as $task): ?>
				<a href="?id=<?php echo $this->id('show_task', 'tid', $task['id']) ?>">
					#z<?php echo $task['id'] ?>
				</a>
		<?php endforeach ?>
	</div>
	<?php if ($template['issue']['raw_state'] == 0 &&
		 ($helper->user_coordinator($template['issue']['id']) || isset($nparams['tid']))): ?>
		<a href="?id=<?php echo $this->id('task_form', 'id', $template['issue']['id']) ?>">
			<?php echo $bezlang['correction_add'] ?>
		</a>
	<?php endif ?>
</div>
