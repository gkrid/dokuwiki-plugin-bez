<h1 class="bez_report">
<?php echo $template['title'] ?>
</h1>

<h2><?php echo $bezlang['report_issues'] ?></h2>

<?php $number_of_open = 0 ?>
<?php $number_of_close = 0 ?>
<table class="bez_sumarise">
	<tr>
		<th><?php echo $bezlang['type'] ?></th>
		<th><?php echo $bezlang['number_of_open'] ?></th>
		<th><?php echo $bezlang['number_of_close'] ?></th>
		<th><?php echo $bezlang['diffirence'] ?></th>
	</tr>
	<?php foreach ($template['report']['issues'] as $issue): ?>
		<tr>
			<td><?php echo $issue['type'] ?></td>
			<td><?php echo $issue['number_of_open'] ?></td>
			<td><?php echo $issue['number_of_close'] ?></td>
			<td><?php echo $issue['number_of_open'] - $issue['number_of_close'] ?></td>
		</tr>
		<?php $number_of_open += (int)$issue['number_of_open'] ?>
		<?php $number_of_close += (int)$issue['number_of_close'] ?>
	<?php endforeach ?>
	<tr>
		<th><?php echo $bezlang['report_total'] ?></th>
		<td><?php echo $number_of_open ?></td>
		<td><?php echo $number_of_close ?></td>
		<td><?php echo $number_of_open - $number_of_close ?></td>
	</tr>
</table>


<table class="bez_sumarise">
	<tr>
		<th><?php echo $bezlang['type'] ?></th>
		<th><?php echo $bezlang['cost_of_open'] ?></th>
		<th><?php echo $bezlang['number_of_close'] ?></th>
		<th><?php echo $bezlang['diffirence'] ?></th>
	</tr>
	<?php $cost_of_open = 0 ?>
	<?php $cost_of_closed = 0 ?>
	<?php foreach ($template['report']['issues'] as $issue): ?>
		<tr>
			<td><?php echo $issue['type'] ?></td>
			<td>
				<?php if ($issue['cost_of_open'] == ''): ?>
					<em>---</em>
				<?php else: ?>
					<?php echo $issue['cost_of_open'] ?>
				<?php endif ?>
			</td>
			<td>
				<?php if ($issue['cost_of_close'] == ''): ?>
					<em>---</em>
				<?php else: ?>
					<?php echo $issue['cost_of_close'] ?>
				<?php endif ?>
			</td>
			<?php $diff = $issue['cost_of_open'] - $issue['cost_of_close'] ?>
			<td>
				<?php if ($diff === ''): ?>
					<em>---</em>
				<?php else: ?>
					<?php echo $diff ?>
				<?php endif ?>
			</td>
		</tr>
		<?php $cost_of_open += (int)$issue['cost_of_open'] ?>
		<?php $cost_of_closed += (int)$issue['cost_of_close'] ?>
		<?php endforeach ?>
	<tr>
		<th><?php echo $bezlang['report_total'] ?></th>
		<td><?php echo $cost_of_open ?></td>
		<td><?php echo $cost_of_closed ?></td>
		<td><?php echo $cost_of_open - $cost_of_closed ?></td>
	</tr>
</table>

<table class="bez_sumarise">
	<tr>
		<th><?php echo $bezlang['type'] ?></th>
		<th><?php echo $bezlang['average_of_close'] ?></th>
	</tr>
	<?php foreach ($template['report']['issues'] as $issue): ?>
		<tr>
			<td><?php echo $issue['type'] ?></td>
			<td><?php echo $issue['average'] ?></td>
		</tr>

		<?php endforeach ?>
	<tr>
		<th><?php echo $bezlang['average'] ?></th>
		<td><?php echo $template['report']['issues_average'] ?></td>
	</tr>
</table>



<h2><?php echo $bezlang['report_tasks'] ?></h2>


<?php $number_of_open = 0 ?>
<?php $number_of_close_on_time = 0 ?>
<?php $number_of_close_off_time = 0 ?>
<table class="bez_sumarise">
	<tr>
		<th><?php echo $bezlang['class'] ?></th>
		<th><?php echo $bezlang['number_of_open'] ?></th>
		<th><?php echo $bezlang['number_of_close_on_time'] ?></th>
		<th><?php echo $bezlang['number_of_close_off_time'] ?></th>
		<th><?php echo $bezlang['diffirence'] ?></th>
	</tr>
	<?php foreach ($template['report']['tasks'] as $task): ?>
		<tr>
			<td><?php echo $task['action'] ?></td>
			<td><?php echo $task['number_of_open'] ?></td>
			<td><?php echo $task['number_of_closed_on_time'] ?></td>
			<td><?php echo $task['number_of_closed_off_time'] ?></td>
			<td><?php echo $task['number_of_open'] - $task['number_of_closed_on_time'] - $task['number_of_closed_off_time'] ?></td>
		</tr>
		<?php $number_of_open += (int)$task['number_of_open'] ?>
		<?php $number_of_close_on_time += (int)$task['number_of_closed_on_time'] ?>
		<?php $number_of_close_off_time += (int)$task['number_of_closed_off_time'] ?>
	<?php endforeach ?>
	<tr>
		<th><?php echo $bezlang['report_total'] ?></th>
		<td><?php echo $number_of_open ?></td>
		<td><?php echo $number_of_close_on_time ?></td>
		<td><?php echo $number_of_close_off_time ?></td>
		<td><?php echo $number_of_open - $number_of_close_on_time - $number_of_close_off_time  ?></td>
	</tr>
