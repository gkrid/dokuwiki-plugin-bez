<div id="bds_issue_box">
<h1>
<?php echo $helper->html_issue_link($template['issue']['id']) ?>
<?php echo $template['issue']['type'] ?> (<?php echo $template['issue']['state'] ?>)
</h1>

<h1>[<?php echo $template['issue']['entity'] ?>] <?php echo $template['issue']['title'] ?></h1>

<div class="time_box">
<span><?php echo $bezlang['open'] ?>: <?php echo $helper->time2date($template['issue']['date']) ?></span>
<?php if ($template['issue']['last_mod'] != NULL): ?>
	<span>
	<?php if ($template['closed']): ?>
		<?php echo $bezlang['closed'] ?>
	<?php else: ?>
		<?php echo $bezlang['last_modified'] ?>
	<?php endif ?>: <?php echo $helper->time2date($template['issue']['last_mod']) ?>
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

<?php if ($template['successfully_closed']): ?>
	<h2><?php echo $bezlang['opinion'] ?></h2>
	<?php echo $template['issue']['opinion'] ?>
<?php endif ?>

<a href="<?php echo $template['8d_link'] ?>" class="bds_inline_button bds_report_button">
	<?php echo $bezlang['8d_report'] ?>
</a>

<a class="bds_inline_button bds_send_button" href="
	<?php echo $helper->mailto($template['issue']['coordinator_email'],
	$bezlang['new_issue'].': #'.$template['issue']['id'].' ['.$template['issue']['entity'].'] '.$template['issue']['title'],
	$template['uri']) ?>">
	✉ <?php echo $bezlang['send_mail'] ?>
</a>

<?php if ($template['user_is_coordinator']): ?> 
	<a href="?id=bez:issue_report:<?php echo $template['issue']['id'] ?>" class="bds_inline_button bds_edit_button">
		<?php echo $bezlang['edit'] ?>
	</a>
<?php endif ?>

</div>

<!-- Comments -->
<div class="bds_block" id="bez_comments">
	<h1><?php echo $bezlang['comments'] ?> <span>(<?php echo count($template['comments']) ?>)</span></h1>
	<div class="bds_block_content">
		<?php foreach ($template['comments'] as $comment): ?>
			<a name="k<?php echo $comment['id'] ?>"></a>
			<div id="k<?php echo $comment['id'] ?>" class="comment">

			<h2>
			<?php echo $bezlang['comment_added'] ?>
			<?php echo $helper->string_time_to_now($comment['date']) ?>
			<?php echo $bezlang['by'] ?>
			<?php echo $comment['reporter'] ?>
			<span><?php echo $bezlang['comment_noun'] ?>: k<?php echo $comment['id'] ?></span>
			</h2>
			<?php if ($template['issue_opened'] && ($comment['reporter_nick'] == $template['user'] || $template['user_is_coordinator'])): ?> 
				<a class="bez_delete_button" href="?id=bez:issue_show:<?php echo $template['issue']['id'] ?>:delete:comment:<?php echo $comment['id'] ?>"><?php echo $bezlang['delete'] ?></a>
				<a class="bds_inline_button" href="?id=bez:issue_show:<?php echo $template['issue']['id'] ?>:edit:comment:<?php echo $comment['id'] ?>#k_"><?php echo $bezlang['change'] ?></a>
			<?php endif ?>

			<?php echo $helper->wiki_parse($comment['content']) ?>
			</div>
		<?php endforeach ?>
		<?php if ($template['issue_opened'] && $template['user_editor']): ?> 
			<form action="<?php echo $template['uri'] ?>:<?php echo $template['comment_action'] ?>#k_" method="POST">
				<fieldset class="bds_form">
					<div class="row">
						<label for="content"><?php echo $bezlang['description'] ?>:</label>
						<span><textarea name="content" id="content"><?php echo $value['content'] ?></textarea></span>
					</div>
				</fieldset>
				<input type="submit" value="<?php echo $template['comment_button'] ?>">
			</form>
			<a name="k_"></a>
		<?php endif; ?>
	</div>
</div>

<!-- Causes -->
<div class="bds_block" id="bez_causes">
	<h1><?php echo $bezlang['causes'] ?> <span>(<?php echo count($template['causes']) ?>)</span></h1>
	<div class="bds_block_content">
		<?php foreach ($template['causes'] as $cause): ?>
			<a name="p<?php echo $cause['id'] ?>"></a>
			<div id="p<?php echo $cause['id'] ?>" class="cause">

			<h2>
			<?php echo $bezlang['cause_added'] ?>
			<?php echo $helper->string_time_to_now($cause['date']) ?>
			<?php echo $bezlang['by'] ?>
			<?php echo $cause['reporter'] ?>
			<span><?php echo $bezlang['cause_noun'] ?>: p<?php echo $cause['id'] ?></span>
			</h2>
			<?php if ($template['issue_opened'] && $template['user_is_coordinator']): ?> 
				<a class="bez_delete_button" href="?id=bez:issue_show:<?php echo $template['issue']['id'] ?>:delete:cause:<?php echo $cause['id'] ?>"><?php echo $bezlang['delete'] ?></a>
				<a class="bds_inline_button" href="?id=bez:issue_show:<?php echo $template['issue']['id'] ?>:edit:cause:<?php echo $cause['id'] ?>#p_"><?php echo $bezlang['change'] ?></a>
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
		<?php if ($template['issue_opened'] && $template['user_is_coordinator']): ?> 
			<form action="<?php echo $template['uri'] ?>:<?php echo $template['cause_action'] ?>#p_" method="POST">
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
			<a name="p_"></a>
		<?php endif ?>
	</div>
