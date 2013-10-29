<?php

class MenuHelper extends Simplify_View_Helper
{

  public function show(Simplify_Menu $menu)
  {
    $output = $this->showMenu($menu, 0);

    return $this->output($output);
  }

  protected function showMenu(Simplify_Menu $menu, $level)
  {
    switch ($menu->style) {
      case Simplify_Menu::STYLE_LIST :
        $ul = e('<ul>')->addClass('nav nav-list');

        if ($menu->label) {
          $ul->append(e('<li>')->addClass('nav-header')->html($menu->label));
        }
        break;

      case Simplify_Menu::STYLE_PILLS :
        $ul = e('<ul>')->addClass('nav nav-pills');
        break;

      case Simplify_Menu::STYLE_TOOLBAR :
        $ul = e('<div>')->addClass('btn-toolbar');
        break;

      case Simplify_Menu::STYLE_BUTTON_GROUP :
        $ul = e('<div>')->addClass('btn-group');
        break;

      default :
        $ul = e('<ul>')->addClass($menu->style);
    }

    $i = 0;
    while ($i < $menu->numItems()) {
      $ul->append($this->showItem($menu->getItemAt($i), $menu->style, $level));
      $i++;
    }

    return $ul;
  }

  protected function showItem(Simplify_MenuItem $item, $style, $level)
  {
    $e = array();

    if ($item instanceof Simplify_Menu) {
      $e[] = $this->showMenu($item, $level + 1);
    }
    else {
      switch ($style) {
        case Simplify_Menu::STYLE_BUTTON_GROUP :
          $linkParams = array();

          if (! empty($item->url)) {
            $linkParams['href'] = $item->url;
            $linkParams['title'] = $item->label;
          }

          $a = e('<a>', $linkParams)->addClass('btn')->append($item->label);

          $li = e()->append($a);

          $e[] = $li;

          break;

        default :
          $li = e('<li>');

          if (! empty($item->url)) {
            $linkParams = array();

            if (! empty($item->url)) {
              $linkParams['href'] = $item->url;
              $linkParams['title'] = $item->label;
            }

            $a = e('<a>', $linkParams)->append($item->label);

            $li->append($a);
          } else {
            $li->append($item->label);
          }

          $e[] = $li;
      }

      if ($item->submenu) {
        switch ($style) {
          case Simplify_Menu::STYLE_PILLS :
          case Simplify_Menu::STYLE_BUTTON_GROUP :
            $li->addClass('dropdown');

            $a->data('toggle', 'dropdown');
            $a->attr('href', '#');
            $a->append(' ')->append(e('<b>')->addClass('caret'));

            $li->append($a);

            $submenu = $this->showMenu($item->submenu, $level + 1);

            $li->append($submenu);

            break;

          default :
            $e[] = $this->showMenu($item->submenu, $level + 1);
        }
      }
    }

    return $e;
  }

}
