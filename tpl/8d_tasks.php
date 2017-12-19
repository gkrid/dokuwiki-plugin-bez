<table>
<tr>
    <th><?php echo $tpl->getLang('id') ?></th>
    <th><?php echo $tpl->getLang('task') ?></th>
    <th><?php echo $tpl->getLang('state') ?></th>
    <th><?php echo $tpl->getLang('cost') ?></th>
    <th><?php echo $tpl->getLang('plan_date') ?></th>
    <th><?php echo $tpl->getLang('closed') ?></th>
</tr>
<?php foreach($tpl->get('tasks') as $task): ?>
    <tr>
        <td>
            <a href="<?php echo $tpl->url('task', 'tid', $task->id) ?>">
                #z<?php echo $task->id ?>
            </a>
        </td>
        <td>
            <?php echo $task->content_html ?>
        </td>
        <td><?php echo $tpl->getLang('task_' . $tpl->get('task')->state) ?></td>
        <td>
            <?php if ($task->cost == ''): ?>
                <em>---</em>
            <?php else: ?>
                <?php echo $task->cost ?>
            <?php endif ?>
        </td>
        <td><?php echo $task->plan_date ?></td>
        <td>
            <?php if ($task->state == 'opened'): ?>
                <em>---</em>
            <?php else: ?>
                <?php echo dformat(strtotime($task->close_date), '%Y-%m-%d') ?>
            <?php endif ?>
        </td>
    </tr>
<?php endforeach ?>
</table>
