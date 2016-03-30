<?php include "issue_box.php" ?>

<div class="bds_block" id="bez_causes">
	<h1><?php echo $bezlang['causes'] ?> <span>(<?php echo count($template['causes']) ?>)</span></h1>
	<div class="bds_block_content">
		<?php foreach ($template['causes'] as $cause): ?>
			<?php include "cause.php" ?>
		<?php endforeach ?>
	</div>

