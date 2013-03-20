<?php

class UsersController extends FormController
{

  protected $table = ACL_USERS_TABLE;

  protected $pk = 'user_id';

  protected function initialize()
  {
    parent::initialize();

    $groups = new Simplify_Form_Element_Checkboxes('groups', 'Groups');
    $groups->table = 'acl_groups';
    $groups->foreignKey = 'group_id';
    $groups->labelField = 'group_name';
    $groups->associationTable = 'acl_groups_users';

    $permissions = new Simplify_Form_Element_Checkboxes('permissions', 'Permissions');
    $permissions->table = 'acl_permissions';
    $permissions->foreignKey = 'permission_id';
    $permissions->labelField = 'permission_name';
    $permissions->associationTable = 'acl_permissions_users';

    $this->Form->addElement(new Simplify_Form_Element_Email('user_email', 'Email'));
    $this->Form->addElement(new Simplify_Form_Element_Password('user_password', 'Password'), Simplify_Form::ACTION_ALL ^ Simplify_Form::ACTION_LIST);
    $this->Form->addElement($groups, Simplify_Form::ACTION_LIST | Simplify_Form::ACTION_EDIT);
    $this->Form->addElement($permissions, Simplify_Form::ACTION_LIST | Simplify_Form::ACTION_EDIT);

    $this->Form->label = 'user_email';

    $this->Form->addListener(Simplify_Form::ON_RENDER, $this);
    $this->Form->addListener(Simplify_Form::ON_BEFORE_DELETE, $this);
  }

  public function onBeforeDelete(Simplify_Form_Action $action, $row)
  {
    if ($row['id'] == 1) {
      throw new Simplify_Validation_ValidationException('Cannot delete this user');
    }
  }

  public function onRender(Simplify_Form_Action $action)
  {
    $data = $action->get('data');

    foreach($data as &$row) {
      if ($row[Simplify_Form::ID] == 1) {
        $row['elements']['groups'] = 'This user belongs to every group';
        $row['elements']['permissions'] = 'This user has all permissions';
      }
    }

    $action->set('data', $data);
  }

}
