<h1><?php echo $tpl->getLang('report') ?></h1>

<div class="bez_filter_form">
    <form action="<?php echo $tpl->url('report') ?>" method="post">
        <span class="datepair">
            <label><?php echo $tpl->getLang('report from') ?>: <input name="from" value="<?php echo $tpl->value('from') ?>" class="date start" style="width: 90px"></label>
            <label><?php echo $tpl->getLang('report to') ?>: <input name="to" value="<?php echo $tpl->value('to') ?>" class="date end" style="width: 90px"></label>
        </span>
        <button><?php echo $tpl->getLang('show') ?></button>
    </form>

</div>

<h2><?php echo $tpl->getLang('issues') ?></h2>

<table class="bez_sumarise">
    <tr>
        <th><?php echo $tpl->getLang('type') ?></th>
        <th><?php echo ucfirst($tpl->getLang('proposal')) ?></th>
        <th><?php echo ucfirst($tpl->getLang('open')) ?></th>
        <th><?php echo ucfirst($tpl->getLang('report threads done')) ?></th>
        <th><?php echo ucfirst($tpl->getLang('closed')) ?></th>
        <th><?php echo ucfirst($tpl->getLang('report threads rejected')) ?></th>
        <th><?php echo ucfirst($tpl->getLang('all')) ?></th>
    </tr>
    <?php foreach ($tpl->get('issues') as $issue): ?>
        <tr>
            <td>
                <?php if (empty($issue['label_name'])) : ?>
                    <i><?php echo $tpl->getLang('issue_type_no_specified') ?></i>
                <?php else: ?>
                    <?php echo $issue['label_name'] ?>
                <?php endif ?>
            </td>
            <td><?php echo $issue['proposal'] ?></td>
            <td><?php echo $issue['opened'] ?></td>
            <td><?php echo $issue['done'] ?></td>
            <td><?php echo $issue['closed'] ?></td>
            <td><?php echo $issue['rejected'] ?></td>
            <td><?php echo $issue['count_all'] ?></td>
        </tr>
    <?php endforeach ?>
    <tr>
        <th><?php echo $tpl->getLang('report_total') ?></th>
        <td><?php echo array_sum(array_column($tpl->get('issues'), 'proposal')) ?></td>
        <td><?php echo array_sum(array_column($tpl->get('issues'), 'opened')) ?></td>
        <td><?php echo array_sum(array_column($tpl->get('issues'), 'done')) ?></td>
        <td><?php echo array_sum(array_column($tpl->get('issues'), 'closed')) ?></td>
        <td><?php echo array_sum(array_column($tpl->get('issues'), 'rejected')) ?></td>
        <td><?php echo array_sum(array_column($tpl->get('issues'), 'count_all')) ?></td>
    </tr>
</table>

<table class="bez_sumarise">
    <tr>
        <th><?php echo $tpl->getLang('type') ?></th>
        <th><?php echo ucfirst($tpl->getLang('report threads cost')) ?></th>
        <th><?php echo ucfirst($tpl->getLang('report threads cost closed')) ?></th>
        <th><?php echo ucfirst($tpl->getLang('average_of_close')) ?></th>
    </tr>
    <?php foreach ($tpl->get('issues') as $issue): ?>
        <tr>
            <td>
                <?php if (empty($issue['label_name'])) : ?>
                    <i><?php echo $tpl->getLang('issue_type_no_specified') ?></i>
                <?php else: ?>
                    <?php echo $issue['label_name'] ?>
                <?php endif ?>
            </td>
            <td>
                <?php if (empty($issue['sum_all'])) : ?>
                    ---
                <?php else: ?>
                    <?php echo $issue['sum_all'] ?>
                <?php endif ?>
            </td>
            <td>
                <?php if (empty($issue['sum_closed'])) : ?>
                    ---
                <?php else: ?>
                    <?php echo $issue['sum_closed'] ?>
                <?php endif ?>
            </td>
            <td>
                <?php if (empty($issue['avg_closed'])) : ?>
                    ---
                <?php else: ?>
                    <?php echo round($issue['avg_closed']) ?> <?php echo $tpl->getLang('days') ?>
                <?php endif ?>
            </td>
        </tr>
    <?php endforeach ?>
    <tr>
        <th><?php echo $tpl->getLang('report_total') ?></th>
        <td><?php echo array_sum(array_column($tpl->get('issues'), 'sum_all')) ?></td>
        <td><?php echo array_sum(array_column($tpl->get('issues'), 'sum_closed')) ?></td>
        <td><?php echo round(array_sum(array_column($tpl->get('issues'), 'avg_closed'))
                             / count(array_filter(array_column($tpl->get('issues'), 'avg_closed')))) ?>
            <?php echo $tpl->getLang('days') ?></td>
    </tr>
</table>

<h2><?php echo $tpl->getLang('nav projects') ?></h2>

