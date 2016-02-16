<?php include "issue_box.php" ?>

<div class="bds_block" id="bez_tasks">
	<h1><?php echo $bezlang['correction_nav'] ?> <span>(<?php echo count($template['tasks']) ?>)</span></h1>
	<div class="bds_block_content">
		<?php foreach ($template['tasks'] as $task): ?>
			<?php include "task.php" ?>
		<?php endforeach ?>
	</div>
	<?php if ($helper->user_coordinator($template['issue']['id'])): ?>
		<a href="?id=<?php echo $this->id('task_form', 'id', $template['issue']['id']) ?>">
			<?php echo $bezlang['correction_add'] ?>
		</a>
	<?php endif ?>
</div>
