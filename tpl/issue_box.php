<div id="bds_issue_box" class="pr<?php echo $template['issue']['priority'] ?>">
<h1>
<?php echo $this->html_issue_link($template['issue']['id']) ?>
<?php echo $template['issue']['type'] ?> (<?php echo $template['issue']['state'] ?>)
</h1>

<h1><?php echo $template['issue']['title'] ?></h1>

<div class="bez_timebox">
<span><strong><?php echo $bezlang['open'] ?>:</strong> <?php echo $helper->time2date($template['issue']['date']) ?></span>
<?php if ($template['issue']['raw_state'] == 1): ?>
	<span>
		<strong><?php echo $bezlang['closed'] ?>: </strong>
		<?php echo $helper->time2date($template['issue']['last_mod']) ?>
	</span>
<?php endif ?>
</div>

<table>
<tr>
<td>
	<strong><?php echo $bezlang['coordinator'] ?>:</strong>
	<?php echo $template['issue']['coordinator'] ?>
</td>
</tr>
</table>

<?php echo $template['issue']['description'] ?>

<?php if ($template['issue']['raw_state'] == 1 || $template['issue']['raw_state'] == 2): ?>
	<h2><?php echo $bezlang['opinion'] ?></h2>
	<?php echo $template['issue']['opinion'] ?>
<?php endif ?>
<div class="bez_buttons">
	<?php if ($helper->user_coordinator($template['issue']['id'])): ?> 
		<a href="?id=<?php echo $this->id('issue_report', 'id', $template['issue']['id']) ?>" class="bds_inline_button">
		 	✎ <?php echo $bezlang['edit'] ?>
		</a>
	<?php endif ?>

	<a class="bds_inline_button" href="
		<?php echo $helper->mailto($template['issue']['coordinator_email'],
		$bezlang['issue'].': #'.$template['issue']['id'].' '.$template['issue']['title'],
		$template['uri']) ?>">
		✉ <?php echo $bezlang['send_mail'] ?>
	</a>

	<a href="<?php echo $helper->link_8d($template[issue][id]) ?>" class="bds_inline_button bds_report_button">
		⎙ <?php echo $bezlang['8d_report'] ?>
	</a>

	<a href="<?php echo $helper->link_rr($template[issue][id]) ?>" class="bds_inline_button bds_report_button">
		⎚ <?php echo $bezlang['rr_report'] ?>
	</a>
</div>

</div>
