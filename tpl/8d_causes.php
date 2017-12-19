
<table>
    <?php foreach ($tpl->get('causes') as $cause): ?>
        <tr>
        <td>
            <a href="<?php echo $tpl->url('thread', 'id', $cause->thread_id) ?>#k<?php echo $cause->id ?>">
                    #p<?php echo $cause->id ?>
            </a>
        </td>
        <td>
        <?php echo $cause->content_html ?>
        </td>
        </tr>
    <?php endforeach ?>
</table>
