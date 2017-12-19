<?php /* @var \dokuwiki\plugin\bez\meta\Tpl $tpl */ ?>
<div class="bds_block">

<?php if ($tpl->get('thread')): ?>
    <?php $url = $tpl->url('thread_report', 'action', 'update', 'id', $tpl->get('thread')->id) ?>
<?php else: ?>
    <?php $url = $tpl->url('thread_report', 'action', 'add') ?>
<?php endif ?>

<form class="bez_form" action="<?php echo $url ?>" method="POST">

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
    
<?php if ($tpl->static_acl('thread', 'labels') >= BEZ_PERMISSION_CHANGE): ?>
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
    
<?php if ($tpl->static_acl('thread', 'coordinator') >= BEZ_PERMISSION_CHANGE): ?>
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
<?php //if ($tpl->action() == 'update'): ?>
<!--	<div class="row">-->
<!--	<label for="state">--><?php //echo $bezlang['state'] ?><!--:</label>-->
<!--	<span>-->
<!--		<strong>--><?php //echo $template['issue']->state_string ?><!--</strong>-->
<!--	</span>-->
<!--	</div>-->
<!--	--><?php //if ($template['issue']->state !== '0') : ?>
<!--		<div class="row">-->
<!--			<label for="opinion">-->
<!--				--><?php //if ($template['issue']->assigned_tasks_count > 0): ?>
<!--					--><?php //echo $bezlang['opinion'] ?><!--:-->
<!--				--><?php //else: ?>
<!--					--><?php //echo $bezlang['reason'] ?><!--:-->
<!--				--><?php //endif ?>
<!--			</label>-->
<!--			<span>-->
<!--				<div class="bez_opinion_toolbar"></div>-->
<!--				<textarea name="opinion" id="opinion" class="edit" data-validation="required">--><?php //echo $value['opinion'] ?><!--</textarea>-->
<!--			</span>-->
<!--		</div>-->
<!--	--><?php //endif ?>
<?php //endif ?>
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
