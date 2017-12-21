<h1 class="bez_report">
<?php echo $tpl->getLang('activity_report') ?>
</h1>

<h2><?php echo $tpl->getLang('activity_in_issues') ?></h2>
<table class="bez_sumarise">
	<tr>
		<th><?php echo $tpl->getLang('user') ?></th>
		<th><?php echo $tpl->getLang('reporter') ?></th>
		<th><?php echo $tpl->getLang('coordinator') ?></th>
		<th><?php echo $tpl->getLang('commentator') ?></th>
		<th><?php echo $tpl->getLang('executor') ?></th>
		<th><?php echo $tpl->getLang('report_total') ?></th>
	</tr>
	<?php $reporter = 0 ?>
	<?php $total_total = 0 ?>
	<?php $commentator = 0 ?>
	<?php $coordinator = 0 ?>
	<?php $executor = 0 ?>
	<?php foreach ($tpl->get('thread_involvement') as $involvement): ?>
		<tr>
			<td>
                <?php echo $tpl->user_name($involvement['user_id']) ?>
			</td>
			<td><?php echo $involvement['SUM(original_poster)'] ?></td>
			<td><?php echo $involvement['SUM(coordinator)'] ?></td>
			<td><?php echo $involvement['SUM(commentator)'] ?></td>
			<td><?php echo $involvement['SUM(task_assignee)'] ?></td>
			<td><?php echo $involvement['COUNT(*)'] ?></td>
		</tr>
		<?php $reporter += $involvement['SUM(original_poster)'] ?>
		<?php $coordinator += $involvement['SUM(coordinator)'] ?>
        <?php $commentator += $involvement['SUM(commentator)'] ?>
		<?php $executor += $involvement['SUM(task_assignee)'] ?>
		<?php $total_total += $involvement['COUNT(*)'] ?>
	<?php endforeach ?>
	<tr>
		<th><?php echo $tpl->getLang('report_total') ?></th>
		<td><?php echo $reporter ?></td>
		<td><?php echo $coordinator ?></td>
        <td><?php echo $commentator ?></td>
		<td><?php echo $executor ?></td>
        <td><?php echo $total_total ?></td>
	</tr>
</table>
<?php
    if ($reporter !== 0) {
        $kpi = $total_total/$reporter;
    } else {
        $kpi = 0;
    }
?>
<p style="font-weight: bold">KPI = <?php echo sprintf("%.2f", $kpi) ?></p>


<h2><?php echo $tpl->getLang('activity_in_tasks') ?></h2>
<table class="bez_sumarise">
	<tr>
		<th><?php echo $tpl->getLang('user') ?></th>
		<th><?php echo $tpl->getLang('reporter') ?></th>
		<th><?php echo $tpl->getLang('commentator') ?></th>
		<th><?php echo $tpl->getLang('executor') ?></th>
		<th><?php echo $tpl->getLang('report_total') ?></th>
	</tr>
	<?php $reporter = 0 ?>
	<?php $commentator = 0 ?>
	<?php $executor = 0 ?>
    <?php $total_total = 0 ?>
	<?php foreach ($tpl->get('task_involvement') as $involvement): ?>
		<tr>
			<td>
                <?php echo $tpl->user_name($involvement['user_id']) ?>
			</td>
			<td><?php echo $involvement['SUM(original_poster)'] ?></td>
			<td><?php echo $involvement['SUM(commentator)'] ?></td>
			<td><?php echo $involvement['SUM(assignee)'] ?></td>
			<td><?php echo $involvement['COUNT(*)'] ?></td>
			<?php $reporter += $involvement['SUM(original_poster)'] ?>
			<?php $commentator += $involvement['SUM(commentator)'] ?>
			<?php $executor += $involvement['SUM(assignee)'] ?>
            <?php $total_total += $involvement['COUNT(*)'] ?>
		</tr>
	<?php endforeach ?>
	<tr>
		<th><?php echo $tpl->getLang('report_total') ?></th>
		<td><?php echo $reporter ?></td>
		<td><?php echo $commentator ?></td>
		<td><?php echo $executor ?></td>
		<td><?php echo $total_total ?></td>
	</tr>
</table>
