<table>
    <tr>
        <th><?php echo $tpl->getLang('id') ?></th>
        <th><?php echo $tpl->getLang('description') ?></th>
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
                <?php if($task->task_comment_content_html == ''): ?>
                    <em>---</em>
                <?php else: ?>
                    <?php echo $task->task_comment_content_html ?>
                <?php endif ?>
            </td>
            <td>
                <?php echo $tpl->date($task->close_date) ?>
            </td>
        </tr>
    <?php endforeach ?>
</table>
