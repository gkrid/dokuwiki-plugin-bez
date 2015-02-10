<?php if (isset($template['confirm'])): ?>
	<div class="success"><?php echo $bezlang['entities_confirm'] ?></div>
<?php endif ?>
<form method="post" action="<?php echo $template['uri'] ?>:save" id="entities_form">
	<label for="entities"><?php echo $bezlang['entities'] ?>:</label>
	<textarea id="entities" name="entities"><?php echo $value['entities'] ?></textarea>
	<input type="submit" value="<?php echo $bezlang['save']; ?>" />
	<input type="button" value="<?php echo $bezlang['sort'] ?>" />
	<input type="reset" value="<?php echo $bezlang['cancel'] ?>" />
</form>
