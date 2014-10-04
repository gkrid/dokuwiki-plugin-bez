<div id="bds_issue_box">
<h1>
<?php echo $helper->html_issue_link($template['issue']['id']) ?>
<?php echo $template['issue']['type'] ?> (<?php echo $template['issue']['state'] ?>)
</h1>

<h1>[<?php echo $template['issue']['entity'] ?>] <?php echo $template['issue']['title'] ?></h1>

<div class="time_box">
<span><?php echo $bezlang['opened_for'] ?>: <?php $template['issue']['date'] ?></span>
<?php if ($template['issue']['moddate'] != NULL): ?>
	<span>
	<?php if ($template['closed']): ?>
		<?php echo $bezlang['closed'] ?>
	<?php else: ?>
		<?php echo $bezlang['last_modified'] ?>
	<?php endif ?>: <?php echo $helper->string_time_to_now($template['issue']['last_mod_date']) ?>
	</span>
<?php endif ?>
</div>

<table>
<tr>
<th><?php echo $bezlang['reporter'] ?></th>
<td><?php echo $template['issue']['reporter'] ?></td>
<th><?php echo $bezlang['coordinator'] ?></th>
<td><?php echo $template['issue']['coordinator'] ?></td>
</tr>
</table>

<h2><?php echo $bezlang['description'] ?></h2>

<?php echo $template['issue']['description'] ?>

<?php if ($template['closed']): ?>
	<h2><?php echo $bezlang['opinion'] ?></h2>
	<?php echo $template['issue']['opinion'] ?>
<?php endif ?>

<a href="?id=bez:8d:<?php echo $template['issue']['id'] ?>" class="bds_inline_button bds_report_button">
<?php echo $bezlang['8d_report'] ?></a>

</div>

<!-- Comments -->
<div class="bds_block" id="bez_comment">
	<h1><?php echo $bezlang['comments'] ?> <span>(<?php echo count($template['comments']) ?>)</span></h1>
	<div class="bds_block_content">
		<?php foreach ($template['comments'] as $comment): ?>
			<a name="bez_comment_<?php echo $comment['id'] ?>"></a>
			<div id="<?php echo $comment['id'] ?>" class="comment">

			<h2>
			<?php echo $bezlang['comment_added'] ?>
			<?php echo $helper->string_time_to_now($comment['date']) ?>
			<?php echo $bezlang['by'] ?>
			<?php echo $comment['reporter'] ?>
			<span><?php echo $bezlang['comment_noun'] ?>: k<?php echo $comment['id'] ?></span>
			</h2>
			<?php if ( ! $template['closed']): ?> 
				<a class="bds_inline_button" href="?id=bez:issue_show:<?php echo $template['issue']['id'] ?>:edit_comment:<?php echo $comment['id'] ?>#bez_comment"><?php echo $bezlang['change'] ?></a>
			<?php endif ?>

			<?php echo $helper->wiki_parse($comment['content']) ?>
			</div>
		<?php endforeach ?>
		<form action="#bez_comment" method="POST">
			<input type="hidden" name="event" value="comment">
			<fieldset class="bds_form">
				<div class="row">
					<label for="content"><?php echo $bezlang['description'] ?>:</label>
					<span><textarea name="content" id="content"><?php echo $value['content'] ?></textarea></span>
				</div>
			</fieldset>
			<input type="submit" value="<?php echo $template['comment_button'] ?>">
		</form>
		<a name="bez_comment"></a>
	</div>
</div>

