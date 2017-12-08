<?php /* @var \dokuwiki\plugin\bez\meta\Tpl $tpl */ ?>
<table>
<tr>
	<th>Name</th>
	<th colspan="3">Count</th>
</tr>
<?php foreach ($tpl->get('labels') as $label): ?>
<tr>
	<?php if (  in_array($tpl->param('action'), array('edit', 'update')) &&
                $tpl->param('id') === $label->id): ?>
        <form action="
        <?php echo $tpl->url('types', 'action', 'update', 'id', $label->id) ?>"
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
<form action="<?php echo $tpl->url('types', 'action', 'add') ?>" method="POST">
<tr>
	<td><input name="name" value="<?php echo $tpl->value('name') ?>" /></td>
	<td colspan="3"><input type="submit" value="<?php echo $tpl->getLang('save') ?>" /></td>
</tr>
<?php endif ?>
</form>
</table>
