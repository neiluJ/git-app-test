<!DOCTYPE html>
<html lang="en" ng-app="gitApp">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php if(isset($page_title)): ?><?php echo $vh->escape($page_title); ?> &bullet; <?php endif; ?><?php echo $vh->escape($vh->appTitle()); ?></title>

    <link rel="icon" type="image/x-icon" href="<?php echo str_replace('/index.php', '/', $vh->url()); ?>favicon.ico" />
    <link rel="apple-touch-icon" sizes="57x57" href="<?php echo str_replace('/index.php', '/', $vh->url()); ?>factory.png" />
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo str_replace('/index.php', '/', $vh->url()); ?>factory.png" />
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo str_replace('/index.php', '/', $vh->url()); ?>factory.png" />
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo str_replace('/index.php', '/', $vh->url()); ?>factory.png" />

    <!-- Bootstrap core CSS -->
    <link href="http://fonts.googleapis.com/css?family=Roboto:400,900,700,300" rel="stylesheet" type="text/css">
    <link href="<?php echo str_replace('/index.php', '/', $vh->url()); ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo str_replace('/index.php', '/', $vh->url()); ?>css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="<?php echo str_replace('/index.php', '/', $vh->url()); ?>css/typeahead.js-bootstrap.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="<?php echo str_replace('/index.php', '/', $vh->url()); ?>css/site.css" rel="stylesheet">
    <link href="<?php echo str_replace('/index.php', '/', $vh->url()); ?>fonts/octicons/octicons.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="<?php echo str_replace('/index.php', '/', $vh->url()); ?>js/html5shiv.js"></script>
      <script src="<?php echo str_replace('/index.php', '/', $vh->url()); ?>js/respond.min.js"></script>
    <![endif]-->
    
    <script src="<?php echo str_replace('/index.php', '/', $vh->url()); ?>js/md5.js"></script>
    <script src="<?php echo str_replace('/index.php', '/', $vh->url()); ?>js/jquery.min.js"></script>
    <script src="<?php echo str_replace('/index.php', '/', $vh->url()); ?>js/angular.min.js"></script>
    <script src="<?php echo str_replace('/index.php', '/', $vh->url()); ?>app/gitapp.js"></script>
    <script src="<?php echo str_replace('/index.php', '/', $vh->url()); ?>app/services.js"></script>
    <script src="<?php echo str_replace('/index.php', '/', $vh->url()); ?>app/controllers.js"></script>
  </head>