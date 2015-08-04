<div class="container">
  <div class="row">
    <div class="col-md-12">
      <h2>Modules</h2>
      
      <table class="table">
        <tr>
          <th>Module</th>
          <th>Actions</th>
        </tr>
      
        {% for path, module in modules %}
        <tr>
          <td>{{ module.getName() }}</td>
      
          <td style="width:1%;">
            {% if module.active %}
              <a href="{{ makeUrl(null, {'deactivate' : 1, 'module' : path}) }}">Deactivate</a>
            {% else %}
              <a href="{{ makeUrl(null, {'activate' : 1, 'module' : path}) }}">Activate</a>
            {% endif %}
          </td>
        </tr>
        {% endfor %}
      </table>
    </div>
  </div>
</div>