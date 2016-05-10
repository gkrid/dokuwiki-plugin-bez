<div id="bez_version">
	<div>
		<?php echo $bezlang['bez'] ?>, <?php echo $bezlang['version'] ?>: <?php echo $template[version] ?>
	</div>
</div>
<div id="bez_info">
	<?php if ($helper->user_editor()): ?>
		<a href="?id=<?php echo $this->id('issue_report') ?>" class="bez_start_button" id="bez_report_issue_button">
			<?php echo $bezlang['report_issue'] ?>
		</a>
	<?php endif ?>
	<a href="?id=<?php echo $this->id('issues:state:0:coordinator:'.$template['client']) ?>" class="bez_start_button" id="bez_info_issues">
		<?php echo $bezlang['close_issues'] ?>:
		<strong><?php echo $template['my_issues'] ?></strong>
	</a>
	<a href="?id=<?php echo $this->id('tasks:taskstate:0:executor:'.$template['client']) ?>" class="bez_start_button" id="bez_info_tasks">
		<?php echo $bezlang['close_tasks'] ?>:
		<strong><?php echo $template['my_tasks'] ?></strong>
	</a>
	<?php if ($helper->user_admin()): ?>
		<a href="?id=<?php echo $this->id('issues:state:-proposal') ?>" class="bez_start_button" id="bez_info_proposals">
			<?php echo $bezlang['proposals'] ?>:
			<strong><?php echo $template['proposals'] ?></strong>
		</a>
	<?php endif ?>
</div>

<dl id="bds_timeline">
<?php foreach ($template['timeline'] as $day => $elms): ?>
	<h2>	
		<?php echo $helper->time2date(strtotime("-$day days")) ?><?php if ($day < 2) echo ':' ?>
		<?php if ($day == 0): ?>
			<?php echo $bezlang['today'] ?>
		<?php elseif ($day == 1): ?>
			<?php echo $bezlang['yesterday'] ?>
		<?php endif ?>
	</h2>
	<?php foreach ($elms as $elm): ?>
		<dt class="<?php echo $elm['class'] ?> pr<?php echo $elm['priority'] ?>">
			<?php if (strstr($elm['class'], 'issue')): ?>
				<a href="<?php echo $this->issue_uri($elm['id']) ?>">
					<span class="time"><?php echo date('H:i', $elm['date']) ?></span>
						<span class="id">#<?php echo $elm['id'] ?></span>
						<?php if ($elm['class'] == 'issue_created'): ?>
							<?php echo $bezlang['issue_created'] ?>
						<?php elseif ($elm['class'] == 'issue_closed'): ?>
							<?php echo $bezlang['issue_closed'] ?>
						<?php elseif ($elm['class'] == 'issue_rejected'): ?>
							<?php echo $bezlang['issue_rejected'] ?>
						<?php endif ?>
						<?php echo $elm['type'] ?>
						"<?php echo $elm['title'] ?>"
						<span class="author"><?php echo $bezlang['coordinator'] ?>: <strong><?php echo $elm['coordinator'] ?></strong></span>
				</a>
			<?php elseif (strstr($elm['class'], 'task')): ?>
				<?php if (isset($elm['issue'])): ?>
					<a href="?id=<?php echo $this->id('issue_task', 'id', $elm['issue'], 'tid', $elm['id']) ?>">
				<?php else: ?>
					<a href="?id=<?php echo $this->id('show_task', 'tid', $elm['id']) ?>">
				<?php endif ?>
					<span class="time"><?php echo date('H:i', $elm['date']) ?></span>
					<?php if (isset($elm['issue'])): ?>
						<span class="id">#<?php echo $elm['issue'] ?> #z<?php echo $elm['id'] ?></span>
					<?php else: ?>
						<span class="id">#z<?php echo $elm['id'] ?></span>
					<?php endif ?>
						<?php echo $bezlang['task'] ?>
						<?php echo lcfirst($elm['action']) ?>
						<?php if ($elm['class'] == 'task_opened'): ?>
							<?php echo lcfirst($bezlang['task_opened']) ?>
						<?php elseif ($elm['class'] == 'task_done'): ?>
							<?php echo lcfirst($bezlang['task_done']) ?>
						<?php elseif ($elm['class'] == 'task_rejected'): ?>
							<?php echo lcfirst($bezlang['task_rejected']) ?>
						<?php endif ?>
						<?php echo $elm['title'] ?>
						<span class="author"><?php echo $bezlang['executor'] ?>: <strong><?php echo $elm['executor'] ?></strong></span>
				</a>
			<?php elseif (strstr($elm['class'], 'comment')): ?>
				<a href="?id=<?php echo $this->id('issue', 'id', $elm[issue]) ?>" style="background-image:none;">
					<span class="time"><?php echo date('H:i', $elm['date']) ?></span>
					<span class="id">#<?php echo $elm['issue'] ?> #k<?php echo $elm['id'] ?></span>
					<?php echo $bezlang['comment_added'] ?>
					<?php echo $bezlang['by'] ?>
					<span class="author"><strong><?php echo $elm['reporter'] ?></strong></span>
				</a>
			<?php elseif (strstr($elm['class'], 'cause')): ?>
				<a href="?id=<?php echo $this->id('issue_cause', 'id', $elm[issue], 'cid', $elm[id]) ?>">
					<span class="time"><?php echo date('H:i', $elm['date']) ?></span>
						<span class="id">#<?php echo $elm['issue'] ?> #p<?php echo $elm['id'] ?></span>
						<?php if ($elm['potential'] == 0): ?>
							<?php echo $bezlang['cause_added'] ?>:
						<?php else: ?>
							<?php echo ucfirst($bezlang['cause_type_potential']) ?>
							<?php echo lcfirst($bezlang['cause_added']) ?>:
						<?php endif ?>
						<strong><?php echo $elm['rootcause'] ?></strong>
				</a>
			<?php endif ?>
		</dt>
		<dd>
			<?php if (strstr($elm['class'], 'issue')): ?>
				<?php echo $elm['description'] ?>
				<?php if ($elm['class'] == 'issue_closed'): ?>
					<h3><?php echo $bezlang['opinion'] ?></h3>
					<?php echo $elm['opinion'] ?>
				<?php endif ?>
			<?php elseif (strstr($elm['class'], 'task')): ?>
				<?php echo $elm['task'] ?>
				<?php if ($elm['class'] == 'task_rejected'): ?>
					<h3><?php echo $bezlang['reason'] ?></h3>
					<?php echo $elm['reason'] ?>
				<?php endif ?>
			<?php elseif (strstr($elm['class'], 'comment')): ?>
				<?php echo $elm['content'] ?>
			<?php elseif (strstr($elm['class'], 'cause')): ?>
				<?php echo $elm['cause'] ?>
			<?php endif ?>
		</dd>
	<?php endforeach ?>
<?php endforeach ?>
</dl>
