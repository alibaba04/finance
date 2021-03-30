<?php
//=======  : Alibaba
//Untuk memastikan bahwa setiap sesi web dimulai dari halaman ini
define('validSession', 1);

//Periksa keberadaan file config.php. Jika ada, load file tersebut untuk memasukkan variable konfigurasi umum
if (!file_exists('config.php')) {
    exit();
}


require_once( 'config.php' );
require_once('./class/c_user.php');

//ini_set('display_errors', 1);

session_name("akunaki");
session_start();

require_once('./function/fungsi_menu.php');
require_once('./function/getUserPrivilege.php');
require_once('./function/pagedresults.php');
require_once('./function/secureParam.php');
require_once('./function/fungsi_formatdate.php');

?>
<!DOCTYPE html>
<html>
    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Finance</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.6 -->
        <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
        <!-- Font Awesome -->
<!--        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">-->
        <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
        <!-- Ionicons -->
<!--        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">-->
        <link rel="stylesheet" href="ionicons/css/ionicons.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="dist/css/AdminLTE.css">
        <!-- AdminLTE Skins. Choose a skin from the css/skins
             folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="dist/css/skins/skin-qoobah.css">
        <!-- iCheck for checkboxes and radio inputs -->
        <link rel="stylesheet" href="plugins/iCheck/all.css">
        <!-- iCheck -->
<!--        <link rel="stylesheet" href="plugins/iCheck/flat/blue.css">-->
        <!-- Morris chart -->
        <link rel="stylesheet" href="plugins/morris/morris.css">
        <!-- jvectormap -->
        <link rel="stylesheet" href="plugins/jvectormap/jquery-jvectormap-1.2.2.css">
        <!-- Date Picker -->
        <link rel="stylesheet" href="plugins/datepicker/datepicker3.css">
        <!-- Daterange picker -->
        <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
        <!-- bootstrap wysihtml5 - text editor -->
        <link rel="stylesheet" href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
        <!-- Select2 -->
        <link rel="stylesheet" href="plugins/select2/select2.min.css">
        <link rel="stylesheet" href="css/searchInput1.css">
        <link rel="icon" href="dist/img/logo-qoobah.png" type="image/png"/>
        <script src="http://code.jquery.com/jquery-2.2.1.min.js"></script>
        <style type="text/css">
            .preloader {
              position: fixed;
              top: 0;
              left: 0;
              width: 100%;
              height: 100%;
              z-index: 9999;
              background-color: #fff;
          }
          .preloader .loading {
              position: absolute;
              left: 50%;
              top: 50%;
              transform: translate(-50%,-50%);
              font: 14px arial;
          }
      </style>
      <script>
        $(document).ready(function(){
            $(".preloader").fadeOut(1000);
        })
    </script>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <?php
    if ((isset($_SESSION["my"]) === false) || (isset($_GET["page"]) === "login_detail")) {
        echo '<body class="hold-transition login-page">';
    } else {
        
        ?>
        <body class="hold-transition skin-green sidebar-mini">
        <?php
    }
        ?>
    </div>
        <div class="wrapper">
            <header class="main-header">
                <!-- Logo -->
                <?php
                if ((isset($_SESSION["my"]) !== false) && (isset($_GET["page"]) !== "login_detail")) {
                    ?>
                    <a href="index.php" class="logo">
                        <!-- mini logo for sidebar mini 50x50 pixels -->
                        <span class="logo-mini"><img src="dist/img/logo-qoobah4.png"></span>
                        <!-- logo for regular state and mobile devices -->
                        <span class="logo-lg"><img src="dist/img/logo-qoobah3.png" ></span>
                    </a>

                    <!-- Header Navbar: style can be found in header.less -->
                    <nav class="navbar navbar-static-top">
                        <!-- Sidebar toggle button-->
                        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                            <span class="sr-only">Toggle navigation</span>
                            <span style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;">Finance</span>
                        </a>
                        

                        <div class="navbar-custom-menu">
                            <ul class="nav navbar-nav">
                                <!-- Notifications: style can be found in dropdown.less -->
                                
                                
                                <!-- Tasks: style can be found in dropdown.less -->
                                
                                <!-- User Account: style can be found in dropdown.less -->
                                <li class="dropdown user user-menu">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <!--            tampilkan nama user di   -->

                                    </a>
                                </li>
                                <!-- Control Sidebar Toggle Button -->
                                <li>
                                    <!--<a href="index.php?page=login_detail&eventCode=20">Logout&nbsp;&nbsp;<i class="fa fa-sign-out" aria-hidden="true" title="Logout"></i></a>-->
                                    <a href="logout.php?page=login_detail&eventCode=20">Logout&nbsp;&nbsp;<i class="fa fa-sign-out" aria-hidden="true" title="Logout"></i></a>
                                </li>
                            </ul>
                        </div>
                        <?php
                    }
                    ?>
                </nav>
            </header>

            <?php
            /* Periksa session $my, jika belum teregistrasi load modul login */
            if (isset($_SESSION["my"]) == false || isset($_GET["page"]) === "login_detail") {
                require_once('login_detail.php' );
            } else {
                ?>   

                <!-- Left side column. contains the logo and sidebar -->
                <aside class="main-sidebar">
                    <!-- sidebar: style can be found in sidebar.less -->
                    <section class="sidebar">
                        <!-- Sidebar user panel -->
                        <div class="user-panel">
                            <div class="pull-left image">
<!--                                <img src="dist/img/<?php //echo $_SESSION["my"]->id; ?>.jpg" class="img-circle" alt="User Image">-->
                                <img src="dist/img/avatar5.png" class="img-circle" alt="User Image">
                            </div>
                            <div class="pull-left info">
                                <p style="font-size: 17px;"><?php echo $_SESSION["my"]->name; ?></p>
                                <a href="#"><i class="fa fa-circle text-success"></i> <?php echo $_SESSION["my"]->privilege; ?></a>
                            </div>
                        </div>
                        <!-- search form -->
                        <!--      <form action="#" method="get" class="sidebar-form">
                                <div class="input-group">
                                  <input type="text" name="q" class="form-control" placeholder="Search...">
                                      <span class="input-group-btn">
                                        <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                                        </button>
                                      </span>
                                </div>
                              </form>-->
                        <!-- /.search form -->
                        <!-- sidebar menu: : style can be found in sidebar.less -->
                        <ul class="sidebar-menu">
                            <!-- <li class="header">MAIN NAVIGATION</li> -->
                            <li class="treeview">
                                <?php echo menu(); ?>
                            </li>
                        </ul>
                    </section>
                    <!-- /.sidebar -->
                </aside>

                <!-- Content Wrapper. Contains page content -->
                <div class="content-wrapper">
                    <!-- Pages Content take in here -->
                    <?php
                    //Load module yang bersesuaian
                    if (isset($_GET["page"])) {
                        require_once('view/' . substr($_GET["page"] . ".php", 5, strlen($_GET["page"] . '.php') - 5));
                    } else {
                        require_once('view/dashboard.php');
                    }
                    ?>
                    <!-- End Pages Content -->
                </div>
                <!-- content wrapper/.row (main row) -->

                <?php
            }
            ?>
            <!-- /.content-wrapper -->
            <?php
            if (isset($_SESSION["my"]) != false && isset($_GET["page"]) != "login_detail") {
                ?>
                <footer class="main-footer">

                    <div class="pull-right hidden-xs">
                        <b>Accounting App</b> v 2.2.0 &nbsp;&nbsp;<strong>Created by : <a href="http://instagram.com/baihaqial">alibaba</a>.
                    </div>
                    <strong>.</strong>
                    <!-- <strong>Copyright &copy; 2020 <a href="http://instagram.com/baihaqial">alibaba's</a>.</strong> All rights reserved.  -->
                    
                </footer>
                <?php
            }
            ?>

        </div>
        <!-- ./wrapper -->
        <!-- jQuery 2.2.3 -->
        <script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
        <!-- jQuery UI 1.11.4 -->
