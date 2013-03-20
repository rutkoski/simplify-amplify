<?php

class PermissionsController extends FormController
{

  protected $table = ACL_PERMISSIONS_TABLE;

  protected $pk = 'permission_id';

  protected function initialize()
  {
    parent::initialize();

    $this->Form->addElement(new Simplify_Form_Element_Text('permission_name', 'Name'));
    $this->Form->addElement(new Simplify_Form_Element_Textarea('permission_description', 'Description'));
  }

}
