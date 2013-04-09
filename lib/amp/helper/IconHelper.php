<?php

class IconHelper extends Simplify_View_Helper
{

  const ICON = 'icon';

  const LABEL = 'label';

  const BOTH = 'both';

  public static $mode = 'icon';

  public static $icons = array(
    //'view' => array('icons/magnifier.png', 'View'),
    'edit' => array('icon-edit', 'Edit'),
    'remove' => array('icon-trash', 'Remove'),
    'create' => array('icon-plus', 'Create'),
    'index' => array('icon-list', 'List'),

    //'top' => array('icons/control-stop-090.png', 'Top'),
    //'up' => array('icons/control-090.png', 'Up'),
    //'down' => array('icons/control-270.png', 'Down'),
    //'bottom' => array('icons/control-stop-270.png', 'Bottom'),

    'first' => array('icon-fast-backward', 'First'),
    'previous' => array('icon-backward', 'Previous'),
    'next' => array('icon-forward', 'Next'),
    'last' => array('icon-fast-forward', 'Last'),

    //'select_all' => array('icons/ui-check-box.png', 'All'),
    //'select_none' => array('icons/ui-check-box-uncheck.png', 'None'),
    //'select_invert' => array('icons/ui-check-boxes.png', 'Invert'),

    //'logout' => array('icons/cross-octagon.png', 'Logout'),
  );
/*
  public static $icons = array(
    'index' => 'icon-list',
    'create' => 'icon-plus',
    'edit' => 'icon-edit',
    'delete' => 'icon-trash',
    'options' => 'icon-cog',
    'group' => 'icon-group',
    'permission' => 'icon-lock',
  );
*/
  public function show($action, $alt = null)
  {
    $output = '';

    if (! empty(self::$icons[$action])) {
      $icon = self::$icons[$action];

      $class = $icon[0];
      $alt = empty($alt) ? $icon[1] : $alt;

      switch (self::$mode) {
        case self::ICON:
          $output = e('<i>')->addClass($class);//$this->html->image($img, $alt);
          break;

        case self::LABEL:
          $output = $alt;
          break;

        case self::BOTH:
          $output = e('<i>')->addClass($class) . ' ' . $alt;
          break;
      }
    }
    else {
      $output = empty($alt) ? $action : $alt;
    }

    return $output;
  }

}
