<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8"/>

  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

  <title><?= Options::value('site_name') ?></title>

  <?= $this->html->js('jquery') ?>
  <?= $this->html->js('../bootstrap/js/bootstrap.js') ?>

  <?= $this->html->css('../bootstrap/css/bootstrap.min.css') ?>
  <?= $this->html->css('../bootstrap/css/bootstrap-responsive.min.css') ?>
  <?= $this->html->css('../bootstrap/css/font-awesome.css') ?>
</head>

<body>
<div class="navbar navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container-fluid">
      <a class="brand" href="<?= Simplify_URL::make('/') ?>">
        <?= Options::value('site_name') ?>
        <small><?= Options::value('site_tagline') ?></small>
      </a>
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

        <?= $layout_content ?>
      </div>
    </div>
  </div>
</div>
</body>
</html>