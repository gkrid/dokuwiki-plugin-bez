<div class="rr">
<h1>
	<?php echo $bezlang['rr_report'] ?>
	<span id="bez_8d_send_button">[<a href="
		<?php echo $helper->mailto($template['issue']['coordinator_email'],
		$bezlang['rr_report'].': #'.$template['issue']['id'].' '.$template['issue']['title'],
		$template['uri']) ?>">
		✉ <?php echo $bezlang['send_mail'] ?>
	</a>]</span>
</h1>

<table>
<tr>
	<td>
		 <strong>
		 	<a href="<?php echo $this->issue_uri($template[issue][id]) ?>">
				#<?php echo  $template['issue']['id'] ?>
			</a>
		</strong>
		<?php echo  ucfirst($template['issue']['type']) ?>
	</td>

	<?php if ($template['issue'][raw_state] == 0): ?>	
		<td>
			<strong><?php echo $bezlang['open_date'] ?>:</strong>
			<?php echo  $helper->time2date($template['issue']['date']) ?>
		</td>
	<?php else: ?>
		<td>
			<strong><?php echo $bezlang['close_date'] ?>: </strong>
			<?php echo $helper->time2date($template['issue']['last_mod']) ?>
		</td>
	<?php endif ?>
</tr>

	<tr>
	<td colspan="3">
		<strong><?php echo $bezlang['title'] ?>:</strong>
		<?php echo  $template['issue']['title'] ?>
	</td>
</tr>
</table>

<h2>1.  <?php echo $bezlang['rr_desc'] ?></h2>
<?php echo  $template['issue']['description'] ?>

<h2>2. <?php echo $bezlang['rr_team'] ?></h2>
<ul>
	<?php foreach($template['team'] as $user): ?>
		<li><?php echo  $user ?></li>
	<?php endforeach ?>
</ul>

<h2>3.  <?php echo $bezlang['rr_eval'] ?></h2>
<table>
	<tr>
		<th><?php echo ucfirst($bezlang[cause]) ?></th>
		<th><?php echo $bezlang[reaction] ?></th>
		<th><?php echo $bezlang[evaluation] ?></th>
	</tr>
	<?php foreach($template[causes] as $r): ?>
	<tr>
		<?php $ctask = count($template[tasks][$r[id]]) ?>
		<td rowspan="<?php echo $ctask > 0 ? $ctask : 1 ?>">
			<h3>
				<strong>
				<a href="?id=<?php echo $this->id('issue_cause', 'id', $template[issue][id], 'cid', $r[id] ) ?>">
						#p<?php echo $r[id] ?>
				</a>
				</strong>
				<?php echo $r[rootcause] ?>
			</h3>
			<?php echo $helper->wiki_parse($r[cause]) ?>
		</td>
		<?php if ($ctask > 0): ?>
			<?php $task = $template[tasks][$r[id]][0]; ?>
			<?php include "rr_task.php" ?>
		<?php else: ?>
			<td></td><td></td>
		<?php endif ?>
	</tr>
	<?php for ($i = 1; $i < $ctask; $i++): ?>
		<tr>
			<?php $task = $template[tasks][$r[id]][$i]; ?>
			<?php include "rr_task.php" ?>
		</tr>	
	<?php endfor ?>
	<?php endforeach ?>
</table>


<?php if ($template[issue][opinion] != NULL): ?>
	<h2>4.  <?php echo $bezlang['rr_suceval'] ?></h2>
		<?php echo $template[issue][opinion] ?>


<?php endif ?>

</div>
