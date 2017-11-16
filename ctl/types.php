<?php

$labels = $this->model->labelFactory->get_all();

if ($this->param('id') === '') {
    $label = $this->model->labelFactory->create_object();
} else {
    $label = $this->model->labelFactory->get_one($this->param('id'));
}

$this->tpl->set('labels', $labels);
$this->tpl->set('label', $label);
    

if ($this->param('action') === 'edit') {
    
    $this->tpl->set_values($label->get_assoc());
    
} else if ($this->param('action') === 'remove') {
    
    $this->model->labelFactory->delete($label);
    
    header('Location: '.$this->url('types'));
    
} elseif (count($_POST) > 0) {
    $label->set_data($_POST);
    $this->model->labelFactory->save($label);
    
    header('Location: '.$this->url('types'));
}
