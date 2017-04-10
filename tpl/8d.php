<h1>
	<?php echo $bezlang['8d_report'] ?>
	<span id="bez_8d_send_button">[<a href="
		<?php echo $helper->mailto('',
		$bezlang['8d_report'].': #'.$template['issue']->id.' '.$template['issue']->title,
		$template['uri']) ?>">
		✉ <?php echo $bezlang['send_mail'] ?>
	</a>]</span>
</h1>

<table>
<tr>
	<td>
		 <strong>
		 	<a href="?id=<?php echo $this->id('issue', 'id', $template['issue']->id) ?>">
				#<?php echo  $template['issue']->id ?>
			</a>
		</strong>
		<?php echo  ucfirst($template['issue']->type_string) ?>
	</td>

	<td>
		<strong><?php echo $bezlang['open_date'] ?>:</strong>
		<?php echo  $helper->time2date($template['issue']->date) ?>
	</td>
</tr>

	<tr>
	<td colspan="2">
		<strong><?php echo $bezlang['title'] ?>:</strong>
		<?php echo  $template['issue']->title ?>
	</td>
</tr>
</table>
<h2><?php echo $bezlang['1d'] ?></h2>
<ul>
	<?php foreach($template['issue']->get_participants() as $participant): ?>
		<li><?php echo  $participant ?></li>
	<?php endforeach ?>
</ul>

<h2><?php echo $bezlang['2d'] ?></h2>
<?php echo  $template['issue']->description_cache ?>

<h2><?php echo $bezlang['3d'] ?></h2>
<?php $tasks = $template['tasks']['3d'] ?>
<?php include '8d_tasks.php' ?>

<h2><?php echo $bezlang['4d'] ?></h2>	
<?php $causes = $template['real_causes'] ?>
<?php include '8d_causes.php' ?>

<h2><?php echo $bezlang['5d'] ?></h2>	
<?php $tasks = $template['tasks']['5d'] ?>
<?php include '8d_tasks.php' ?>

<h2><?php echo $bezlang['6d'] ?></h2>	
<?php $causes = $template['potential_causes'] ?>
<?php include '8d_causes.php' ?>

<h2><?php echo $bezlang['7d'] ?></h2>	
<?php $tasks = $template['tasks']['7d'] ?>
<?php include '8d_tasks.php' ?>

<h2><?php echo $bezlang['8d'] ?></h2>
<?php if ($template['issue']->state !== '0'): ?>
	<?php echo  $template['issue']->opinion_cache ?>
    <table>
    <tr>
        <td>
            <strong><?php echo $bezlang['true_date'] ?>:</strong>
            <?php echo  $helper->time2date($template['issue']->last_mod) ?>
        </td>
        <td>
            <strong><?php echo $bezlang['state'] ?>:</strong>
            <?php echo $template['issue']->state_string ?>
        </td>
    </tr>

    <tr>
        <td>
            <strong><?php echo $bezlang['totalcost'] ?>:</strong>
            <?php if ($template['total_cost'] !== NULL): ?>
                <?php echo $template['total_cost'] ?>
            <?php else: ?>
                <em>---</em>
            <?php endif ?>
        </td>
        <td>
            <strong><?php echo $bezlang['coordinator'] ?>:</strong>
            <?php echo $this->model->users->get_user_full_name($template['issue']->coordinator) ?>
        </td>
    </tr>
    </table>
<?php else: ?>
	<p><i><?php echo $bezlang['not_relevant'] ?></i></p>
<?php endif ?>


