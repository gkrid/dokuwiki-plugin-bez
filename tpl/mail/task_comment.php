<?php /* @var \dokuwiki\plugin\bez\meta\Tpl $tpl */ ?>
<html>
<head>
    <title><?php echo $tpl->get('wiki_title') ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<style type="text/css">
    body {
        font: normal 87.5%/1.4 Arial, sans-serif;
    }
    <?php echo $tpl->get('style') ?>
</style>
<body>

<h1 style="padding-bottom: 5px; margin-bottom: 5px; border-bottom: 1px solid #E5E5E5;">
    <?php echo $tpl->get('wiki_title') ?>
</h1>

<p>
    Użytkownik <?php echo $tpl->user_name($tpl->get('who')) ?> <?php echo $tpl->getLang($tpl->get('action')) ?>
    <a href="<?php echo $tpl->url('task', 'tid', $tpl->get('task')->id) ?>">
        #z<?php echo $tpl->get('task')->id ?>
    </a>.
</p>

<div style="border: 1px solid <?php echo $tpl->get('action_border_color', '#E5E5E5') ?>; background-color: <?php echo $tpl->get('action_background_color', 'transparent') ?>;">
    <div style="margin: 5px;">
        <div style="margin: 5px 0;">
            <strong><?php echo $tpl->user_name($tpl->get('who')) ?></strong> <br>
            <span style="color: #888"><?php echo $tpl->date($tpl->get('when')) ?></span>
        </div>
        <?php echo $tpl->get('content') ?>
    </div>
    <div style="margin-top: 5px; padding: 20px; background: #F2F2F2;">
        <a href="<?php $tpl->url('task', 'tid', $tpl->get('task')->id) ?>#k_" style="text-decoration: none; color: #444; font-weight: bold;">Dodaj komentarz</a>
    </div>
</div>

<p style="font-size:small;color:#666;">&mdash;<br />
    Otrzymujesz tą wiadomość, ponieważ jesteś subskrybentem tego problemu.<br />
    <a href="<?php echo $tpl->url('thread', 'id', $tpl->get('thread')->id) ?>">Przejdź do problemu</a>,
    albo <a href="<?php echo $tpl->url('thread', 'id', $tpl->get('thread')->id, 'action', 'unsubscribe') ?>">
        wyłącz subskrypcję
    </a>.
</p>

<?php include "footer.php" ?>

</body>
</html>
