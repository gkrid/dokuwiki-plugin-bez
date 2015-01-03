<h1><?php echo $bezlang['bds_timeline'] ?></h1>

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
		<dt class="<?php echo $elm['class'] ?>">
			<?php if (strstr($elm['class'], 'issue')): ?>
				<a href="<?php echo $helper->issue_uri($elm['id']) ?>">
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
						[<?php echo $elm['entity'] ?>] <?php echo $elm['title'] ?>
						<span class="author"><?php echo $bezlang['coordinator'] ?>: <strong><?php echo $elm['coordinator'] ?></strong></span>
				</a>
			<?php elseif (strstr($elm['class'], 'task')): ?>
				<a href="<?php echo $helper->issue_uri($elm['issue']) ?>#z<?php echo $elm['id'] ?>">
					<span class="time"><?php echo date('H:i', $elm['date']) ?></span>
						<span class="id">#<?php echo $elm['issue'] ?>:z<?php echo $elm['id'] ?></span>
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
			<?php endif ?>
		</dt>
		<dd>
			<?php if (strstr($elm['class'], 'issue')): ?>
				<?php echo $helper->wiki_parse($elm['description']) ?>
				<?php if ($elm['class'] == 'issue_closed'): ?>
					<h3><?php echo $bezlang['opinion'] ?></h3>
					<?php echo $helper->wiki_parse($elm['opinion']) ?>
				<?php endif ?>
			<?php elseif (strstr($elm['class'], 'task')): ?>
				<?php echo $helper->wiki_parse($elm['task']) ?>
				<?php if ($elm['class'] == 'task_rejected'): ?>
					<h3><?php echo $bezlang['reason'] ?></h3>
					<?php echo $helper->wiki_parse($elm['reason']) ?>
				<?php endif ?>
			<?php endif ?>
		</dd>
	<?php endforeach ?>
<?php endforeach ?>
</dl>
