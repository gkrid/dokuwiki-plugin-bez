<div class="info">
Aby usunąć wybrany rekord, nadpisz jego pola pustymi wartościami.
</div>
<table>
<tr>
	<th>Polski</th>
	<th>English</th>
	<th colspan="2">References</th>
</tr>
</tr>
<?php foreach ($template['types'] as $type): ?>
<tr>
	<?php if (isset($template['edit']) && $template['edit'] == $type['id']): ?>
	<form action="<?php echo $template['uri'] ?>
				?id=<?php echo $this->id('types', 'action', 'update', $type['id']) ?>" method="POST">
		<td><input name="pl" value="<?php echo $value['pl'] ?>" /></td>
		<td><input name="en" value="<?php echo $value['en'] ?>" /></td>
		<td>
			<input type="submit" value="<?php echo $bezlang['save'] ?>" />
		<a href="<?php echo $template['uri'] ?>?id=<?php echo $this->id('types') ?>">
			<?php echo $bezlang['cancel'] ?>
		</a>
		</td>
	</form>
	<?php else: ?>
	<td><?php echo $type['pl'] ?></td>
	<td><?php echo $type['en'] ?></td>
	<td>
	<a href="<?php echo $template['uri'] ?>?id=<?php echo $this->id('types', 'action', 'edit', $type['id']) ?>">
		<?php echo $bezlang['edit'] ?>
	</a>
	<?php endif ?>
	</td>
	<td>
	<a href="<?php echo $template['uri'] ?>?id=<?php echo $this->id('issues', 'type', $type['id']) ?>">
		<?php echo $type['refs'] ?>
	</a>
</td>
</tr>
<?php endforeach ?>
<?php if ( ! isset($template['edit'])): ?>
<form action="<?php echo $template['uri'] ?>?id=<?php echo $this->id('types', 'action', 'add') ?>" method="POST">
<tr>
	<td><input name="pl" value="<?php echo $value['pl'] ?>" /></td>
	<td><input name="en" value="<?php echo $value['en'] ?>" /></td>
	<td colspan="2"><input type="submit" value="<?php echo $bezlang['save'] ?>" /></td>
</tr>
<?php endif ?>
</form>
</table>
