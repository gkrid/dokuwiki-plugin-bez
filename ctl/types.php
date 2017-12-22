<?php
/** @var action_plugin_bez $this */

use \dokuwiki\plugin\bez;

if ($this->model->acl->get_level() < BEZ_AUTH_ADMIN) {
    throw new bez\meta\PermissionDeniedException();
}

$labels = $this->model->labelFactory->get_all();

if ($this->get_param('id') === '') {
    $label = $this->model->labelFactory->create_object();
} else {
    $label = $this->model->labelFactory->get_one($this->get_param('id'));
}

$this->tpl->set('labels', $labels);
$this->tpl->set('label', $label);
    

if ($this->get_param('action') === 'edit') {

    $this->tpl->set_values($label->get_assoc());

} else if ($this->get_param('action') === 'remove') {
    
    $this->model->labelFactory->delete($label);
    
    header('Location: '.$this->url('types'));

} elseif (count($_POST) > 0) {
    $label->set_data($_POST);
    $this->model->labelFactory->save($label);
    
    header('Location: '.$this->url('types'));
}
