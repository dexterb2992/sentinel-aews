<?php 
  session_start(); 
  date_default_timezone_set('Europe/Dublin');
  ini_set('date.timezone', 'Europe/Dublin');
   global $current_user;
   global $userdata;
   get_currentuserinfo();

   if(!function_exists('redirect')){
    include __LIB."functions.php";
   }
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
      <?php 
        echo __APP_TITLE; 
        echo isset($_page_title) ? " | $_page_title" : "";
      ?>
    </title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <?php include "_includes/_styles.php"; ?>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <!--
  BODY TAG OPTIONS:
  =================
  Apply one or more of the following classes to get the
  desired effect
  |---------------------------------------------------------|
  | SKINS         | skin-blue                               |
  |               | skin-black                              |
  |               | skin-purple                             |
  |               | skin-yellow                             |
  |               | skin-red                                |
  |               | skin-green                              |
  |---------------------------------------------------------|
  |LAYOUT OPTIONS | fixed                                   |
  |               | layout-boxed                            |
  |               | layout-top-nav                          |
  |               | sidebar-collapse                        |
  |               | sidebar-mini                            |
  |---------------------------------------------------------|
  -->
  <body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">

      <!-- Main Header -->
      <header class="main-header">

        <!-- Logo -->
        <a href="#" class="logo">
          <!-- mini logo for sidebar mini 50x50 pixels -->
          <span class="logo-mini"><?php echo __APP_NAME_ABBREV; ?></span>
          <!-- logo for regular state and mobile devices -->
          <span class="logo-lg"><b><?php echo __APP_NAME; ?></b></span>
        </a>

        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
          </a>
        </nav>         
      </header>
      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">

        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">

          <!-- Sidebar user panel (optional) -->
          <?php if( get_current_user_id() > 0 ){ ?>
          <div class="user-panel">
            <div class="pull-left image">
              <?php echo get_avatar( $userdata->ID, 256, '', "User Image", array('class' => 'img-circle') ); ?>
            </div>
            <div class="pull-left info">
              <?php echo  $current_user->display_name; ?>
              <span><a href="<?php echo site_url('my-account'); ?>">My Account</a> | <a href="<?php echo wp_logout_url(); ?>">Logout</a></span>
            </div>
          </div>

          <?php } ?>
          <!-- /.search form -->

          <?php include "_sidebar.php"; ?>
        </section>
      </aside>
