<form method="post" action="<?php echo $template['uri'] ?>:save" id="entities_form">
	<label for="entities"><?php echo $bezlang['entities'] ?>:</label>
	<textarea id="entities" name="entities"><?php echo $template['entities'] ?></textarea>
	<input type="submit" value="<?php echo $bezlang['save']; ?>" />
	<input type="button" value="<?php echo $bezlang['sort'] ?>" />
	<input type="reset" value="<?php echo $bezlang['cancel'] ?>" />
</form>
