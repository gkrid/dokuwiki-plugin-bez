
<?php include "issue_box.php" ?>

<!-- Comments -->
<div class="bez_comments">
		<?php foreach ($template['comments'] as $comment): ?>
			<a name="k<?php echo $comment['id'] ?>"></a>
			<div id="k<?php echo $comment['id'] ?>" class="bez_comment">
			<div class="bez_comment
				<?php
					if ($comment['reporter'] == $this->model->users->get_user_full_name($INFO['client'])) {
						echo "bez_my_comment";
					}
				?>">
				<div class="bez_avatar">
					<img src="<?php echo DOKU_URL ?>lib/plugins/bez/images/avatar_default.png" />
				</div>
				<div class="bez_text_comment">
					<span class="bez_arrow-tip-container">
						<span class="bez_arrow-tip">
							<span class="bez_arrow-tip-grad"></span>
						</span>
					</span>
					<h2>
						<strong><?php echo $comment['reporter'] ?></strong>
						<?php echo $bezlang['comment_added'] ?>
						<?php echo $helper->string_time_to_now($comment['date']) ?>
					
					<div class="bez_comment_buttons">
					<?php if ($template['issue']['raw_state'] == 0 &&
						($comment['reporter_nick'] == $INFO['client'] ||
						$helper->user_coordinator($template['issue']['id']))): ?> 
						
						<a class="bez_comment_button"
						href="?id=<?php echo $this->id('issue', 'id', $template['issue']['id'], 'action', 'comment_edit', 'kid', $comment['id']) ?>#k_">
							<span class="bez_awesome">&#xf040;</span>
						</a>
						<a class="bez_comment_button bez_delete_prompt"
						href="?id=<?php echo $this->id('issue', 'id', $template['issue']['id'], 'action', 'comment_delete', 'kid', $comment['id']) ?>">
							<span class="bez_awesome">&#xf00d;</span>
						</a>
					<?php endif ?>
					</div>
			
					</h2>
					<div class="bez_content">
						<?php echo $helper->wiki_parse($comment['content']) ?>
					</div>
				</div>
			</div>
			


			
		</div>
		<?php endforeach ?>


<?php if ($template['issue'][raw_state] == 0 && $helper->user_editor()): ?> 
<form action="?id=<?php echo $this->id('issue', 'id', $template[issue][id], 'action', $template['comment_action']) ?>#k_" method="POST">
	<div class="bez_comment">
		<div class="bez_avatar">
			<img src="<?php echo DOKU_URL ?>lib/plugins/bez/images/avatar_default.png" />
		</div>
		<div class="bez_text_comment">
			<span class="bez_arrow-tip-container">
				<span class="bez_arrow-tip">
					<span class="bez_arrow-tip-grad"></span>
				</span>
			</span>
			<h2>
				<button class="bez_button_notify toolbuttonn">@</button>
				<span class="bez_toolbar"></span>
			</h2>
			<div class="bez_content">
				<textarea name="content" id="content"><?php echo $value['content'] ?></textarea>
				<input type="submit" value="<?php echo $template['comment_button'] ?>">
			</div>
		</div>
	</div>
</form>
<a name="k_"></a>
<?php endif ?>
</div>	

<?php if ($template['is_proposal']): ?> 
	<div class="info"><?php echo $bezlang['issue_is_proposal'] ?></div>
<?php endif ?>
