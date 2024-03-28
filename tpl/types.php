<?php /* @var \dokuwiki\plugin\bez\meta\Tpl $tpl */ ?>
<form action="<?php echo $tpl->url('types') ?>" method="POST">
<table>
<tr>
	<th>Name</th>
	<th colspan="3">Count</th>
</tr>
<?php foreach ($tpl->get('labels') as $label): ?>
<tr>
	<?php if (  in_array($tpl->param('action'), array('edit', 'update')) &&
                $tpl->param('id') == $label->id): ?>
            <td>
                <input type="hidden" name="id" value="<?php echo $label->id ?>" />
                <input name="name" value="<?php echo $tpl->value('name') ?>" />
            </td>
            <td colspan="2">
                <button name="action" value="update"><?php echo $tpl->getLang('save') ?></button>
                <a href="<?php echo $tpl->url('types') ?>">
                    <?php echo $tpl->getLang('cancel') ?>
                </a>
            </td>
	<?php else: ?>
        <td><?php echo $label->name ?></td>
        <td <?php if ($label->count > 0) echo 'colspan="2"'; ?>>
            <a href="<?php echo $tpl->url('types', 'action', 'edit', 'id', $label->id) ?>">
                <?php echo $tpl->getLang('edit') ?>
            </a>
        </td>
        <?php if ($label->count == 0): ?>
            <td>
            <a onclick="return confirm('<?php echo $tpl->getLang('js')['remove_confirm'] ?>')"
                href="<?php echo $tpl->url('types', 'action', 'remove', 'id', $label->id) ?>">
                <?php echo $tpl->getLang('delete') ?>
            </a>
            </td>
        <?php endif ?>
    <?php endif ?>

        <td>
        <a href="<?php echo $tpl->url('threads', 'label_id', $label->id) ?>">
            <?php echo $label->count ?>
        </a>
    </td>
</tr>
<?php endforeach ?>
<?php if (  $tpl->param('action') !== 'edit' &&
            $tpl->param('action') !== 'update'): ?>
<tr>
	<td><input name="name" value="<?php echo $tpl->value('name') ?>" /></td>
	<td colspan="3">
        <button name="action" value="add"><?php echo $tpl->getLang('save') ?></button>
    </td>
</tr>
<?php endif ?>
</table>
</form>
