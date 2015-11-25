<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

  <title>{{ optionsValue('site_name') ? optionsValue('site_name') : 'Amplify' }}</title>

  {% spaceless %}
  {{ asset('jquery/jquery.min.js', 'vendor', -1) }}

  {{ asset('jquery-ui/jquery-ui.min.js', 'vendor') }}

  {{ asset('bootstrap/css/bootstrap.min.css', 'vendor') }}
  {{ asset('bootstrap/js/bootstrap.min.js', 'vendor') }}

  {{ asset('fancybox/jquery.fancybox.css', 'vendor') }}
  {{ asset('fancybox/jquery.fancybox.pack.js', 'vendor') }}

  {{ assets(['vendor', 'app']) }}
  {% endspaceless %}
</head>
<body>
  {% if user %}
  <nav class="navbar navbar-default" role="navigation">
    <div class="container-fluid">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
          <span class="sr-only">Menu</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="{{ makeUrl('route://admin') }}">{{ optionsValue('site_name') }}</a>
      </div>

      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
          {% if menu %}{% include 'navbar_menu.php' with {'menu': menu} %}{% endif %}
        </ul>

        <ul class="nav navbar-nav navbar-right">
          <li><a href="{{ makeUrl('route://admin_account') }}"><span class="glyphicon glyphicon-user"></span> {{ user['user_email'] }}</a></li>
          <li><a href="{{ makeUrl(config.get('app:url')) }}" target="_blank"><span class="glyphicon glyphicon-new-window"></span> Ir para o site</a></li>
          <li><a href="{{ makeUrl('route://admin_logout') }}"><span class="glyphicon glyphicon-log-out"></span> Sair</a></li>
        </ul>
      </div>
    </div>
  </nav>
  {% endif %}

  {% if warnings %}
    <div class="container">
      <div class="col-md-12">
        <div class="alert alert-danger" role="alert">
          <ul>
          {% for message in warnings %}
            <li>{{ message }}</li>
          {% endfor %}
          </ul>
        </div>
      </div>
    </div>
  {% endif %}

  {% if notices %}
    <div class="container">
      <div class="col-md-12">
        <div class="alert alert-info" role="alert">
          <ul>
          {% for message in notices %}
            <li>{{ message }}</li>
          {% endfor %}
          </ul>
        </div>
      </div>
    </div>
  {% endif %}

  {{ layout_content }}

</body>
</html>