<table>
    <tr>
        <th><?php echo ucfirst($tpl->getLang('proposal')) ?></th>
        <th><?php echo ucfirst($tpl->getLang('open')) ?></th>
        <th><?php echo ucfirst($tpl->getLang('report threads done')) ?></th>
        <th><?php echo ucfirst($tpl->getLang('closed')) ?></th>
        <th><?php echo ucfirst($tpl->getLang('report threads rejected')) ?></th>
        <th><?php echo ucfirst($tpl->getLang('all')) ?></th>
    </tr>
    <?php foreach ($tpl->get('projects') as $project): ?>
        <tr>
            <td><?php echo $project['proposal'] ?></td>
            <td><?php echo $project['opened'] ?></td>
            <td><?php echo $project['done'] ?></td>
            <td><?php echo $project['closed'] ?></td>
            <td><?php echo $project['rejected'] ?></td>
            <td><?php echo $project['count_all'] ?></td>
        </tr>
    <?php endforeach ?>
</table>

<table>
    <tr>
        <th><?php echo ucfirst($tpl->getLang('report threads cost')) ?></th>
        <th><?php echo ucfirst($tpl->getLang('report threads cost closed')) ?></th>
        <th><?php echo ucfirst($tpl->getLang('average_of_close')) ?></th>
    </tr>
    <?php foreach ($tpl->get('projects') as $project): ?>
        <tr>
            <td>
                <?php if (empty($project['sum_all'])) : ?>
                    ---
                <?php else: ?>
                    <?php echo $project['sum_all'] ?>
                <?php endif ?>
            </td>
            <td>
                <?php if (empty($project['sum_closed'])) : ?>
                    ---
                <?php else: ?>
                    <?php echo $project['sum_closed'] ?>
                <?php endif ?>
            </td>
            <td>
                <?php if (empty($project['avg_closed'])) : ?>
                    ---
                <?php else: ?>
                    <?php echo round($project['avg_closed']) ?> <?php echo $tpl->getLang('days') ?>
                <?php endif ?>
            </td>
        </tr>
    <?php endforeach ?>
</table>

<h2><?php echo $tpl->getLang('tasks') ?></h2>

<table class="bez_sumarise">
    <tr>
        <th><?php echo ucfirst($tpl->getLang('task_type')) ?></th>
        <th><?php echo ucfirst($tpl->getLang('open')) ?></th>
        <th><?php echo ucfirst($tpl->getLang('number_of_close_on_time')) ?></th>
        <th><?php echo ucfirst($tpl->getLang('number_of_close_off_time')) ?></th>
        <th><?php echo ucfirst($tpl->getLang('all')) ?></th>

    </tr>
    <?php foreach ($tpl->get('tasks') as $task): ?>
        <tr>
            <td>
                <?php if (empty($task['task_program_name'])) : ?>
                    <i><?php echo $tpl->getLang('tasks_no_type') ?></i>
                <?php else: ?>
                    <?php echo $task['task_program_name'] ?>
                <?php endif ?>
            </td>
            <td><?php echo $task['opened'] ?></td>
            <td><?php echo $task['closed_on_time'] ?></td>
            <td><?php echo $task['closed_after_the_dedline'] ?></td>
            <td><?php echo $task['count_all'] ?></td>
        </tr>
    <?php endforeach ?>
    <tr>
        <th><?php echo $tpl->getLang('report_total') ?></th>
        <td><?php echo array_sum(array_column($tpl->get('tasks'), 'opened')) ?></td>
        <td><?php echo array_sum(array_column($tpl->get('tasks'), 'closed_on_time')) ?></td>
        <td><?php echo array_sum(array_column($tpl->get('tasks'), 'closed_after_the_dedline')) ?></td>
        <td><?php echo array_sum(array_column($tpl->get('tasks'), 'count_all')) ?></td>
    </tr>
</table>

<table class="bez_sumarise">
    <tr>
        <th><?php echo ucfirst($tpl->getLang('task_type')) ?></th>
        <th><?php echo ucfirst($tpl->getLang('report threads cost')) ?></th>
        <th><?php echo ucfirst($tpl->getLang('report threads cost closed')) ?></th>
    </tr>
    <?php foreach ($tpl->get('tasks') as $task): ?>
        <tr>
            <td>
                <?php if (empty($task['task_program_name'])) : ?>
                    <i><?php echo $tpl->getLang('tasks_no_type') ?></i>
                <?php else: ?>
                    <?php echo $task['task_program_name'] ?>
                <?php endif ?>
            </td>
            <td>
                <?php if (empty($task['total_cost'])) : ?>
                    ---
                <?php else: ?>
                    <?php echo $task['total_cost'] ?>
                <?php endif ?>
            </td>
            <td>
                <?php if (empty($task['cost_of_closed'])) : ?>
                    ---
                <?php else: ?>
                    <?php echo $task['cost_of_closed'] ?>
                <?php endif ?>
            </td>
        </tr>
    <?php endforeach ?>
    <tr>
        <th><?php echo $tpl->getLang('report_total') ?></th>
        <td><?php echo array_sum(array_column($tpl->get('tasks'), 'total_cost')) ?></td>
        <td><?php echo array_sum(array_column($tpl->get('tasks'), 'cost_of_closed')) ?></td>
    </tr>
</table>
