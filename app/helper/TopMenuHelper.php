<?php

class TopMenuHelper extends Simplify_View_Helper
{

  protected $icons = array(
    'index' => 'icon-list',
    'create' => 'icon-plus',
    'edit' => 'icon-edit',
    'delete' => 'icon-trash',
    'options' => 'icon-cog',
    'group' => 'icon-group',
    'permission' => 'icon-lock',
  );

  protected function addIcon(Simplify_HtmlElement $e, $icon)
  {
    if (empty($icon)) return;

    if (isset($this->icons[$icon])) {
      $icon = $this->icons[$icon];
    }

    $e->prepend(' ')->prepend(e('<i>')->addClass($icon)->html(''));
  }

  public function show(Simplify_Menu $menu)
  {
    $o = e('<ul>', array('class' => 'nav'));

    for ($i = 0; $i < $menu->numItems(); $i++) {
      $item = $menu->getItemAt($i);

      $a = e('<a>')->attr('href', $item->url ? $item->url : '#')->append($item->label);

      $this->addIcon($a, $item->icon);

      $li = e('<li>')->addClass('dropdown')->append($a);

      if ($item instanceof Simplify_Menu) {
        $a->attr('data-toggle', 'dropdown')->addClass('dropdown-toggle')->append(' <b class="caret"></b>');

        $li->append($this->showDropdown($item, 0));
      }

      $o->append($li);
    }

    return $o;
  }

  public function showDropdown(Simplify_Menu $menu, $level = 0)
  {
    $o = e('<ul>', array('class' => 'dropdown-menu' . ($level ? ' submenu-show submenu-hide' : '')));

    for ($i = 0; $i < $menu->numItems(); $i++) {
      $item = $menu->getItemAt($i);

      $a = e('<a>')->attr('href', $item->url ? $item->url : '#')->append($item->label);

      $this->addIcon($a, $item->icon);

      $li = e('<li>', array('class' => 'dropdown'))->append($a);

      if ($item instanceof Simplify_Menu) {
        $a->attr('data-toggle', 'dropdown')->addClass('dropdown-toggle');

        $li->addClass('submenu')->append($this->showDropdown($item, $level + 1));
      }

      $o->append($li);
    }

    return $o;
  }

}
