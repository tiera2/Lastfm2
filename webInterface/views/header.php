<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title><?php echo $page_title; ?></title>

    <!-- Bootstrap core CSS -->
    <link href="/lastfm2/webinterface/css/bootstrap.min.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="/lastfm2/webinterface/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/lastfm2/webinterface/css/navbar-fixed-top.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="/lastfm2/webinterface/js/ie-emulation-modes-warning.js"></script>
    <script src="/lastfm2/webinterface/js/angular.js"></script>
    <script src="/lastfm2/webInterface/js/jquery-2.1.4.min.js"></script>
      <!--script src="/lastfm2/webInterface/scripts/vendor/bootstrap-3.3.6-dist/js/bootstrap.min.js"></script-->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script>
    var app = angular.module('stryktApp', []);

    app.controller("stryktController", function($scope, $http) {
      $http.get('http://localhost:8081/lastfm2/api/tracks').
        success(function(data, status, headers, config) {
          $scope.message = data;
        }).
        error(function(data, status, headers, config) {
          // log error
        });
    });
</script>
  </head>

  <body>

    <!-- Fixed navbar -->
    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/lastfm2">Last.FM</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li ><a href="/lastfm2/artists">Artists top</a></li>
            <li><a href="/lastfm2/tracks">Tracks top</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <!--li><a href="../navbar/">Default</a></li>
            <li><a href="../navbar-static-top/">Static top</a></li-->
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $_SESSION['user_name'] ?> <span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="/lastfm2/user/<?php echo $_SESSION['user_name']?>">Settings</a></li>
                <li><a href="/lastfm2/logout">Log out</a></li>
              </ul>
            </li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container">