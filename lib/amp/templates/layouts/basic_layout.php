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
  <?= $this->html->js('/bootstrap/js/bootstrap.js') ?>

  <?= $this->html->css('/bootstrap/css/bootstrap.min.css') ?>
  <?= $this->html->css('/bootstrap/css/bootstrap-responsive.min.css') ?>
  <?= $this->html->css('/bootstrap/css/font-awesome.css') ?>
</head>

<body>
  <?= $layout_content ?>
</body>
</html>