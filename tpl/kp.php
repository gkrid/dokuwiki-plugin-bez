<?php /* @var \dokuwiki\plugin\bez\meta\Tpl $tpl */ ?>
<h1>
    <?php echo $tpl->getLang('kp_report') ?>
    <span id="bez_8d_send_button">[<a href="
		<?php echo $tpl->mailto('',
                                $tpl->getLang('kp_report').': #'.$tpl->get('thread')->id.' '.$tpl->get('thread')->title,
                                $tpl->url()) ?>">
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
            <?php echo $tpl->getLang('project') ?>
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
<h2><?php echo $tpl->getLang('kp_team') ?></h2>
<ul>
    <?php foreach($tpl->get('thread')->get_participants() as $participant): ?>
        <li><?php echo $tpl->user_name($participant['user_id']) ?></li>
    <?php endforeach ?>
</ul>

<h2><?php echo $tpl->getLang('kp_description') ?></h2>
<?php echo $tpl->get('thread')->content_html ?>

<?php if (count($tpl->get('tasks')) > 0): ?>
    <h2><?php echo $tpl->getLang('kp_schedule') ?></h2>
    <?php include '8d_tasks.php' ?>
<?php endif ?>

<?php if ($tpl->get('thread')->state == 'closed'): ?>
    <h2><?php echo $tpl->getLang('kp_evaluation') ?></h2>
    <?php echo  $tpl->get('thread')->closing_comment() ?>
    <table>
        <tr>
            <td>
                <strong><?php echo $tpl->getLang('true_date') ?>:</strong>
                <?php echo $tpl->date($tpl->get('thread')->close_date) ?>
            </td>
            <td>
                <strong><?php echo $tpl->getLang('state') ?>:</strong>
                <?php echo $tpl->getLang('state_' . $tpl->get('thread')->state) ?>
            </td>
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
<?php endif ?>


