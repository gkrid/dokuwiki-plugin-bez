<table>
<tr>
	<th>Polski</th>
	<th>English</th>
	<th colspan="3">References</th>
</tr>
<?php foreach ($tpl->get('types') as $type): ?>
<tr>
	<?php if (  in_array($tpl->param('action'), array('edit', 'update')) &&
                $tpl->param('id') === $type->id): ?>
        <form action="
        <?php echo $tpl->url('types', 'action', 'update', 'id', $type->id) ?>"
        method="POST">
            <td>
                <input name="pl" value="<?php echo $tpl->value('pl') ?>" />
            </td>
            <td>
                <input name="en" value="<?php echo $tpl->value('en') ?>" />
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
        <td><?php echo $type->pl ?></td>
        <td><?php echo $type->en ?></td>
        <td <?php if ($type->refs !== '0') echo 'colspan="2"'; ?>>
            <a href="<?php echo $tpl->url('types', 'action', 'edit', 'id', $type->id) ?>">
                <?php echo $tpl->getLang('edit') ?>
            </a>
        </td>
        <?php if ($type->refs === '0'): ?>
            <td>
            <a onclick="return confirm('<?php echo $tpl->getLang('js')['remove_confirm'] ?>')"
                href="<?php echo $tpl->url('types', 'action', 'remove', 'id', $type->id) ?>">
                <?php echo $tpl->getLang('delete') ?>
            </a>
            </td>
        <?php endif ?>
    <?php endif ?>
    
        <td>
        <a href="<?php echo $tpl->url('issues', 'type', $type->id) ?>">
            <?php echo $type->refs ?>
        </a>
    </td>
</tr>
<?php endforeach ?>
<?php if (  $tpl->param('action') !== 'edit' &&
            $tpl->param('action') !== 'update'): ?>
<form action="<?php echo $tpl->url('types', 'action', 'add') ?>" method="POST">
<tr>
	<td><input name="pl" value="<?php echo $tpl->value('pl') ?>" /></td>
	<td><input name="en" value="<?php echo $tpl->value('en') ?>" /></td>
	<td colspan="3"><input type="submit" value="<?php echo $tpl->getLang('save') ?>" /></td>
</tr>
<?php endif ?>
</form>
</table>
