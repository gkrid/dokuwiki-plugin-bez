<h1 class="bez_report">
<?php echo $template['title'] ?>
</h1>

<table>
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
	<?php foreach ($template['report']['involvement'] as $nick => $involvement): ?>
		<tr>
			<td><?php echo $involvement['name'] ?></td>
			<td><?php echo $involvement['reporter'] ?></td>
			<td><?php echo $involvement['coordinator'] ?></td>
			<td><?php echo $involvement['commentator'] ?></td>
			<td><?php echo $involvement['executor'] ?></td>
			<td><?php echo $involvement['total'] ?></td>
		</tr>
		<?php $reporter += (int)$involvement['reporter'] ?>
		<?php $total_total += (int)$involvement['total'] ?>
	<?php endforeach ?>
</table>
<p style="font-weight: bold">KPI = <?php echo sprintf("%.2f", $total_total/$reporter) ?></p>
