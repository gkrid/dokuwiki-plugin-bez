<div id="bez_task_context">
	<?php include "issue_box.php" ?>
	<?php if (isset($template['cause'])): ?>
		<?php $cause = $template['cause'] ?>
		<br>
		<div class="bds_block" id="bez_causes">
			<?php include "cause.php" ?>
		</div>
	<?php endif ?>
</div>
						
<a href="#" id="bez_task_context_show_button"><?php echo $bezlang['show_issue'] ?></a>

<div class="bds_block" id="bez_tasks">
	<br>
	<?php $task = $template['task'] ?>
	<?php include "task.php" ?>
</div>

<?php include "removal_confirm.php" ?>
