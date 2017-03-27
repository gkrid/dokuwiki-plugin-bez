<?php if (	$template['action'] === 'task_edit' &&
            $template['tid'] === $template['task']->id): ?>
    <?php include 'task_form.php' ?>
<?php else: ?>
    <?php include 'task_box.php' ?>
<?php endif ?>

<?php $template['no_edit'] = true ?>

<?php if (isset($template['commcause'])): ?>
<div class="bez_comments" style="display: block; margin-bottom: 10px">
    <?php include 'commcause_box.php' ?>
</div>
<?php endif ?>

<?php if (isset($template['issue'])): ?>
    <?php include 'issue_box.php' ?>
<?php endif ?>