<dl class="bez_timeline">
    <?php foreach ($tpl->get('timeline') as $date => $rows): ?>
        <h2>
            <?php if ($date == date('Y-m-d')): ?>
                <?php echo $date ?>: <?php echo $tpl->getLang('today') ?>
            <?php elseif ($date == date('Y-m-d', strtotime('yesterday'))): ?>
                <?php echo $date ?>: <?php echo $tpl->getLang('yesterday') ?>
            <?php else: ?>
                <?php echo $date ?>
            <?php endif ?>
        </h2>
        <?php foreach ($rows as $row): ?>
            <dt class="<?php echo $row['type'] ?>">
                <?php $pre = '<span class="time">' . $row['time'] . '</span> ' ?>
                <?php $post = sprintf($tpl->getLang('timeline ' . $row['type']),
                                      $tpl->user_name($row['author'])) ?>
                <?php $post = ' <span class="author">' . $post . '</span>' ?>
                <?php $row['entity']->html_link($pre, $post) ?>
            </dt>
            <dd>
                <?php echo $row['entity']->content_html ?>
            </dd>
        <?php endforeach ?>
    <?php endforeach ?>
</dl>