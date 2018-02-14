<h1 class="bez_report">
<?php echo $tpl->getLang('activity_report') ?>
</h1>

<div class="bez_filter_form">
    <form action="<?php echo $tpl->url('activity_report') ?>" method="post">
        <span class="datepair">
            <label>od: <input name="from" value="<?php echo $tpl->value('from') ?>" class="date start" style="width: 90px"></label>
            <label>do: <input name="to" value="<?php echo $tpl->value('to') ?>" class="date end" style="width: 90px"></label>
        </span>
        <button>Pokaż</button>
    </form>

</div>

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
			<td><?php echo $involvement['original_poster_sum'] ?></td>
			<td><?php echo $involvement['coordinator_sum'] ?></td>
			<td><?php echo $involvement['commentator_sum'] ?></td>
			<td><?php echo $involvement['task_assignee_sum'] ?></td>
            <?php $total = $involvement['original_poster_sum'] + $involvement['coordinator_sum'] + $involvement['commentator_sum'] + $involvement['task_assignee_sum'] ?>
            <td><?php echo $total ?></td>
		</tr>
		<?php $reporter += $involvement['original_poster_sum'] ?>
		<?php $coordinator += $involvement['coordinator_sum'] ?>
        <?php $commentator += $involvement['commentator_sum'] ?>
		<?php $executor += $involvement['task_assignee_sum'] ?>
		<?php $total_total += $total ?>
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
<p style="font-weight: bold">KPI = <?php echo sprintf("%.2f", $tpl->get('kpi')) ?></p>


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
			<td><?php echo $involvement['original_poster_sum'] ?></td>
			<td><?php echo $involvement['commentator_sum'] ?></td>
			<td><?php echo $involvement['assignee_sum'] ?></td>
            <?php $total = $involvement['original_poster_sum'] + $involvement['commentator_sum'] + $involvement['assignee_sum'] ?>
			<td><?php echo $total ?></td>
			<?php $reporter += $involvement['original_poster_sum'] ?>
			<?php $commentator += $involvement['commentator_sum'] ?>
			<?php $executor += $involvement['assignee_sum'] ?>
            <?php $total_total += $total ?>
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
