<div id="bez_issue_filter" class="bds_block">
<form action="<?php echo $template['uri'] ?>?id=bez:issue_report:<?php echo $template['issue_id'] ?>:<?php echo $template['action'] ?>" method="POST">
<fieldset class="bds_form">
	<label><?php echo $bezlang['state'] ?>:
		<select name="state">
			<option <?php if ($value['state'] == '-all') echo 'selected' ?>
				value="-all">--- <?php echo $bezlang['all'] ?> ---</option>
		<?php foreach ($template['states'] as $key => $name): ?>
			<option <?php if ($value['state'] === $key) echo 'selected' ?>
				value="<?php echo $key ?>"><?php echo $name ?></option>
		<?php endforeach ?>
		</select>
	</label>

	<label><?php echo $bezlang['type'] ?>:
		<select name="type">
			<option <?php if ($value['type'] == '-all') echo 'selected' ?>
				value="-all">--- <?php echo $bezlang['all'] ?> ---</option>
		<?php foreach ($template['issue_types'] as $key => $name): ?>
			<option <?php if ($value['type'] === $key) echo 'selected' ?>
				value="<?php echo $key ?>"><?php echo $name ?></option>
		<?php endforeach ?>
		</select>
	</label>

	<label><?php echo $bezlang['entity'] ?>:
		<select name="entity">
			<option <?php if ($value['entity'] == '-all') echo 'selected' ?>
				value="-all">--- <?php echo $bezlang['all'] ?> ---</option>
		<?php foreach ($template['entities'] as $key => $name): ?>
			<option <?php if ($value['entity'] === $key) echo 'selected' ?>
				value="<?php echo $key ?>"><?php echo $name ?></option>
		<?php endforeach ?>
		</select>
	</label>

	<label><?php echo $bezlang['year'] ?>:
		<select name="year">
			<option <?php if ($value['year'] == '-all') echo 'selected' ?>
				value="-all">--- <?php echo $bezlang['all'] ?> ---</option>
		<?php foreach ($template['years'] as $key => $name): ?>
			<option <?php if ($value['year'] === $key) echo 'selected' ?>
				value="<?php echo $key ?>"><?php echo $name ?></option>
		<?php endforeach ?>
		</select>
	</label>
	<label><input type="submit" value="<?php echo $bezlang['filter'] ?>" />

</fieldset>
</form>
</div>
