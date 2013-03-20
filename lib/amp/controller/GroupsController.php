<?php

class GroupsController extends FormController
{

  protected $table = ACL_GROUPS_TABLE;

  protected $pk = 'group_id';

  protected function initialize()
  {
    parent::initialize();

    $permissions = new Simplify_Form_Element_Checkboxes('group_id', 'Permissions');
    $permissions->table = 'acl_permissions';
    $permissions->foreignKey = 'permission_id';
    $permissions->labelField = 'permission_name';
    $permissions->associationTable = 'acl_groups_permissions';

    $this->Form->addElement(new Simplify_Form_Element_Text('group_name', 'Name'));
    $this->Form->addElement($permissions, Simplify_Form::ACTION_LIST | Simplify_Form::ACTION_EDIT);
  }

}
