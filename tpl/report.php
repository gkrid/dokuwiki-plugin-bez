<div  class="bds_block">
<form action="<?php echo $template['uri'] ?>?id=bez:report" method="POST">
<fieldset class="bds_form">
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
	<label><input type="submit" value="<?php echo $bezlang['filter'] ?>" />

	<span id="bez_8d_send_button">[<a href="
		<?php echo $helper->mailto($template['issue']['coordinator_email'],
		$bezlang['8d_report'].': #'.$template['issue']['id'].' ['.$template['issue']['entity'].'] '.$template['issue']['title'],
		$template['uri']) ?>">
		✉ <?php echo $bezlang['send_mail'] ?>
	</a>]</span>
</fieldset>
</form>
</div>

<h1><?php echo $bezlang['report'] ?></h1>

<h2><?php echo $bezlang['report_issues'] ?></h2>
<table class="bez_sumarise">
	<tr>
		<th><?php echo $bezlang['type'] ?></th>
		<th><?php echo $bezlang['number'] ?></th>
		<th><?php echo $bezlang['totalcost'] ?></th>
	</tr>
	<?php foreach ($template['report']['issues'] as $issue): ?>
		<tr>
			<td><?php echo $issue['type'] ?></td>
			<td><?php echo $issue['number'] ?></td>
			<td>
				<?php if ($issue['totalcost'] == ''): ?>
					<em><?php echo $bezlang['ns'] ?></em>
				<?php else: ?>
					<?php echo $issue['totalcost'] ?>
				<?php endif ?>
			</td>
		</tr>
	<?php endforeach ?>
	<tr>
		<th><?php echo $bezlang['report_total'] ?></th>
		<td><?php echo $template['report']['issues_total'] ?></td>
		<td><?php echo $template['report']['issues_totalcost'] ?></td>
	</tr>
</table>
<h2><?php echo $bezlang['report_tasks'] ?></h2>
<table class="bez_sumarise">
	<tr>
		<th><?php echo $bezlang['class'] ?></th>
		<th><?php echo $bezlang['number'] ?></th>
		<th><?php echo $bezlang['totalcost'] ?></th>
	</tr>
	<?php foreach ($template['report']['tasks'] as $task): ?>
		<tr>
			<td><?php echo $task['action'] ?></td>
			<td><?php echo $task['number'] ?></td>
			<td>
				<?php if ($task['totalcost'] == ''): ?>
					<em><?php echo $bezlang['ns'] ?></em>
				<?php else: ?>
					<?php echo $task['totalcost'] ?>
				<?php endif ?>
			</td>
		</tr>
	<?php endforeach ?>
	<tr>
		<th><?php echo $bezlang['report_total'] ?></th>
		<td><?php echo $template['report']['tasks_total'] ?></td>
		<td><?php echo $template['report']['tasks_totalcost'] ?></td>
	</tr>
</table>

<h2><?php echo $bezlang['report_causes'] ?></h2>
<table class="bez_sumarise">
	<tr>
		<th><?php echo $bezlang['root_cause'] ?></th>
		<th><?php echo $bezlang['number'] ?></th>
	</tr>
	<?php foreach ($template['report']['causes'] as $cause): ?>
		<tr>
			<td><?php echo $cause['rootcause'] ?></td>
			<td><?php echo $cause['number'] ?></td>
		</tr>
	<?php endforeach ?>
	<tr>
		<th><?php echo $bezlang['report_total'] ?></th>
		<td><?php echo $template['report']['causes_total'] ?></td>
	</tr>
</table>

<h2><?php echo $bezlang['report_priority'] ?></h2>
<table class="bez bez_sumarise">
	<tr>
		<th><?php echo $bezlang['priority'] ?></th>
		<th><?php echo $bezlang['number'] ?></th>
		<th><?php echo $bezlang['average'] ?></th>
	</tr>
	<?php foreach ($template['report']['priorities'] as $priority): ?>
		<tr class="pr<?php echo $priority['priority_nr'] ?>">
			<td><?php echo $priority['priority'] ?></td>
			<td><?php echo $priority['number'] ?></td>
			<td><?php echo $priority['average'] ?></td>
		</tr>
	<?php endforeach ?>
	<tr>
		<th><?php echo $bezlang['report_total'] ?></th>
		<td><?php echo $template['report']['priorities_total'] ?></td>
		<td><?php echo $template['report']['priorities_average'] ?></td>
	</tr>
</table>
