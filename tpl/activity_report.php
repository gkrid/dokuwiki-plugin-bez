<h1 class="bez_report">
<?php echo $template['title'] ?>
</h1>

<h2><?php echo $bezlang['activity_in_issues'] ?></h2>
<table class="bez_sumarise">
	<tr>
		<th><?php echo $bezlang['user'] ?></th>
		<th><?php echo $bezlang['reporter'] ?></th>
		<th><?php echo $bezlang['coordinator'] ?></th>
		<th><?php echo $bezlang['commentator'] ?></th>
		<th><?php echo $bezlang['executor'] ?></th>
		<th><?php echo $bezlang['report_total'] ?></th>
	</tr>
	<?php $reporter = 0 ?>
	<?php $total_total = 0 ?>
	
	<?php $coordinator = 0 ?>
	<?php $executor = 0 ?>
	<?php foreach ($template['report']['involvement'] as $nick => $involvement): ?>
		<tr>
			<td>
				<?php if ($involvement['name'] != ''): ?>
					<?php echo $involvement['name'] ?>
				<? else: ?>
					<i><?php echo $nick ?></i>
				<? endif ?>
			</td>
			<td><?php echo $involvement['reporter'] ?></td>
			<td><?php echo $involvement['coordinator'] ?></td>
			<td><?php echo $involvement['commentator'] ?></td>
			<td><?php echo $involvement['executor'] ?></td>
			<td><?php echo $involvement['total'] ?></td>
		</tr>
		<?php $reporter += (int)$involvement['reporter'] ?>
		<?php $coordinator += (int)$involvement['coordinator'] ?>
		<?php $executor += (int)$involvement['executor'] ?>
		<?php $total_total += (int)$involvement['total'] ?>
	<?php endforeach ?>
	<tr>
		<th><?php echo $bezlang['report_total'] ?></th>
		<td><?php echo $reporter ?></td>
		<td colspan="2"><?php echo $coordinator ?></td>
		<td colspan="2"><?php echo $executor ?></td>
	</tr>
</table>
<p style="font-weight: bold">KPI = <?php echo sprintf("%.2f", $total_total/$reporter) ?></p>


<h2><?php echo $bezlang['activity_in_tasks'] ?></h2>
<table class="bez_sumarise">
	<tr>
		<th><?php echo $bezlang['user'] ?></th>
		<th><?php echo $bezlang['opened_tasks'] ?></th>
		<th><?php echo $bezlang['closed_tasks'] ?></th>
		<th><?php echo $bezlang['rejected_tasks'] ?></th>
		<th><?php echo $bezlang['report_total'] ?></th>
	</tr>
	<?php $number_of_opened = 0 ?>
	<?php $number_of_closed = 0 ?>
	<?php $number_of_rejected = 0 ?>
	<?php foreach ($template['report']['tasks'] as $nick => $involvement): ?>
		<tr>
			<td>
				<?php if ($involvement['name'] != ''): ?>
					<?php echo $involvement['name'] ?>
				<? else: ?>
					<i><?php echo $nick ?></i>
				<? endif ?>
			</td>
			<td><?php echo $involvement['opened_tasks'] ?></td>
			<td><?php echo $involvement['closed_tasks'] ?></td>
			<td><?php echo $involvement['rejected_tasks'] ?></td>
			<td><?php echo $involvement['total'] ?></td>
			<?php $number_of_opened += (int)$involvement['opened_tasks'] ?>
			<?php $number_of_closed += (int)$involvement['closed_tasks'] ?>
			<?php $number_of_rejected += (int)$involvement['rejected_tasks'] ?>
		</tr>
	<?php endforeach ?>
	<tr>
		<th><?php echo $bezlang['report_total'] ?></th>
		<td><?php echo $number_of_opened ?></td>
		<td><?php echo $number_of_closed ?></td>
		<td><?php echo $number_of_rejected ?></td>
		<td><?php echo $number_of_opened + $number_of_closed + $number_of_rejected ?></td>
	</tr>
</table>
