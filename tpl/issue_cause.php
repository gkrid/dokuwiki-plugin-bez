<?php include "issue_box.php" ?>
<div class="bds_block" id="bez_causes">
	<br>
	<?php $cause = $template['cause']; ?>
	<?php include "cause.php" ?>
</div>
<br>
<div class="bds_block" id="bez_tasks">
	<div class="bds_block_content">
		<?php foreach ($template[tasks] as $task): ?>
			<?php include "task.php" ?>
		<?php endforeach ?>
	</div>
</div>