<table class="bez">
	<tr>
		<th><?php echo $bezlang['id'] ?></th>
		<th><?php echo $bezlang['state'] ?></th>
		<th><?php echo $bezlang['type'] ?></th>
		<th><?php echo $bezlang['title'] ?></th>
		<th><?php echo $bezlang['date'] ?></th>
		<th><?php echo $bezlang['last_mod_date'] ?></th>
		<th><?php echo $bezlang['opened_tasks'] ?></th>
	</tr>
	<?php foreach ($template['issues'] as $issue): ?>
		<tr class="pr<?php echo $issue['priority'] ?>">
			<td><?php echo $helper->html_issue_link($issue['id']) ?></td>
			<td><?php echo $issue['state'] ?></td>
			<td><?php echo $issue['type'] ?></td>
			<td>[<?php echo $issue['entity'] ?>] <?php echo $issue['title'] ?></td>
			<td><?php echo $helper->string_time_to_now($issue['date']) ?></td>
			<td>
				<?php if ($issue['last_mod'] == ''): ?>
					<em><?php echo $bezlang['ns'] ?></em>
				<?php else: ?>
					<?php echo $helper->string_time_to_now($issue['last_mod']) ?>
				<?php endif ?>
			</td>
			<td><?php echo $issue['tasks_opened'] ?></td>
		</tr>
	<?php endforeach ?>
</table>
