<?php if (count($tasks) > 0): ?>
    <table>
    <tr>
        <th><?php echo $bezlang['id'] ?></th>
        <th><?php echo $bezlang['task'] ?></th>
        <th><?php echo $bezlang['state'] ?></th>
        <th><?php echo $bezlang['cost'] ?></th>
        <th><?php echo $bezlang['plan_date'] ?></th>
        <th><?php echo $bezlang['closed'] ?></th>
    </tr>
    <?php foreach($tasks as $task): ?>
        <tr>
            <td>	
                <a href="?id=<?php echo $this->id('task', 'tid', $task->id) ?>">
                    #z<?php echo $task->id ?>
                </a>
            </td>
            <td>
                <?php echo $task->task_cache ?>

                <?php if ($task->state !== '0'): ?>
                    <h3 class="bez_8d"><?php echo $bezlang->evaluation ?></h3>
                    <?php echo  $task->reason_cache ?>
                <?php endif ?>
            </td>
            <td><?php echo $task->state_string ?></td>
            <td>
                <?php if ($task->cost == ''): ?>
                    <em>---</em>
                <?php else: ?>
                    <?php echo $task->cost ?>
                <?php endif ?>
            </td>
            <td><?php echo $task->plan_date ?></td>
            <td>
                <?php if ($task->state === '0'): ?>
                    <em>---</em>
                <?php else: ?>
                    <?php echo $helper->time2date($task->close_date) ?>
                <?php endif ?>
            </td>
        </tr>
    <?php endforeach ?>
    </table>
<?php else: ?>
    <p><i><?php echo $bezlang['not_relevant'] ?></i></p>
<?php endif ?>