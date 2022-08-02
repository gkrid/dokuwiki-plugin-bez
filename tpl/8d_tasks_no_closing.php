<table>
<tr>
    <th><?php echo $tpl->getLang('id') ?></th>
    <th><?php echo $tpl->getLang('state') ?></th>
    <th><?php echo $tpl->getLang('description') ?></th>
    <th><?php echo $tpl->getLang('executor') ?></th>
    <th><?php echo $tpl->getLang('plan') ?></th>
    <th><?php echo $tpl->getLang('cost') ?></th>
</tr>
<?php foreach($tpl->get('tasks') as $task): ?>
    <tr>
        <td>
            <a href="<?php echo $tpl->url('task', 'tid', $task->id) ?>">
                #z<?php echo $task->id ?>
            </a>
        </td>
        <td><?php echo lcfirst($tpl->getLang('task_' . $task->state)) ?></td>
        <td>
            <?php echo $task->content_html ?>
        </td>
        <td><?php echo $tpl->user_name($task->assignee) ?></td>
        <td><?php echo $task->plan_date ?></td>
        <td>
            <?php if ($task->cost == ''): ?>
                <em>---</em>
            <?php else: ?>
                <?php echo $task->cost ?>
            <?php endif ?>
        </td>
    </tr>
<?php endforeach ?>
</table>
