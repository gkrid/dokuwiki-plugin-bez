
<?php include "issue_box.php" ?>

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
			<?php echo $helper->wiki_parse($comment['content']) ?>

			<?php if ($template['issue'][raw_state] == 0 &&
						($comment['reporter_nick'] == $INFO['client'] ||
						$helper->user_coordinator($template[issue][id]))): ?> 
				<div class="bez_buttons">
					<a class="bds_inline_button"
					href="?id=<?php echo $this->id('issue', 'id', $template['issue']['id'], 'action', 'comment_edit', 'kid', $comment['id']) ?>#k_">
						<?php echo $bezlang['change'] ?>
					</a>
					<a class="bez_delete_button"
					href="?id=<?php echo $this->id('issue', 'id', $template['issue']['id'], 'action', 'comment_delete', 'kid', $comment['id']) ?>">
						<?php echo $bezlang['delete'] ?>
					</a>
				</div>
			<?php endif ?>
		</div>
		<?php endforeach ?>
		<?php if ($template['issue'][raw_state] == 0 && $helper->user_editor()): ?> 
			<form action="?id=<?php echo $this->id('issue', 'id', $template[issue][id], 'action', $template['comment_action']) ?>#k_" method="POST">
				<fieldset class="bds_form">
					<?php if (strpos($template['comment_action'], 'comment_update') === 0): ?>
						<div class="row">
						<label for="id"><?php echo $bezlang['id'] ?>:</label>
						<span><strong>#k<?php echo $template['comment_id'] ?></strong></span>
						</div>
					<?php endif ?>
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

<?php include "removal_confirm.php" ?>

<?php if ($template['is_proposal']): ?> 
	<div class="info"><?php echo $bezlang['issue_is_proposal'] ?></div>
<?php endif ?>