<!--        <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>-->
        <script src="dist/js/jquery-ui.min.js"></script>
        <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
        <script>
            $.widget.bridge('uibutton', $.ui.button);
        </script>
        <!-- Bootstrap 3.3.6 -->
        <script src="bootstrap/js/bootstrap.min.js"></script>
        <!-- Select2 -->
        <script src="plugins/select2/select2.full.min.js"></script>
        <!-- Morris.js charts -->
<!--        <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>-->
        <script src="dist/js/raphael-min.js"></script>
        <script src="plugins/morris/morris.min.js"></script>
        <!-- Sparkline -->
        <script src="plugins/sparkline/jquery.sparkline.min.js"></script>
        <!-- jvectormap -->
        <script src="plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
        <script src="plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
        <!-- jQuery Knob Chart -->
        <script src="plugins/knob/jquery.knob.js"></script>
        <!-- daterangepicker -->
<!--        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>-->
        <script src="dist/js/moment.min.js"></script>
        <script src="plugins/daterangepicker/daterangepicker.js"></script>
        <!-- datepicker -->
        <script src="plugins/datepicker/bootstrap-datepicker.js"></script>
        <script src="plugins/datepicker/locales/bootstrap-datepicker.id.js"></script>
        <!-- iCheck 1.0.1 -->
        <script src="plugins/iCheck/icheck.min.js"></script>
        <!-- Bootstrap WYSIHTML5 -->
        <script src="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
        <!-- Slimscroll -->
        <script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
        <!-- FastClick -->
        <script src="plugins/fastclick/fastclick.js"></script>
        <!-- AdminLTE App -->
        <script src="dist/js/app.min.js"></script>
        <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
        <script src="dist/js/pages/dashboard.js"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="dist/js/demo.js"></script>
        
    </body>
    
    
</html>