<!-- Causes -->
<div class="bds_block" id="bez_cause">
	<h1><?php echo $bezlang['causes'] ?> <span>(<?php echo count($template['causes']) ?>)</span></h1>
	<div class="bds_block_content">
		<?php foreach ($template['causes'] as $cause): ?>
			<a name="bez_cause_<?php echo $cause['id'] ?>"></a>
			<div id="<?php echo $cause['id'] ?>" class="cause">

			<h2>
			<?php echo $bezlang['cause_added'] ?>
			<?php echo $helper->string_time_to_now($cause['date']) ?>
			<?php echo $bezlang['by'] ?>
			<?php echo $cause['reporter'] ?>
			<span><?php echo $bezlang['cause_noun'] ?>: p<?php echo $cause['id'] ?></span>
			</h2>
			<?php if ( ! $template['closed']): ?> 
				<a class="bds_inline_button" href="?id=bez:issue_show:<?php echo $template['issue']['id'] ?>:edit_cause:<?php echo $cause['id'] ?>#bez_cause"><?php echo $bezlang['change'] ?></a>
			<?php endif ?>
			<div class="root_cause">
			<span>
			<?php echo lcfirst($bezlang['root_cause']) ?>:
			<strong><?php echo $cause['rootcause'] ?></strong>
			</span>
			</div>
			<?php echo $helper->wiki_parse($cause['cause']) ?>
			</div>
		<?php endforeach ?>
		<form action="#bez_cause" method="POST">
			<input type="hidden" name="event" value="cause">
			<fieldset class="bds_form">
				<div class="row">
				<label for="rootcause"><?php echo $bezlang['root_cause'] ?>:</label>
				<span>
					<select name="rootcause" id="rootcause">
					<?php foreach ($template['rootcauses'] as $key => $name): ?>
						<option <?php if ($value['rootcause'] == $key) echo 'selected' ?>
						 value="<?php echo $key ?>"><?php echo $name ?></option>
					<?php endforeach ?>
					</select>
				</span>
				</div>
				<div class="row">
					<label for="cause"><?php echo $bezlang['description'] ?>:</label>
					<span><textarea name="cause" id="cause"><?php echo $value['cause'] ?></textarea></span>
				</div>
			</fieldset>
			<input type="submit" value="<?php echo $template['cause_button'] ?>">
		</form>
		<a name="bez_cause"></a>
	</div>
</div>

<!-- Tasks -->
<div class="bds_block" id="bez_task">
	<h1><?php echo $bezlang['tasks'] ?> <span>(<?php echo count($template['tasks']) ?>)</span></h1>
	<div class="bds_block_content">
	<?php foreach ($template['tasks'] as $task): ?>
			<a name="bez_task_<?php echo $task['id'] ?>"></a>
			<div id="<?php echo $task['id'] ?>" class="task">

			<h2>
			<?php echo $bezlang['task_added'] ?>
			<?php echo $helper->string_time_to_now($task['date']) ?>
			<?php echo $bezlang['by'] ?>
			<?php echo $task['reporter'] ?>
			<span><?php echo $bezlang['task'] ?>: z<?php echo $task['id'] ?></span>
			</h2>
			<?php if ( ! $template['closed']): ?> 
				<a class="bds_inline_button" href="?id=bez:issue_show:<?php echo $template['issue']['id'] ?>:edit_task:<?php echo $task['id'] ?>#bez_task"><?php echo $bezlang['change'] ?></a>
			<?php endif ?>

			<table>	
			<tr>
					<th><?php echo $bezlang['task_state'] ?>:</th>
					<td><?php echo $task['state'] ?></td>

					<th><?php echo $bezlang['executor'] ?>:</th>
					<td><?php echo $task['executor'] ?></td>

					<th><?php echo $bezlang['action'] ?>:</th>
					<td><?php echo $task['action'] ?></td>

					<?php if ($task['cost'] != 0): ?>
						<th><?php echo $bezlang['cost'] ?>:</th>
						<td><?php echo $task['cost'] ?></td>
					<?php endif ?>
			</tr>
			</table>	

			<?php echo $helper->wiki_parse($task['task']) ?>
			</div>
	<?php endforeach ?>
	<?php if ($template['user_is_coordinator']): ?>
		<form action="#bez_task" method="POST">
			<input type="hidden" name="event" value="task">
			<fieldset class="bds_form">
				<div class="row">
				<label for="executor"><?php echo $bezlang['executor'] ?>:</label>
				<span>
				<select name="executor" id="executor">
				<?php foreach ($template['users'] as $nick => $name): ?>
					<option <?php if ($value['executor'] == $nick) echo 'selected' ?>
					 value="<?php echo $nick ?>"><?php echo $name ?></option>
				<?php endforeach ?>
				</select>
				</span>
				</div>

				<div class="row">
				<label for="action"><?php echo $bezlang['action'] ?>:</label>
				<span>
				<select name="action" id="action">
				<?php foreach ($template['taskactions'] as $key => $name): ?>
					<option <?php if ($value['action'] == $key) echo 'selected' ?>
					 value="<?php echo $key ?>"><?php echo $name ?></option>
				<?php endforeach ?>
				</select>
				</span>
				</div>

				<div class="row">
					<label for="task"><?php echo $bezlang['description'] ?>:</label>
					<span><textarea name="task" id="task"><?php echo $value['task'] ?></textarea></span>
				</div>

				<div class="row">
					<label for="cost"><?php echo $bezlang['cost'] ?>:</label>
					<span><input name="cost" id="cost" value="<?php echo $value['cost'] ?>"></span>
				</div>
			</fieldset>
			<input type="submit" value="<?php echo $template['cause_button'] ?>">
		</form>
		<?php endif ?>
		<a name="bez_task"></a>
	</div>
</div>
