<?php

$types = $this->model->issuetypes->get_all();

if ($this->param('id') === '') {
    $issuetype = $this->model->issuetypes->create_object();
} else {
    $issuetype = $this->model->issuetypes->get_one($this->param('id'));
}

$this->tpl->set('types', $types);
$this->tpl->set('issuetype', $issuetype);
    

if ($this->param('action') === 'edit') {
    
    $this->tpl->set_values($issuetype->get_assoc());
    
} else if ($this->param('action') === 'remove') {
    
    $this->model->issuetypes->delete($issuetype);
    
    header('Location: '.$this->url('types'));
    
} elseif (count($_POST) > 0) {
    $issuetype->set_data($_POST);
    $this->model->issuetypes->save($issuetype);
    
    header('Location: '.$this->url('types'));
}