</table>


<table class="bez_sumarise">
	<tr>
		<th><?php echo $bezlang['class'] ?></th>
		<th><?php echo $bezlang['cost_of_open'] ?></th>
		<th><?php echo $bezlang['number_of_close'] ?></th>
		<th><?php echo $bezlang['diffirence'] ?></th>
	</tr>
	<?php $cost_of_open = 0 ?>
	<?php $cost_of_closed = 0 ?>
	<?php foreach ($template['report']['tasks'] as $task): ?>
		<tr>
			<td><?php echo $task['action'] ?></td>
			<td>
				<?php if ($task['cost_of_open'] == ''): ?>
					<em>---</em>
				<?php else: ?>
					<?php echo $task['cost_of_open'] ?>
				<?php endif ?>
			</td>
			<td>
				<?php if ($task['cost_of_close'] == ''): ?>
					<em>---</em>
				<?php else: ?>
					<?php echo $task['cost_of_close'] ?>
				<?php endif ?>
			</td>
			<?php $diff = $task['cost_of_open'] - $task['cost_of_close'] ?>
			<td>
				<?php if ($diff === ''): ?>
					<em>---</em>
				<?php else: ?>
					<?php echo $diff ?>
				<?php endif ?>
			</td>
		</tr>
		<?php $cost_of_open += (int)$task['cost_of_open'] ?>
		<?php $cost_of_closed += (int)$task['cost_of_close'] ?>
		<?php endforeach ?>
	<tr>
		<th><?php echo $bezlang['report_total'] ?></th>
		<td><?php echo $cost_of_open ?></td>
		<td><?php echo $cost_of_closed ?></td>
		<td><?php echo $cost_of_open - $cost_of_closed ?></td>
	</tr>
</table>

<table class="bez_sumarise">
	<tr>
		<th><?php echo $bezlang['class'] ?></th>
		<th><?php echo $bezlang['average_of_close'] ?></th>
	</tr>
	<?php foreach ($template['report']['tasks'] as $task): ?>
		<tr>
			<td><?php echo $task['action'] ?></td>
			<td><?php echo $task['average'] ?></td>
		</tr>

		<?php endforeach ?>
	<tr>
		<th><?php echo $bezlang['average'] ?></th>
		<td><?php echo $template['report']['tasks_average'] ?></td>
	</tr>
</table>

<?php /*
<table class="bez_sumarise">
	<tr>
		<th><?php echo $bezlang['class'] ?></th>
		<th><?php echo $bezlang['number'] ?></th>
		<th><?php echo $bezlang['totalcost'] ?></th>
		<?php if (!isset($this->report_open)): ?>
			<th><?php echo $bezlang['report_priority'] ?></th>
		<?php endif ?>
	</tr>
	<?php foreach ($template['report']['tasks'] as $task): ?>
		<tr>
			<td><?php echo $task['action'] ?></td>
			<td><?php echo $task['number'] ?></td>
			<td>
				<?php if ($task['totalcost'] == ''): ?>
					<em>---</em>
				<?php else: ?>
					<?php echo $task['totalcost'] ?>
				<?php endif ?>
			</td>
			<?php if (!isset($this->report_open)): ?>
				<td><?php echo $task['average'] ?></td>
			<?php endif ?>
		</tr>
	<?php endforeach ?>
	<tr>
		<th><?php echo $bezlang['report_total'] ?></th>
		<td><?php echo $template['report']['tasks_total'] ?></td>
		<td><?php echo $template['report']['tasks_totalcost'] ?></td>
		<?php if (!isset($this->report_open)): ?>
			<td><?php echo $template['report']['tasks_average'] ?></td>
		<?php endif ?>
	</tr>
</table>
*/
?>

<h2><?php echo $bezlang['report_causes'] ?></h2>
<table class="bez_sumarise">
	<tr>
		<th><?php echo $bezlang['root_cause'] ?></th>
		<th><?php echo $bezlang['number'] ?></th>
		<th><?php echo $bezlang['cost'] ?></th>
		<th><?php echo $bezlang['report_priority'] ?></th>
	</tr>
	<?php $number = 0 ?>
	<?php $cost = 0 ?>
	<?php $average = 0?>
	<?php foreach ($template['report']['causes'] as $cause): ?>
		<tr>
			<td><?php echo $cause['rootcause'] ?></td>
			<td><?php echo $cause['number'] ?></td>
			<td>
				<?php if ($cause['cost'] == ''): ?>
					<em>---</em>
				<?php else: ?>
					<?php echo $cause['cost'] ?>
				<?php endif ?>
			</td>
			<td><?php echo $helper->days($cause['average']) ?></td>
		</tr>
		<?php $number += (int)$cause['number'] ?>
		<?php $cost += (int)$cause['cost']  ?>
		<?php $average += (int)$cause['average']*(int)$cause['number']  ?>
	<?php endforeach ?>
	<tr>
		<th><?php echo $bezlang['report_total'] ?></th>
		<td><?php echo $number ?></td>
		<td><?php echo $cost ?></td>
		<td>
			<?php
				if ($number > 0)
					echo $helper->days($average/$number);
				else
					echo "---";
			?>
		</td>
	</tr>
</table>

