<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8"/>

  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

  <title><?= Options::value('site_name') ?></title>

  <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->

  <?= $this->html->js('jquery') ?>
  <?= $this->html->js('jquery.maskedinput.min') ?>
  <?= $this->html->js('jquery-ui') ?>
  <?= $this->html->js('jquery-ui-timepicker-addon') ?>
  <?= $this->html->js('jquery.ui.datepicker-pt-BR') ?>
  <?= $this->html->js('jquery-ui-timepicker-pt-BR') ?>
  <?= $this->html->js('/fancybox/jquery.fancybox.pack.js') ?>
  <?= $this->html->js('/wysiwyg/jquery.wysiwyg.js') ?>
  <?= $this->html->js('knockout-latest.js') ?>
  <?= $this->html->js('knockout.mapping-latest.js') ?>
  <?= $this->html->js('/bootstrap/js/bootstrap.js') ?>

  <?= $this->html->css('custom-theme/jquery-ui.custom.css') ?>
  <?= $this->html->css('custom-theme/jquery-ui-timepicker-addon.css') ?>
  <?= $this->html->css('/fancybox/jquery.fancybox.css') ?>
  <?= $this->html->css('/wysiwyg/jquery.wysiwyg.css') ?>
  <?= $this->html->css('/bootstrap/css/bootstrap.min.css') ?>
  <?= $this->html->css('/bootstrap/css/bootstrap-responsive.min.css') ?>
  <?= $this->html->css('/bootstrap/css/font-awesome.css') ?>

  <?= $this->html->css('/fineuploader/fineuploader-3.4.1.css')?>

  <!--[if IE 7]>
  <?= $this->html->css('/bootstrap/css/font-awesome-ie7.css') ?>
  <![endif]-->

  <?= $this->html->css('style.css')?>

  <script>
  var Amp = function() {
    var self = this;

    var baseUrl = '<?= Simplify_URL::make() ?>';

    var _loader;
    var _loadCount = 0;

    function getLoader() {
      if (! _loader) {
        _loader = $('<div>').css({
          backgroundColor: '#000000',
          opacity: .8,
          position: 'absolute',
          top: 0,
          left: 0,
          zIndex: 99999,
        }).append(
          $('<img>').attr('src', '<?= s::config()->get('theme_url') ?>/images/ajax-loader.gif').css({
            position: 'absolute'
          })
        ).hide();

        $(window).resize(function() {
          _loader.width($(window).width()).height($(window).height());

          var img = _loader.find('img');

          img.css({
            top: ($(window).height() - img.height()) / 2,
            left: ($(window).width() - img.width()) / 2
          });
        }).trigger('resize');

        $('body').append(_loader);
      }

      return _loader;
    }

    self.loadBegin = function() {
      if (! _loadCount) {
        getLoader().fadeIn('fast');
        $(window).trigger('resize');
      }

      _loadCount++;
    }

    self.loadEnd = function() {
      _loadCount--;

      if (! _loadCount) {
        getLoader().fadeOut('fast');
      }
    }
  };

  $(document).ready(function() {
    $.amp = new Amp();

    $('.lightbox').fancybox();
    $('.wysiwyg').wysiwyg();
  });
/*
  $.timepicker.regional['pt-BR'] = {
    timeOnlyTitle: 'Hora',
    timeText: 'Hora',
    hourText: 'Horas',
    minuteText: 'Minutos',
    secondText: 'Segundos',
    millisecText: 'Milisegundos',
    currentText: 'Agora',
    closeText: 'Ok',
    ampm: true
  };

  $.timepicker.setDefaults($.timepicker.regional['pt-BR']);
*/
  $(document).ready(function() {
    $('.submenu').hover(function () {
      $(this).children('ul').removeClass('submenu-hide').addClass('submenu-show');
    }, function () {
      $(this).children('ul').removeClass('.submenu-show').addClass('submenu-hide');
    }).find("a:first").append(" &raquo; ");
  });
  </script>

  <style>
  .submenu-show {
    border-radius: 3px;
    display: block;
    left: 100%;
    margin-top: -25px !important;
    moz-border-radius: 3px;
    position: absolute;
    webkit-border-radius: 3px;
  }
  .submenu-hide {
    display: none !important;
    float: right;
    position: relative;
    top: auto;
  }
  .navbar .submenu-show:before {
    border-bottom: 7px solid transparent;
    border-left: none;
    border-right: 7px solid rgba(0, 0, 0, 0.2);
    border-top: 7px solid transparent;
    left: -7px;
    top: 10px;
  }
  .navbar .submenu-show:after {
    border-bottom: 6px solid transparent;
    border-left: none;
    border-right: 6px solid #fff;
    border-top: 6px solid transparent;
    left: 10px;
    left: -6px;
    top: 11px;
  }
  </style>
</head>

<body>
<div class="navbar navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container-fluid">
      <a class="brand" href="<?= Simplify_URL::make('/') ?>">
        <?= Options::value('site_name') ?>
        <small><?= Options::value('site_tagline') ?></small>
      </a>

      <?= $this->top_menu->show(s::app()->menu()) ?>

      <ul class="nav pull-right">
        <?php $user = Account::getUser() ?>
        <li><a href="<?= Simplify_URL::make('/') ?>"><i class="icon-user"></i> <?= $user['user_email'] ?></a></li>
        <li><a href="<?= Simplify_URL::make('/..') ?>"><i class="icon-external-link"></i> Ir para o site</a></li>
        <li><a href="<?= Simplify_URL::make('/logout') ?>"><i class="icon-signout"></i> Logout</a></li>
      </ul>
    </div>
  </div>
</div>

<div class="container">
  <div class="row" style="margin-top:50px;">
    <div id="main-column" class="span12">
      <div id="content">
        <?php if ($messages = s::app()->warnings()) { ?>
        <div class="alert alert-error"><?= implode('<br/>', $messages['*']) ?></div>
        <?php } ?>

        <?php if ($messages = s::app()->notices()) { ?>
        <div class="alert alert-info"><?= implode('<br/>', $messages['*']) ?></div>
        <?php } ?>

        <?php s::app()->clearMessages(); ?>

        <?= $layout_content ?>
      </div>
    </div>
  </div>
</div>
</body>
</html>