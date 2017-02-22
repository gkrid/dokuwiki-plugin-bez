<?php $task = $template['task'] ?>
<?php include 'task_box.php' ?>

<?php $template['no_edit'] = true ?>

<?php if (isset($template['commcause'])): ?>
<div class="bez_comments" style="display: block; margin-bottom: 10px">
    <?php include 'commcause_box.php' ?>
</div>
<?php endif ?>

<?php if (isset($template['issue'])): ?>
    <?php include 'issue_box.php' ?>
<?php endif ?>