<h1><?php echo $bezlang['bds_timeline'] ?></h1>
	<dl id="bds_timeline">

			$class = $cursor['type'];
			if (isset($cursor['info']['new']) && isset($cursor['info']['new']['state'])) {
				if (in_array($cursor['info']['new']['state'], $this->blocking_states)) {
					$class = 'issue_closed
				} else {
					$class = 'issue_created
				}
			} elseif ($cursor['type'] == 'task_rev') {
				if ($cursor['info']['state'] != $cursor['info']['old']['state']) {
					if ($cursor['info']['state'] != 0) {
						$class = 'task_closed
					} else {
						$class = 'task
					}
				}
			}
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
			<?php if ($elm['class'] == 'issue_created'): ?>
				<a href="<?php echo $helper->issue_uri($elm['id']) ?>">
					<span class="time"><?php echo date('H:i', $elm['date']) ?></span>
						<?php echo $bezlang['issue_created'] ?>
						<span class="id">#<?php echo $elm['id'] ?></span>
						(<?php echo $elm['title'] ?>)
						<?php echo $bezlang['by'] ?>
						<span class="author"><?php echo $elm['reporter'] ?></span>
				</a>
			<?php elseif ($elm['class'] == 'issue_closed'): ?>
				<a href="<?php echo $helper->issue_uri($elm['id']) ?>">
					<span class="time"><?php echo date('H:i', $elm['date']) ?></span>
						<?php echo $bezlang['issue_closed'] ?>
						<span class="id">#<?php echo $elm['id'] ?></span>
						(<?php echo $elm['title'] ?>)
						<?php echo $bezlang['by'] ?>
						<span class="author"><?php echo $elm['reporter'] ?></span>
				</a>
			<?php endif ?>
		</dt>
		<dd>
			<?php if ($elm['class'] == 'issue_created'): ?>
				<?php echo $helper->wiki_parse($elm['description']) ?>
			<?php endif ?>
		</dd>
	<?php endforeach ?>
<?php endforeach ?>

			$aid = explode(':', $id);
			switch($cursor['type']) {
				case 'comment':
				case 'change':
				case 'task':
					<a href="'.$this->string_issue_href($aid[0], $aid[1]).'">
				break;
				case 'comment_rev':
				case 'task_rev':
					if ($aid[2] == -1) {
						<a href="'.$this->string_issue_href($aid[0], $aid[1]).'">
					} else {
						<a href="'.$this->string_issue_href($aid[0], $aid[1], $aid[2]).'">
					}
				break;
				case 'issue_created':
					<a href="'.$this->string_issue_href($aid[0]).'">
				break;
				case 'change':
					<a href="'.$this->string_issue_href($aid[0]).'">
				break;
				default:
					<a href="#">
				break;
			}
			<span class="time">
			echo date('H:i', $cursor['date']);
			</span>
			 
			switch($cursor['type']) {
				case 'comment':
				case 'task':
					if ($cursor['type'] == 'task') {
						echo $this->getLang('task_added');
					} else {
						echo $this->getLang('comment_added');
					}
					 
					<span class="id">
					#'.$aid[0].':'.$aid[1];
					</span>
					 
					(
					echo $cursor['title'];
					)
					if ($cursor['type'] == 'task') {
						 
						echo $this->getLang('task_for');
						 
						<span class="author">
						echo $this->string_format_field('name', $cursor['info']['executor']);
						</span>
					}
					 
					echo $this->getLang('by');
					 
					<span class="author">
					echo $this->string_format_field('name', $cursor['author']);
					</span>
					</a>
					</dt>
					<dd>
					echo $this->wiki_parse($cursor['info']['content']);
					</dd>

				break;
				case 'change':
				case 'change_state':
					$state = $cursor['info']['new']['state']; 
					if (isset($state)) {
						$diff = array('opinion');
						if (in_array($state, $this->blocking_states)) {
							echo $this->getLang('issue_closed');
						} else {
							echo $this->getLang('issue_reopened');
						}
					} else {
						$diff = array();
						echo $this->getLang('change_made');
					}
					 
					<span class="id">
					#'.$aid[0].':'.$aid[1];
					</span>
					 
					(
					echo $cursor['title'];
					)
					 
					echo $this->getLang('by');
					 
					<span class="author">
					echo $this->string_format_field('name', $cursor['author']);
					</span>
					</a>
					</dt>
					<dd>
					echo $this->html_format_change($cursor['info']['new'], $cursor['info']['prev'], $diff);
					if (isset($cursor['info']['new']['state'])) {
						echo $this->wiki_parse($cursor['info']['new']['opinion']);
					}
					</dd>
				break;
				case 'comment_rev':
				case 'task_rev':
					if ($cursor['type'] == 'task_rev') {
						if ($cursor['info']['state'] != $cursor['info']['old']['state']) {
							if ($cursor['info']['state'] == 0) {
								echo $this->getLang('task_reopened');
							} else if ($cursor['info']['state'] == 1) {
								echo $this->getLang('task_closed');
							} else if ($cursor['info']['state'] == 2) {
								echo $this->getLang('task_rejected');
							}
						} else {
							echo $this->getLang('task_changed');
						}
					} else {
						echo $this->getLang('comment_changed');
					}
					 
					<span class="id">
					#'.$aid[0].':'.$aid[1];
					</span>
					 
					(
					echo $cursor['title'];
					)
					 
					echo lcfirst($this->getLang('version'));
					 
					<span class="id">
					echo $cursor['info']['rev_len'] - $aid[2];
					</span>
					 
					echo $this->getLang('by');
					 
					<span class="author">
					echo $this->string_format_field('name', $cursor['author']);
					</span>
					</a>
					</dt>
					<dd>
					if ($cursor['type'] == 'task_rev' && isset($cursor['info']['old'])) {
						//remoev unchanged field
						$new = array();
						foreach ($cursor['info']['old'] as $k => $v) {
							if (isset($cursor['info'][$k])) {
								$new[$k] = $cursor['info'][$k];
							}
						}

						echo $this->html_format_change($new, $cursor['info']['old'], array('reason'), 'task');
						echo $this->wiki_parse($cursor['info']['reason']);
					} else {
						echo $this->wiki_parse($cursor['info']['content']);
					}
					</dd>
				break;
				case 'issue_created':
					echo $this->getLang('issue_created');
					 
					<span class="id">
					#'.$aid[0];
					</span>
					 
					(
					echo $cursor['title'];
					)
					 
					echo $this->getLang('by');
					 
					<span class="author">
					echo $this->string_format_field('name', $cursor['author']);
					</span>

					</a>
					</dt>
					<dd>
					echo $this->wiki_parse($cursor['info']['description']);
					</dd>
				break;
				case 'change':
				break;
				default:
					</a>
					</dt>
				break;
			}
		}
		</dl>
