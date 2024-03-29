<?php /* @var \dokuwiki\plugin\bez\meta\Tpl $tpl */ ?>
<?php $D = 2 ?>
<?php if (count($tpl->get('8d_tasks')['correction']) > 0) $D++ ?>
<?php if (count($tpl->get('causes')) > 0) $D++ ?>
<?php if (count($tpl->get('8d_tasks')['corrective']) > 0) $D++ ?>
<?php $corrective_done = array_filter($tpl->get('8d_tasks')['corrective'], function ($task) {
    return $task->state == 'done';
}) ?>
<?php if (count($corrective_done) > 0) $D++ ?>
<?php if (count($tpl->get('8d_tasks')['preventive']) > 0) $D++ ?>
<?php if ($tpl->get('thread')->state == 'closed' || $tpl->get('thread')->state == 'rejected') $D++ ?>

<h1>
    <?php printf($tpl->getLang('8d_report_header'), $D); ?>
	<span id="bez_8d_send_button">[<a href="
		<?php echo $tpl->mailto('',
   $tpl->getLang('8d_report').': #'.$tpl->get('thread')->id.' '.$tpl->get('thread')->title,
            $tpl->url('8d', 'id', $tpl->get('thread')->id) . '?t=' . $_GET['t']) ?>">
		✉ <?php echo $tpl->getLang('send_mail') ?>
	</a>]</span>
</h1>

<table>
<tr>
	<td>
		 <strong>
		 	<a href="<?php echo $tpl->url('thread', 'id', $tpl->get('thread')->id) ?>">
				#<?php echo  $tpl->get('thread')->id ?>
			</a>
		</strong>
        <?php if (!empty($tpl->get('thread')->label_name)): ?>
            <?php echo $tpl->get('thread')->label_name ?>
        <?php else: ?>
            <i style="color: #777"><?php echo $tpl->getLang('issue_type_no_specified') ?></i>
        <?php endif ?>
	</td>

	<td>
		<strong><?php echo $tpl->getLang('open_date') ?>:</strong>
        <?php echo $tpl->date($tpl->get('thread')->create_date) ?>
	</td>
</tr>

	<tr>
	<td colspan="2">
		<strong><?php echo $tpl->getLang('title') ?>:</strong>
		<?php echo  $tpl->get('thread')->title ?>
	</td>
</tr>
</table>
<?php $D = 1 ?>
<h2><?php echo $D++ ?>D - <?php echo $tpl->getLang('1d') ?></h2>
<ul>
	<?php foreach($tpl->get('thread')->get_participants() as $participant): ?>
		<li><?php echo $tpl->user_name($participant['user_id']) ?></li>
	<?php endforeach ?>
</ul>

<h2><?php echo $D++ ?>D - <?php echo $tpl->getLang('2d') ?></h2>
<?php echo $tpl->get('thread')->content_html ?>

<?php if (count($tpl->get('8d_tasks')['correction']) > 0): ?>
    <h2><?php echo $D++ ?>D - <?php echo $tpl->getLang('3d') ?></h2>
    <?php $tpl->set('tasks', $tpl->get('8d_tasks')['correction']) ?>
    <?php include '8d_tasks.php' ?>
<?php endif ?>

<?php if (count($tpl->get('causes')) > 0): ?>
    <h2><?php echo $D++ ?>D - <?php echo $tpl->getLang('4d') ?></h2>
    <?php $tpl->set('causes', $tpl->get('causes')) ?>
    <?php include '8d_causes.php' ?>
<?php endif ?>

<?php if (count($tpl->get('8d_tasks')['corrective']) > 0): ?>
    <h2><?php echo $D++ ?>D - <?php echo $tpl->getLang('5d') ?></h2>
    <?php $tpl->set('tasks', $tpl->get('8d_tasks')['corrective']) ?>
    <?php include '8d_tasks.php' ?>
<?php endif ?>

<?php if ($tpl->get('thread')->state == 'closed' || $tpl->get('thread')->state == 'rejected'): ?>
    <h2><?php echo $D++ ?>D - <?php echo $tpl->getLang('6d-var2') ?></h2>
    <?php echo  $tpl->get('thread')->closing_comment() ?>
<?php endif ?>

<?php if (count($tpl->get('8d_tasks')['preventive']) > 0): ?>
    <h2><?php echo $D++ ?>D - <?php echo $tpl->getLang('7d') ?></h2>
    <?php $preventive = array_merge($tpl->get('risks'), $tpl->get('opportunities')) ?>
    <?php usort($preventive, function ($a, $b) {
        return $a->id > $b->id;
    }); ?>
    <?php $tpl->set('causes', $preventive) ?>
    <?php include '8d_causes.php' ?>

    <?php $tpl->set('tasks', $tpl->get('8d_tasks')['preventive']) ?>
    <?php include '8d_tasks.php' ?>
<?php endif ?>


<h2><?php echo $D++ ?>D - <?php echo $tpl->getLang('8d') ?></h2>

<table>
    <tr>
        <td <?php if (count($tpl->get('8d_tasks')['preventive']) == 0) echo 'colspan="2"' ?>>
            <strong><?php echo $tpl->getLang('problem_close_date') ?>:</strong>
            <?php if ($tpl->get('thread')->state == 'closed' || $tpl->get('thread')->state == 'rejected'): ?>
                <?php echo $tpl->date($tpl->get('thread')->close_date) ?>
            <?php else: ?>
                ---
            <?php endif ?>
        </td>
        <?php if (count($tpl->get('8d_tasks')['preventive']) > 0): ?>
            <td>
                <strong><?php echo $tpl->getLang('preventive_close_date') ?>:</strong>
                <?php if ($tpl->get('preventive_close_date')): ?>
                    <?php echo $tpl->get('preventive_close_date') ?>
                <?php else: ?>
                    ---
                <?php endif ?>
            </td>
        <?php endif ?>
    </tr>

    <tr>
        <td>
            <strong><?php echo $tpl->getLang('totalcost') ?>:</strong>
            <?php if ($tpl->get('thread')->task_sum_cost != ''): ?>
                <?php echo $tpl->get('thread')->task_sum_cost ?>
            <?php else: ?>
                <em>---</em>
            <?php endif ?>
        </td>
        <td>
            <strong><?php echo $tpl->getLang('coordinator') ?>:</strong>
            <?php echo $tpl->user_name($tpl->get('thread')->coordinator) ?>
        </td>
    </tr>
</table>
