<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 
?>
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

<section class="content-header">
     
    <h1>
        Dashboard
        <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-light-blue">
                <div class="inner">
                    <h3>1</h3>
                    <p>Jurnal Umum</p>
                </div>
                <div class="icon">
                    <i class="ion ion-android-globe"></i>
                </div>
                <?php
                if ($_SESSION['my']->privilege == 'ADMIN') {
                    echo '<a href="index2.php?page=view/jurnalumum_list" class="small-box-footer">Lanjut <i class="fa fa-arrow-circle-right"></i></a>';
                }
                ?>
            </div>
        </div>
        <!-- <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua-active">
                <div class="inner">
                    <h3>2</h3>
                    <p>Jurnal Kas Masuk</p>
                </div>
                <div class="icon">
                    <i class="ion ion-ios-list"></i>
                </div>
                <?php
                if ($_SESSION['my']->privilege == 'ADMIN') {
                    echo '<a href="index2.php?page=view/jurnalkasmasuk_list" class="small-box-footer">Lanjut <i class="fa fa-arrow-circle-right"></i></a>';
                }
                ?>
            </div>
        </div>-->
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>2</h3>
                    <p>Jurnal Penyesuaian</p>
                </div>
                <div class="icon">
                    <i class="ion ion-android-list"></i>
                </div>
                <?php
                if ($_SESSION['my']->privilege == 'ADMIN') {
                    echo '<a href="index2.php?page=view/jurnalpenyesuaian_list" class="small-box-footer">Lanjut <i class="fa fa-arrow-circle-right"></i></a>';
                }
                ?>
            </div>
        </div> 
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-blue">
                <div class="inner">
                    <h3>3</h3>
                    <p>Posting</p>
                </div>
                <div class="icon">
                    <i class="ion ion-ios-analytics"></i>
                </div>
                <?php
                if ($_SESSION['my']->privilege == 'ADMIN') {
                    echo '<a href="index2.php?page=view/posting_list" class="small-box-footer">Lanjut <i class="fa fa-arrow-circle-right"></i></a>';
                }
                ?>
            </div>
        </div>
    </div>
    <div class="row">
        <section class="col-lg-6 connectedSortable">
            <div class="box box-primary">
                <div class="box-header">
                    <i class="ion ion-clipboard"></i>
                    <h3 class="box-title">Akuntansi </h3>
                </div>
                <div class="box-body">
                    <ul class="todo-list">
                        <img src="./dist/img/logo-qoobah.png" width="100%" height="75%">
                    </ul>
                </div>
                <div class="box-footer clearfix no-border">
                </div>
            </div>
        </section>
    </div>
</section>