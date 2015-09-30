<h1>
	<?php echo $bezlang['rr_report'] ?>
	<span id="bez_8d_send_button">[<a href="
		<?php echo $helper->mailto($template['issue']['coordinator_email'],
		$bezlang['rr_report'].': #'.$template['issue']['id'].' '.$template['issue']['title'],
		$template['uri']) ?>">
		✉ <?php echo $bezlang['send_mail'] ?>
	</a>]</span>
</h1>

<h2><?php echo $bezlang['rr_desc'] ?></h2>
<?php echo  $template['issue']['description'] ?>

<h2><?php echo $bezlang['rr_team'] ?></h2>
<ul>
	<?php foreach($template['team'] as $user): ?>
		<li><?php echo  $user ?></li>
	<?php endforeach ?>
</ul>

<h2><?php echo $bezlang['rr_eval'] ?></h2>

<?php var_dump($temlate[tasks]) ?>

<h2><?php echo $bezlang['rr_suceval'] ?></h2>


