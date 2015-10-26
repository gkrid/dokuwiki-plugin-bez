<div class="info">
Aby usunąć wybrany rekord, nadpisz jego pola pustymi wartościami.
</div>
<a href="<?php echo $template['uri'] ?>?id=<?php echo $this->id('root_causes', 'action', 'clean') ?>">
	Wyczyść typy bez referencji.
</a>
<table>
<tr>
	<th>Polski</th>
	<th>English</th>
	<th colspan="2">References</th>
</tr>
</tr>
<?php foreach ($template['rootcauses'] as $rootcause): ?>
<tr>
	<?php if (isset($template['edit']) && $template['edit'] == $rootcause['id']): ?>
	<form action="<?php echo $template['uri'] ?>
				?id=<?php echo $this->id('root_causes', 'action', 'update', $rootcause['id']) ?>" method="POST">
		<td><input name="pl" value="<?php echo $value['pl'] ?>" /></td>
		<td><input name="en" value="<?php echo $value['en'] ?>" /></td>
		<td>
			<input type="submit" value="<?php echo $bezlang['save'] ?>" />
		<a href="<?php echo $template['uri'] ?>?id=<?php echo $this->id('root_causes') ?>">
			<?php echo $bezlang['cancel'] ?>
		</a>
		</td>
	</form>
	<?php else: ?>
	<td><?php echo $rootcause['pl'] ?></td>
	<td><?php echo $rootcause['en'] ?></td>
	<td>
	<a href="<?php echo $template['uri'] ?>?id=<?php echo $this->id('root_causes', 'action', 'edit', $rootcause['id']) ?>">
		<?php echo $bezlang['edit'] ?>
	</a>
	<?php endif ?>
	</td>
	<td>
	<a href="<?php echo $template['uri'] ?>?id=<?php echo $this->id('issues', 'rootcause', $rootcause['id']) ?>">
		<?php echo $rootcause['refs'] ?>
	</a>
</td>
</tr>
<?php endforeach ?>
<?php if ( ! isset($template['edit'])): ?>
<form action="<?php echo $template['uri'] ?>?id=<?php echo $this->id('root_causes', 'action', 'add') ?>" method="POST">
<tr>
	<td><input name="pl" value="<?php echo $value['pl'] ?>" /></td>
	<td><input name="en" value="<?php echo $value['en'] ?>" /></td>
	<td colspan="2"><input type="submit" value="<?php echo $bezlang['save'] ?>" /></td>
</tr>
<?php endif ?>
</form>
</table>
