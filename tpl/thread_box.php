<?php /* @var \dokuwiki\plugin\bez\meta\Tpl $tpl */ ?>
<div    data-type="<?php echo $tpl->get('thread')->type ?>"
        class="bez_thread
        <?php
        if ($tpl->get('thread')->state == 'opened') {
            echo 'priority_' . $tpl->get('thread')->priority;
        }
        ?>">

<h1 class="thread_box_header">

<a href="<?php echo $tpl->url('thread', 'id', $tpl->get('thread')->id) ?>">
    #<?php echo $tpl->get('thread')->id ?>
</a>

<?php if ($tpl->get('thread')->type == 'project'): ?>
    <?php echo $tpl->getLang('project') ?>
<?php elseif (!empty($tpl->get('thread')->label_name)): ?>
	<?php echo $tpl->get('thread')->label_name ?>
<?php else: ?>
	<i style="color: #777"><?php echo $tpl->getLang('issue_type_no_specified') ?></i>
<?php endif ?>

(<?php echo $tpl->getLang('state_' . $tpl->get('thread')->state) ?>)

<?php if ($tpl->get('thread')->private == '1'): ?>
    <?php echo inlineSVG(DOKU_PLUGIN . 'bez/images/lock.svg') ?>
<?php endif ?>
</h1>

<h1 class="thread_header"><?php echo $tpl->get('thread')->title ?></h1>

<div class="timebox">
    <span>
        <strong><?php echo $tpl->getLang('open') ?>:</strong>
        <?php echo $tpl->date($tpl->get('thread')->create_date) ?>
    </span>


<?php if ($tpl->get('thread')->state == 'closed' || $tpl->get('thread')->state == 'rejected'): ?>
    <span>
        <strong><?php echo $tpl->getLang('closed') ?>:</strong>
        <?php echo $tpl->date($tpl->get('thread')->close_date) ?>
    </span>
    
	<span>
		<strong><?php echo $tpl->getLang('report_priority') ?>: </strong>
        <?php echo $tpl->date_diff_days($tpl->get('thread')->create_date, $tpl->get('thread')->close_date, '%a') ?>
	</span>
<?php endif ?>
</div>

<table class="data">
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
<?php if (!$tpl->get('no_actions')): ?>
    <div class="bez_buttons">
        <?php if (count($tpl->get('thread')->changable_fields(array('label_id', 'title', 'content', 'coordinator'))) > 0): ?>
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

        <?php if ($tpl->get('thread')->type == 'issue'): ?>
            <a href="<?php echo $tpl->url('8d', 'id', $tpl->get('thread')->id) ?>" class="bds_inline_button bds_report_button">
                ⎙ <?php echo $tpl->getLang('8d_report') ?>
            </a>
        <?php else: ?>
            <a href="<?php echo $tpl->url('kp', 'id', $tpl->get('thread')->id) ?>" class="bds_inline_button bds_report_button">
                ⎙ <?php echo $tpl->getLang('kp_report') ?>
            </a>
        <?php endif ?>
    </div>
<?php endif?>

</div>

