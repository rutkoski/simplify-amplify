{% for item in menu.items() %}
  {% if item.isMenu() %}
    {% if inSubMenu != 1 %}
      <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
          {% if item.icon %}<span class="{{ item.icon }}"></span>{% endif %}
          {{ item.label }}
           <span class="caret"></span>
        </a>
        <ul class="dropdown-menu" role="menu">
        {% include 'navbar_menu.php' with {'menu': item, 'inSubMenu': true} %}
        </ul>
      </li>
    {% else %}
      <li class="divider"></li>
      <li role="presentation" class="dropdown-header">
        {% if item.icon %}<span class="{{ item.icon }}"></span>{% endif %}
        {{ item.label }}
      </li>
      {% include 'navbar_menu.php' with {'menu': item, 'inSubMenu': true} %}
    {% endif %}
  {% else %}
    <li>
      <a href="{{ item.url }}" title="{{ item.label }}">
        {% if item.icon %}<span class="{{ item.icon }}"></span>{% endif %}
        {{ item.label }}
      </a>
    </li>
  {% endif %}
{% endfor %}