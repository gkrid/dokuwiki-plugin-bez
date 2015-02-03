<div id="bez_issue_filter" class="bds_block">
<form action="<?php echo $template['uri'] ?>?id=bez:issues" method="POST">
<fieldset class="bds_form">
	<label><?php echo $bezlang['state'] ?>:
		<select name="state">
			<option <?php if ($value['state'] == '-all') echo 'selected' ?>
				value="-all">--- <?php echo $bezlang['all'] ?> ---</option>
		<?php foreach ($template['states'] as $key => $name): ?>
			<option <?php if ($value['state'] == (string)$key) echo 'selected' ?>
				value="<?php echo $key ?>"><?php echo $name ?></option>
		<?php endforeach ?>
		</select>
	</label>

	<label><?php echo $bezlang['type'] ?>:
		<select name="type">
			<option <?php if ($value['type'] == '-all') echo 'selected' ?>
				value="-all">--- <?php echo $bezlang['all'] ?> ---</option>
		<?php foreach ($template['issue_types'] as $key => $name): ?>
			<option <?php if ($value['type'] == (string)$key) echo 'selected' ?>
				value="<?php echo $key ?>"><?php echo $name ?></option>
		<?php endforeach ?>
		</select>
	</label>

	<label><?php echo $bezlang['entity'] ?>:
		<select name="entity">
			<option <?php if ($value['entity'] == '-all') echo 'selected' ?>
				value="-all">--- <?php echo $bezlang['all'] ?> ---</option>
		<?php foreach ($template['entities'] as $entity): ?>
			<option <?php if ($value['entity'] == $entity) echo 'selected' ?>
				value="<?php echo $entity ?>"><?php echo $entity ?></option>
		<?php endforeach ?>
		</select>
	</label>

	<label><?php echo $bezlang['coordinator'] ?>:
		<select name="coordinator">
			<option <?php if ($value['coordinator'] == '-all') echo 'selected' ?>
				value="-all">--- <?php echo $bezlang['all'] ?> ---</option>
			<option <?php if ($value['coordinator'] == '-none') echo 'selected' ?>
				value="-none">--- <?php echo $bezlang['none'] ?> ---</option>
		<?php foreach ($template['coordinators'] as $nick => $name): ?>
			<option <?php if ($value['coordinator'] == $nick) echo 'selected' ?>
				value="<?php echo $nick ?>"><?php echo $name ?></option>
		<?php endforeach ?>
		</select>
	</label>

	<label><?php echo $bezlang['year'] ?>:
		<select name="year">
			<option <?php if ($value['year'] == '-all') echo 'selected' ?>
				value="-all">--- <?php echo $bezlang['all'] ?> ---</option>
		<?php foreach ($template['years'] as $year): ?>
			<option <?php if ($value['year'] == $year) echo 'selected' ?>
				value="<?php echo $year ?>"><?php echo $year ?></option>
		<?php endforeach ?>
		</select>
	</label>
	<label><input type="submit" value="<?php echo $bezlang['filter'] ?>" />
</fieldset>
</form>
</div>

<table>
	<tr>
		<th><?php echo $bezlang['id'] ?></th>
		<th><?php echo $bezlang['state'] ?></th>
		<th><?php echo $bezlang['type'] ?></th>
		<th><?php echo $bezlang['title'] ?></th>
		<th><?php echo $bezlang['coordinator'] ?></th>
		<th><?php echo $bezlang['date'] ?></th>
		<th><?php echo $bezlang['last_mod_date'] ?></th>
	</tr>
	<?php foreach ($template['issues'] as $issue): ?>
		<tr>
			<td><?php echo $helper->html_issue_link($issue['id']) ?></td>
			<td><?php echo $issue['state'] ?></td>
			<td><?php echo $issue['type'] ?></td>
			<td>[<?php echo $issue['entity'] ?>] <?php echo $issue['title'] ?></td>
			<td><?php echo $issue['coordinator'] ?></td>
			<td><?php echo $helper->string_time_to_now($issue['date']) ?></td>
			<td>
				<?php if ($issue['last_mod'] == ''): ?>
					<em><?php echo $bezlang['ns'] ?></em>
				<?php else: ?>
					<?php echo $helper->string_time_to_now($issue['last_mod']) ?>
				<?php endif ?>
			</td>
		</tr>
	<?php endforeach ?>
</table>

</div>
