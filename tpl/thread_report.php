<?php /* @var \dokuwiki\plugin\bez\meta\Tpl $tpl */ ?>
<div class="bds_block">

<?php if ($tpl->get('thread')): ?>
    <?php $url = $tpl->url('thread_report', 'action', 'update', 'id', $tpl->get('thread')->id) ?>
<?php else: ?>
    <?php $url = $tpl->url('thread_report', 'action', 'add') ?>
<?php endif ?>

<?php if ($tpl->get('thread')): ?>
    <?php $type = $tpl->get('thread')->type ?>
<?php else: ?>
    <?php $type = $tpl->param('type') ?>
<?php endif ?>

<form class="bez_form" action="<?php echo $url ?>" method="POST">

<?php if ($type == 'project'): ?>
    <input type="hidden" name="type" value="project" />
<?php else: ?>
    <input type="hidden" name="type" value="issue" />
<?php endif ?>

<input type="hidden" name="id" value="bez:threads">

<?php $class = 'prNone'; ?>
<?php if ($tpl->get('thread')): ?>
    <?php $class = 'pr' . $tpl->get('thread')->priority ?>
<?php endif ?>
<fieldset id="bds_issue_box"  class="bds_form <?php echo $class ?>">

<?php if ($tpl->param('action') == 'edit'): ?>
<div class="row">
<label for="id"><?php echo $tpl->getLang('id') ?>:</label>
<span><strong>#<?php echo $tpl->get('thread')->id ?></strong></span>
</div>
<?php endif ?>
    
<?php if ($type != 'project' &&
    $tpl->acl($tpl->get('thread', 'thread'), 'label_id') >= BEZ_PERMISSION_CHANGE): ?>
<div class="row">
<label for="label_id"><?php echo $tpl->getLang('type') ?>:</label>
<span>
<select name="label_id" id="label_id">
<option <?php if ($tpl->value('label_id') == '') echo 'selected' ?>
	value="">--- <?php echo $tpl->getLang('issue_type_no_specified') ?> ---</option>
<?php foreach ($tpl->get('labels') as $label): ?>
	<option <?php if ($tpl->value('label_id') == $label->id) echo 'selected' ?>
	 value="<?php echo $label->id ?>"><?php echo $label->name ?></option>
<?php endforeach ?>
</select>
</span>
</div>
<?php endif ?>
    
<?php if ($tpl->acl($tpl->get('thread', 'thread'), 'coordinator') >= BEZ_PERMISSION_CHANGE): ?>
<div class="row">
<label for="coordinator"><?php echo $tpl->getLang('coordinator') ?>:</label>
<span>
<select name="coordinator" id="coordinator" data-validation="required">
    <option value="">--- <?php echo $tpl->getLang('select') ?>---</option>
<?php foreach ($tpl->get('users') as $nick => $name): ?>
	<option <?php if ($tpl->value('coordinator') == $nick) echo 'selected' ?>
	 value="<?php echo $nick ?>"><?php echo $name ?></option>
<?php endforeach ?>
</select>
</span>
</div>
<?php endif ?>

<div class="row">
<label for="title"><?php echo $tpl->getLang('title') ?>:</label>
<span>
<input name="title" id="title" value="<?php echo $tpl->value('title') ?>" data-validation="required">
</span>
</div>

<div class="row">
<label for="content"><?php echo $tpl->getLang('description') ?>:</label>
<span>
	<div class="bez_description_toolbar"></div>
	<textarea name="content" id="content" class="edit" data-validation="required"><?php echo $tpl->value('content') ?></textarea>
</span>
</div>

<?php if ($tpl->acl($tpl->get('thread', 'thread'), 'private') >= BEZ_PERMISSION_CHANGE): ?>
    <div class="row">
        <label for="private"><?php echo $tpl->getLang('private') ?>:</label>
        <span><input <?php if ($tpl->value('private') == '1') echo 'checked' ?>
                    type="checkbox" name="private" value="1" id="private" /></span>
    </div>
<?php endif ?>

<div class="row">
    <label></label>
    <span style="padding-top:0px;">
        <input type="submit" value="<?php echo $tpl->getLang('save') ?>">&nbsp;&nbsp;

        <?php if ($tpl->get('thread')): ?>
            <?php $url = $tpl->url('thread', 'id', $tpl->get('thread')->id) ?>
        <?php else: ?>
            <?php $url = $tpl->url('threads') ?>
        <?php endif ?>

        <a href="<?php echo $url ?>" class="bez_delete_button bez_link_button">
            <?php echo $tpl->getLang('cancel')?>
        </a>
    </span>
</div>
</fieldset>

</form>
</div>
