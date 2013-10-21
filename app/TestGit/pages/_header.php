<!DOCTYPE html>
<html lang="en" ng-app="gitApp">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Test-Git</title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo str_replace('/index.php', '', $vh->url()); ?>/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<?php echo str_replace('/index.php', '', $vh->url()); ?>/css/site.css" rel="stylesheet">
    
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="<?php echo str_replace('/index.php', '', $vh->url()); ?>/js/html5shiv.js"></script>
      <script src="<?php echo str_replace('/index.php', '', $vh->url()); ?>/js/respond.min.js"></script>
    <![endif]-->
    
    <script src="<?php echo str_replace('/index.php', '', $vh->url()); ?>/js/angular.min.js"></script>
    <script src="<?php echo str_replace('/index.php', '', $vh->url()); ?>/app/gitapp.js"></script>
    <script src="<?php echo str_replace('/index.php', '', $vh->url()); ?>/app/controllers.js"></script>
  </head>