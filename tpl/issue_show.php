<div id="bds_issue_box" class="pr<?php echo $template['issue']['priority'] ?>">
<h1>
<?php echo $this->html_issue_link($template['issue']['id']) ?>
<?php echo $template['issue']['type'] ?> (<?php echo $template['issue']['state'] ?>)
</h1>

<h1><?php echo $template['issue']['title'] ?></h1>

<div class="bez_timebox">
<span><strong><?php echo $bezlang['open'] ?>:</strong> <?php echo $helper->time2date($template['issue']['date']) ?></span>
<?php if ($template['issue']['last_mod'] != NULL): ?>
	<span>
	<strong>
	<?php if ($template['closed']): ?>
		<?php echo $bezlang['closed'] ?>:
	<?php else: ?>
		<?php echo $bezlang['last_modified'] ?>:
	<?php endif ?> 
	</strong>
	<?php echo $helper->time2date($template['issue']['last_mod']) ?>
	</span>
<?php endif ?>
</div>

<table>
<tr>
<th><?php echo $bezlang['reporter'] ?>:</th>
<td><?php echo $template['issue']['reporter'] ?></td>
<th><?php echo $bezlang['coordinator'] ?>:</th>
<td><?php echo $template['issue']['coordinator'] ?></td>
</tr>
</table>

<h2><?php echo $bezlang['description'] ?></h2>

<?php echo $template['issue']['description'] ?>

<?php if ($template['display_opinion']): ?>
	<h2><?php echo $bezlang['opinion'] ?></h2>
	<?php echo $template['issue']['opinion'] ?>
<?php endif ?>
<a href="<?php echo $template['8d_link'] ?>" class="bds_inline_button bds_report_button">
	<?php echo $bezlang['8d_report'] ?>
</a>

<a class="bds_inline_button bds_send_button" href="
	<?php echo $helper->mailto($template['issue']['coordinator_email'],
	$bezlang['issue'].': #'.$template['issue']['id'].' '.$template['issue']['title'],
	$template['uri']) ?>">
	✉ <?php echo $bezlang['send_mail'] ?>
</a>

<?php if ($template['user_is_coordinator']): ?> 
	<a href="?id=<?php echo $this->id('issue_report', $template['issue']['id']) ?>" class="bds_inline_button bds_edit_button">
		<?php echo $bezlang['edit'] ?>
	</a>
<?php endif ?>

</div>

<!-- Comments -->
<div class="bds_block bez_standard_block" id="bez_comments">
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
				<a class="bez_delete_button"
				href="?id=<?php echo $this->id('issue_show', $template['issue']['id'], 'delete', 'comment', $comment['id']) ?>">
					<?php echo $bezlang['delete'] ?>
				</a>
				<a class="bds_inline_button"
				href="?id=<?php echo $this->id('issue_show', $template['issue']['id'], 'edit', 'comment', $comment['id']) ?>#k_">
					<?php echo $bezlang['change'] ?>
				</a>
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
				<a href="?id=<?php echo $this->id('issue_show', $template['issue']['id']) ?>"
				 class="bez_delete_button bez_link_button">
					<?php echo $bezlang['cancel'] ?>
				</a>
			</form>
			<a name="k_"></a>
		<?php endif; ?>
	</div>
</div>

<?php if ($template['is_proposal']): ?> 
	<div class="info"><?php echo $bezlang['issue_is_proposal'] ?></div>
<?php else: ?>

<!-- Tasks -->
<div class="bds_block" id="bez_tasks">
	<h1><?php echo $bezlang['correction_h'] ?> <span>(<?php echo count($template['tasks']) ?>)</span></h1>
	<div class="bds_block_content">
		<?php $params = array('tasks' => $template['tasks'], 'task_action' => $template['task_action'], 'hidden' => array()) ?>
		<?php include "task.php" ?>
	<a name="z_"></a>
	</div>
<?php endif ?>

	<?php if ($template['closed']): ?>
		<div class="bds_block" id="bds_closed">
			<div class="info">
				<?php echo $template['closed_com'] ?>
			</div>
	<?php endif ?>
		</div>

<!-- Causes -->
<div class="bds_block bez_standard_block" id="bez_causes">
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
				<a class="bez_delete_button"
				href="?id=<?php echo $this->id('issue_show', $template['issue']['id'], 'delete', 'cause', $cause['id']) ?>">
					<?php echo $bezlang['delete'] ?>
				</a>
				<a class="bds_inline_button"
				href="?id=<?php echo $this->id('issue_show', $template['issue']['id'], 'edit', 'cause', $cause['id']) ?>#p_">
					<?php echo $bezlang['change'] ?>
				</a>
			<?php endif ?>
			<div class="root_cause">
			<span>
			<?php echo lcfirst($bezlang['root_cause']) ?>:
			<strong><?php echo $cause['rootcause'] ?></strong>
			</span>
			</div>
			<?php echo $helper->wiki_parse($cause['cause']) ?>
			
			<h2><?php echo $bezlang['tasks'] ?></h2>
			<div id="bez_tasks">
				<?php $params = array('tasks' => $cause['tasks'], 'task_action' => $template['task_action'],
										'hidden' => array('cause' => $cause['id'])) ?>
				<?php include "task.php" ?>
			</div>

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
				<a href="?id=<?php echo $this->id('issue_show', $template['issue']['id']) ?>"
				 class="bez_delete_button bez_link_button">
					<?php echo $bezlang['cancel'] ?>
				</a>
			</form>
			<a name="p_"></a>
		<?php endif ?>
	</div>
</div>
</div>


<div id="bez_removal_confirm" style="display:none;">
	<?php echo $bezlang['do_you_want_remove'] ?>
	<input type="button" class="yes" value="<?php echo $bezlang['yes'] ?>" />
	<a href="#" class="no bez_delete_button bez_link_button"><?php echo $bezlang['no'] ?></a>
</div>
