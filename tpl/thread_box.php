<?php /* @var \dokuwiki\plugin\bez\meta\Tpl $tpl */ ?>
<div    id="bds_issue_box"
        class="pr<?php echo $tpl->get('thread')->priority ?>
        <?php if (  $template['action'] === 'issue_edit_metadata') echo 'bez_metadata_edit_warn' ?>">

<h1>

<a href="<?php echo $tpl->url('thread', 'id', $tpl->get('thread')->id) ?>">
    #<?php echo $tpl->get('thread')->id ?>
</a>

<?php if (!empty($tpl->get('thread')->label_name)): ?>
	<?php echo $tpl->get('thread')->label_name ?>
<?php else: ?>
	<i style="color: #777"><?php echo $tpl->getLang('issue_type_no_specified') ?></i>
<?php endif ?>

(<?php echo $tpl->getLang('state_' . $tpl->get('thread')->state) ?>)
</h1>

<h1 id="bez_issue_title"><?php echo $tpl->get('thread')->title ?></h1>

<div class="bez_timebox">
    <span>
        <strong><?php echo $tpl->getLang('open') ?>:</strong>
        <?php echo dformat(strtotime($tpl->get('thread')->create_date), '%Y-%m-%d') ?>
    </span>


<?php if ($tpl->get('thread')->state == 'closed' || $tpl->get('thread')->state == 'rejected'): ?>
    <span>
        <strong><?php echo $tpl->getLang('closed') ?>:</strong>
        <?php echo dformat(strtotime($tpl->get('thread')->close_date), '%Y-%m-%d') ?>
    </span>
    
	<span>
		<strong><?php echo $tpl->getLang('report_priority') ?>: </strong>
        <?php $dStart = new DateTime($tpl->get('thread')->create_date) ?>
        <?php $dEnd = new DateTime($tpl->get('thread')->close_date) ?>
 		<?php echo $dStart->diff($dEnd)->days ?> <?php echo $tpl->getLang('days') ?>
	</span>
<?php endif ?>
</div>

<table class="bez_box_data_table">
<tr>
    <th><?php echo $tpl->getLang('reporter') ?>:</th>
    <td>
        <?php echo $tpl->user_name($tpl->get('thread')->original_poster) ?>
    </td>
    
    <th><?php echo $tpl->getLang('coordinator') ?>:</th>
    <td>
        <?php if ($tpl->get('thread')->coordinator == ''): ?>
            <i style="font-weight: normal; color: #aaa"><?php echo $tpl->getLang('none') ?></i>
        <?php else: ?>
            <?php echo $tpl->user_name($tpl->get('thread')->coordinator) ?>
        <?php endif?>
    </td>
</tr>
</table>

<?php echo $tpl->get('thread')->content_html ?>

<?php if ($tpl->get('thread')->task_count - $tpl->get('thread')->task_count_closed > 0): ?>
    <div class="info"><?php echo $tpl->getLang('issue_unclosed_tasks') ?></div>
<?php endif ?>
<?php if ($tpl->get('thread')->state == 'proposal'): ?>
    <div class="info"><?php echo $tpl->getLang('issue_is_proposal') ?></div>
<?php endif ?>
<?php if ($tpl->get('thread')->causes_without_tasks_count() > 0): ?>
    <div class="info"><?php echo $tpl->getLang('cause_without_task') ?></div>
<?php endif ?>
<?php if ($tpl->get('thread')->state == 'open' && $tpl->get('thread')->task_count == 0): ?>
    <div class="info"><?php echo $tpl->getLang('issue_no_tasks') ?></div>
<?php endif ?>

<div class="bez_buttons">

	<?php if (count($tpl->get('thread')->changable_fields()) > 0): ?>
		<a href="<?php echo $tpl->url('thread_report', 'action', 'edit', 'id', $tpl->get('thread')->id) ?>" class="bds_inline_button">
		 	✎ <?php echo $tpl->getLang('edit') ?>
		</a>
	<?php endif ?>

	<a class="bds_inline_button" href="
		<?php echo $tpl->mailto($tpl->user_email($tpl->get('thread')->coordinator),
                                   '#'.$tpl->get('thread')->id.' '.$tpl->get('thread')->title,
		$tpl->url('thread', 'id', $tpl->get('thread')->id)) ?>">
		✉ <?php echo $tpl->getLang('send_mail') ?>
	</a>

	<a href="<?php echo $tpl->url('8d', $tpl->get('thread')->id) ?>" class="bds_inline_button bds_report_button">
		⎙ <?php echo $tpl->getLang('8d_report') ?>
	</a>
</div>

</div>

