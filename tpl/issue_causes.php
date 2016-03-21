<?php include "issue_box.php" ?>

<div class="bds_block" id="bez_causes">
	<h1><?php echo $bezlang['causes'] ?> <span>(<?php echo count($template['causes']) ?>)</span></h1>
	<div class="bds_block_content">
		<?php foreach ($template['causes'] as $cause): ?>
			<?php include "cause.php" ?>
		<?php endforeach ?>
	</div>
	<?php if ($template['issue']['raw_state'] == 0 &&
		 ($helper->user_coordinator($template['issue']['id']) || isset($nparams['tid']))): ?>
		<a href="?id=<?php echo $this->id('cause_form', 'id', $template['issue']['id']) ?>">
			<?php echo $bezlang['add_cause'] ?>
		</a>
	<? endif ?>
</div>

<?php include "removal_confirm.php";
