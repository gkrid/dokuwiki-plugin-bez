<table>
    <tr>
        <th>Nr</th>
        <th>Zgłoszone</th>
        <th>Plan</th>
        <th>Opis</th>
    </tr>
    <?php foreach ($tpl->get('tasks') as $task): ?>
        <?php
        switch($task->priority) {
            case '2':
                $color = "#F8E8E8";
                break;
            case '1':
                $color = "#ffd";
                break;
            case '0':
                $color = "#EEF6F0";
                break;
        }
        ?>
        <tr style="background-color: <?php echo $color ?>">
            <td><a href="<?php echo $tpl->url('task', 'tid', $task->id) ?>">
                    #z<?php echo $task->id ?>
                </a></td>
            <td><?php echo $tpl->date($task->create_date) ?></td>
            <td>
                <?php echo $task->plan_date ?>
                <?php if ($task->all_day_event == '0'): ?>
                    <?php echo $task->start_time ?>&nbsp;-&nbsp;<?php echo $task->finish_time ?>
                <?php endif ?>
            </td>
            <td><?php echo $task->content_html ?></td>
        </tr>
    <?php endforeach ?>
</table>