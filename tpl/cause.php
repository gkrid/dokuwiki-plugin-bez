<a name="p<?php echo $cause['id'] ?>"></a>
<div id="p<?php echo $cause['id'] ?>" class="cause">
	<div class="bez_timebox">
		<span><strong><?php echo $bezlang['added'] ?>:</strong> <?php echo $helper->time2date($cause['date']) ?></span>
	</div>
	<h2>
		<a href="?id=<?php echo $this->id('issue_causes', 'id', $cause['issue']) ?>">#p<?php echo $cause['id'] ?></a>
		<?php if ($cause[potential] == 0): ?>
			<?php echo $bezlang['cause'] ?>
		<?php else: ?>
			<?php echo $bezlang['potential_cause'] ?>
		<?php endif ?>

		(<?php echo lcfirst($cause['rootcause']) ?>)
	</h2>
	<table>	
	<tr>
		<td>
		<strong>
			<?php if ($cause[potential] == 0): ?>
				<?php echo $bezlang['corrective_action_h'] ?>:
			<?php else: ?>
				<?php echo $bezlang['preventive_action_h'] ?>:
			<?php endif ?>
		</strong>&nbsp;
		<?php if (count($cause[tasks]) == 0): ?>
			&nbsp;---
		<?php else: ?>
			<?php foreach ($cause[tasks] as $task): ?>
				<?php if (isset($nparams[tid]) && $nparams[tid] == $task[id]) echo '<strong>' ?>
				<a href="?id=<?php echo $this->id('issue_task', 'id', $template[issue][id], 'tid', $task[id]) ?>">
					#z<?php echo $task[id] ?>
				</a>&nbsp;
				<?php if (isset($nparams[tid]) && $nparams[tid] == $task[id]) echo '</strong>' ?>
			<?php endforeach ?>
		<?php endif ?> 
		<?php if ($template['issue'][raw_state] == 0 && $helper->user_coordinator($template[issue][id])): ?> 
			&nbsp;&nbsp;&nbsp;
			<a href="?id=<?php echo $this->id('task_form', 'id', $template[issue][id], 'cause', $cause[id]) ?>">
				dodaj
			</a>
		<?php endif ?>
		</td>
	</tr>
	</table>	
	<?php echo $helper->wiki_parse($cause['cause']) ?>
	<?php if ($template['issue'][raw_state] == 0 && $helper->user_coordinator($template[issue][id])): ?> 
	<div class="bez_buttons">
	<a class="bds_inline_button"
	href="?id=<?php echo $this->id('cause_form', 'id', $template['issue']['id'], 'action', 'edit', 'id', $cause['id']) ?>#p_">
		✎	<?php echo $bezlang['edit'] ?>
		</a>
		<?php if (count($cause[tasks]) == 0): ?>
			<a class="bez_delete_button"
			href="?id=<?php echo $this->id('issue_causes', 'id', $template['issue']['id'], 'delete', $cause['id']) ?>">
				<?php echo $bezlang['delete'] ?>
			</a>
		<?php endif ?>
	</div>
	<?php endif ?>
</div>

