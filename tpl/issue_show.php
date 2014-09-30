			<div id="bds_issue_box">
			<h1>
			<?php echo $helper->html_issue_link($value['id']) ?>
			 
			<?php echo $value['type'] ?>
			 (
			<?php echo $value['state'] ?>
			)
			</h1>

			<h1>
			[
			<?php echo $value['entity'] ?>
			] 
			<?php echo $value['title'] ?>
			</h1>

			<div class="time_box">
			<span>
			<?php echo $bezlang['opened_for'] ?>
			: 
			<?php $value['date'] ?>
			</span>
			<span>
			<?php if ($value['moddate'] != NULL): ?>
				<?php if ($template['closed']): ?>
					<?php echo $bezlang['closed'] ?>
				<?php else: ?>
					<?php echo $bezlang['last_modified'] ?>
				<?php endif ?>
				: 
				<?php echo $helper->string_time_to_now($value['last_mod_date']) ?>
				</span>
			<?php endif ?>
			</div>

			<table>
			<tr>

			<th><?php echo $bezlang['reporter'] ?></th>

			<td><?php echo $value['reporter'] ?></td>

			<th><?php echo $bezlang['coordinator'] ?></th>
			<td><?php echo $value['coordinator'] ?></td>
			</tr>
			</table>

			<h2>
			<?php echo $bezlang['description'] ?>
			</h2>

			<?php echo $value['description'] ?>

			<?php if ($template['closed']): ?>
				<h2>
				<?php echo $bezlang['opinion'] ?>
				</h2>
				<?php echo $value['opinion'] ?>
			<?php endif ?>

			<a href="?id=bez:8d:<?php echo $value['id'] ?>" class="bds_inline_button bds_report_button">
			<?php echo $bezlang['8d_report'] ?>
			</a>


			</div>

			<div class="bds_block" id="bez_comment">
			<h1><?php echo $bezlang['comments'] ?> <span>(<?php echo count($value['comments']) ?>)</span></h1>
			<div class="bds_block_content">
				<?php foreach ($value['comments'] as $comment): ?>
					<a name="bez_comment_<?php echo $comment['id'] ?>"></a>
					<div id="<?php echo $event['id'] ?>" class="comment">

					<h2>
					<?php echo $bezlang['comment_added'] ?>
					 
					<?php echo $comment['date'] ?>
					 
					<?php echo $bezlang['by'] ?>
					 
					<?php echo $comment['author'] ?>
					<span>
							<?php echo $bezlang['comment_noun'] ?>
					: 
					<?php echo $event['id'] ?>
					</span>
					</h2>
					<?php if ( ! $template['closed']): ?> 
						<a class="bds_inline_button" href="?id=bez:bds_issue_show:<?php echo $value['_id'] ?>:event_id:<?php echo $event['id'] ?>#<?php echo $event['type'] ?>_form"><?php echo $bezlang['change'] ?></a>
					<?php endif ?>
					<?php echo $comment['content'] ?>
					</div>
			<?php endforeach ?>

		<a name="bez_comment"></a>
		<form action="#bez_comment" method="POST">
		<input type="hidden" name="event" value="comment">
		<fieldset class="bds_form">
			<div class="row">
			<label for="content"><?php echo $bezlang['description'] ?>:</label>
			<span>
			<textarea name="content" id="content"><?php echo $value['content'] ?></textarea>
			</span>
			</div>
		</fieldset>
		<input type="submit" value="<?php echo $template['comment_button'] ?>">
		</form>
	</div>
			
