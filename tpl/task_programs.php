<?php /* @var \dokuwiki\plugin\bez\meta\Tpl $tpl */ ?>
<table>
    <tr>
        <th>Name</th>
        <th colspan="3">Count</th>
    </tr>
    <?php foreach ($tpl->get('task_programs') as $task_program): ?>
        <tr>
            <?php if (  in_array($tpl->param('action'), array('edit', 'update')) &&
                $tpl->param('id') == $task_program->id): ?>
                <form action="
        <?php echo $tpl->url('types', 'action', 'update', 'id', $task_program->id) ?>"
                      method="POST">
                    <td>
                        <input name="name" value="<?php echo $tpl->value('name') ?>" />
                    </td>
                    <td colspan="2">
                        <input type="submit"
                               value="<?php echo $tpl->getLang('save') ?>" />
                        <a href="<?php echo $tpl->url('types') ?>">
                            <?php echo $tpl->getLang('cancel') ?>
                        </a>
                    </td>
                </form>
            <?php else: ?>
                <td><?php echo $task_program->name ?></td>
                <td <?php if ($task_program->count > 0) echo 'colspan="2"'; ?>>
                    <a href="<?php echo $tpl->url('types', 'action', 'edit', 'id', $task_program->id) ?>">
                        <?php echo $tpl->getLang('edit') ?>
                    </a>
                </td>
                <?php if ($task_program->count == 0): ?>
                    <td>
                        <a onclick="return confirm('<?php echo $tpl->getLang('js')['remove_confirm'] ?>')"
                           href="<?php echo $tpl->url('types', 'action', 'remove', 'id', $task_program->id) ?>">
                            <?php echo $tpl->getLang('delete') ?>
                        </a>
                    </td>
                <?php endif ?>
            <?php endif ?>

            <td>
                <a href="<?php echo $tpl->url('tasks', 'task_program', $task_program->id) ?>">
                    <?php echo $task_program->count ?>
                </a>
            </td>
        </tr>
    <?php endforeach ?>
    <?php if (  $tpl->param('action') !== 'edit' &&
    $tpl->param('action') != 'update'): ?>
    <form action="<?php echo $tpl->url('task_programs', 'action', 'add') ?>" method="POST">
        <tr>
            <td><input name="name" value="<?php echo $tpl->value('name') ?>" /></td>
            <td colspan="3"><input type="submit" value="<?php echo $tpl->getLang('save') ?>" /></td>
        </tr>
        <?php endif ?>
    </form>
</table>
