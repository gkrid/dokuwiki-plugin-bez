<?php

$this->tpl->set('thread_involvement', $this->model->threadFactory->users_involvement());
$this->tpl->set('task_involvement', $this->model->taskFactory->users_involvement());