</div>

<!-- Tasks -->
<div class="bds_block" id="bez_tasks">
	<h1><?php echo $bezlang['tasks'] ?> <span>(<?php echo count($template['tasks']) ?>)</span></h1>
	<div class="bds_block_content">
	<?php foreach ($template['tasks'] as $task): ?>
			<a name="z<?php echo $task['id'] ?>"></a>
			<div id="z<?php echo $task['id'] ?>" class="task">

			<h2>
			<?php echo $bezlang['task_added'] ?>
			<?php echo $helper->string_time_to_now($task['date']) ?>
			<?php echo $bezlang['by'] ?>
			<?php echo $task['reporter'] ?>
			<span><?php echo $bezlang['task'] ?>: z<?php echo $task['id'] ?></span>
			</h2>
			<?php if ($template['issue_opened'] && ($template['user_is_coordinator'] || $task['executor_nick'] == $template['user'])): ?> 
				<a class="bds_inline_button" href="?id=bez:issue_show:<?php echo $template['issue']['id'] ?>:edit:task:<?php echo $task['id'] ?>#z_"><?php echo $bezlang['change'] ?></a>
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

			<?php if ($task['rejected']): ?>
				<h3><?php echo $bezlang['reason'] ?></h3>
				<?php echo $helper->wiki_parse($task['reason']) ?>
			<?php endif ?>
			</div>
	<?php endforeach ?>
	<?php if ($template['issue_opened'] && ($template['user_is_coordinator'] || strstr($template['task_action'], 'update'))): ?> 
		<form action="<?php echo $template['uri'] ?>:<?php echo $template['task_action'] ?>#z_" method="POST">
			<fieldset class="bds_form">
			<?php if ($template['issue_opened'] && $template['user_is_coordinator']): ?> 
				<?php if (count($template['causes']) == 0) : ?>
					<div class="row" >
						<div style="display:table-cell"><br><br></div>
						<label style="position: relative">
							<div class="info" style="position: absolute; left: -7em; width:45em;"><?php echo $bezlang['info_no_causes_added'] ?></div>
						</label>
					</div>
				<?php endif ?>
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
				<?php if (count($template['causes']) > 0) : ?>
					<select name="action" id="action">
					<?php foreach ($template['taskactions'] as $key => $name): ?>
						<option <?php if ($value['action'] == $key) echo 'selected' ?>
						 value="<?php echo $key ?>"><?php echo $name ?></option>
					<?php endforeach ?>
					</select>
				<?php else : ?>
					<strong>
						<?php if (isset($value['action'])): ?>
							<?php echo $template['taskactions'][$value['action']] ?>
						<?php else : ?>
							<?php echo $template['taskactions'][0] ?>
						<?php endif ?>
					</strong>
				<?php endif ?>
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
				<?php endif ?>
				<?php if (strstr($template['task_action'], 'update')): ?>
					<div class="row">
					<label for="task_state"><?php echo $bezlang['task_state'] ?>:</label>
					<span>
					<select name="state" id="task_state">
					<?php foreach ($template['task_states'] as $code => $name): ?>
						<option <?php if ($value['state'] == $code) echo 'selected' ?>
						 value="<?php echo $code?>"><?php echo $name ?></option>
					<?php endforeach ?>
					</select>
					</span>
					</div>
					<div class="row">
						<label for="reason"><?php echo $bezlang['reason'] ?>:</label>
						<span><textarea name="reason" id="reason"><?php echo $value['reason'] ?></textarea></span>
					</div>
				<?php endif ?>
			</fieldset>
			<input type="submit" value="<?php echo $template['task_button'] ?>">
		</form>
	<?php endif ?>
	<a name="z_"></a>
	</div>
	<?php if ($template['closed']): ?>
		<div class="bds_block" id="bds_closed">
			<div class="info">
				<?php echo $template['closed_com'] ?>
			</div>
	<?php endif ?>
		</div>
</div>

<div id="bez_removal_confirm" style="display:none;">
	<?php echo $bezlang['do_you_want_remove'] ?>
	<input type="button" class="yes" value="<?php echo $bezlang['yes'] ?>" />
	<a href="#" class="no bez_delete_button bez_link_button"><?php echo $bezlang['no'] ?></a>
</div>
