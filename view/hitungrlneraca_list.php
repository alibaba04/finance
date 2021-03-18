<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/hitungrlneraca_list";
$judulMenu = 'Hitung Rugi Laba dan Neraca';
$hakUser = getUserPrivilege($curPage);
if ($hakUser < 10) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User cannot access this page!');
    echo "</p>";
}
//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {
    require_once("./class/c_hitungrlneraca.php");
    $tmpRLNeraca = new c_hitungrlneraca;
//Jika Mode Tambah/Add
    if ($_POST["sbmHitungRL"] == "Proses Hitung") {
        $tglPosting1 = $_POST['starts'];
        $tglPosting2 = $_POST['ends'];
        $pesan = $tmpRLNeraca->hitungRL($tglPosting1,$tglPosting2);
    }
//Seharusnya semua transaksi Add dan Edit Sukses karena data sudah tervalidasi dengan javascript di form detail.
//Jika masih ada masalah, berarti ada exception/masalah yang belum teridentifikasi dan harus segera diperbaiki!
    if (strtoupper(substr($pesan, 0, 5)) == "GAGAL") {
        global $mailSupport;
        $pesan.="Warning!!, please text to " . $mailSupport . " for support this error!.";
    }
    header("Location:index.php?page=$curPage&pesan=" . $pesan);
    exit;
}
?>
<!-- Include script date di bawah jika ada field tanggal -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="dist/js/jquery-ui.min.js"></script>
<script src="plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" charset="utf-8">
    $(function () {
        $('#tglTransaksi').daterangepicker({ 
            locale: { format: 'DD-MM-YYYY' } });
        $('#tglTransaksi').on('apply.daterangepicker', function(ev, picker) {
            $('#sbmPosting').prop('disabled', false);
            $("#starts").val (picker.startDate.format('DD-MM-YYYY'));
            $("#ends").val (picker.endDate.format('DD-MM-YYYY'));
        });
    });
</script>
<!-- End of Script Tanggal -->
<section class="content-header">
    <h1>
        HITUNG RUGI LABA & NERACA
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Proses</li>
        <li class="active">Hitung Rugi Laba & Neraca </li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <!-- Main row -->
    <div class="row">
        <!-- Left col -->
        <section class="col-lg-6">
            <!-- TO DO List -->
            <div class="box box-primary">
                <div class="box-header">
                    <i class="ion ion-clipboard"></i>
                    <h3 class="box-title"> </h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <form name="frmCariJurnalMasuk" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <input type="hidden" name="page" value="<?php echo $curPage; ?>">

                        <div class="form-group">
                            <label>Range Transaction Date</label>
                            <input type="text" class="form-control" name="tglTransaksi" id="tglTransaksi" 
                            <?php
                            if (isset($_GET["tglTransaksi"])) {
                                echo("value='" . $_GET["tglTransaksi"] . "'");
                            }
                            ?>
                            onKeyPress="return handleEnter(this, event)">
                        </div>
                    </form>
                    <div class="callout callout-danger">
                        <h4><strong>WARNING!</strong></h4>
                        <p align="justify">
                            Menu ini adalah Proses untuk <strong>MENGHITUNG RUGI LABA dan NERACA</strong>. Dari proses ini akan menghasilkan laporan keuangan berupa Laporan Rugi Laba dan Neraca.
                        </p>
                        <p align="justify">
                            <strong>Proses Hitung bisa dilakukan setelah semua data transaksi diposting. Bila belum melakukan posting silahkan lakukan posting melalui Menu Proses->Posting Jurnal.</strong>
                        </p>
                        <p align="justify">
                            Untuk melakukan Proses Hitung Rugi Laba dan Neraca tekan tombol <strong>Proses Hitung</strong> di bawah ini.
                        </p>
                    </div>
                    <form name="frmProsesRL" method="POST" action="index2.php?page=view/hitungrlneraca_list" onSubmit="return validate(this);">
                        <input type="hidden" id="starts" name="starts">
                        <input type="hidden" id="ends" name="ends" >
                        <input type="submit" name="sbmHitungRL" onclick="return confirm('Dengan menekan tombol OK, Anda akan menghitung RUGI LABA dan NERACA semua transaksi yang sudah terposting, Transaksi yang sudah Terproses Rugi Laba dan Neraca TIDAK DAPAT Diubah Lagi. Bila Anda Yakin Data Sudah BENAR Semua, tekan Ok, tekan Cancel untuk membatalkan proses ini')" class="btn btn-primary pull-right" value="SUBMIT">
                    </form>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </section>
        <!-- /.Left col -->
        <!-- right col -->
        <section class="col-lg-6">
            <?php
//informasi hasil input/update Sukses atau Gagal
            if (isset($_GET["pesan"]) != "") {
                ?>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <i class="fa fa-warning"></i>
                        <h3 class="box-title">Message</h3>
                    </div>
                    <div class="box-body">
                        <?php
                        if (substr($_GET["pesan"],0,5) == "Gagal") { 
                            echo '<div class="callout callout-danger">';
                        }else{
                            echo '<div class="callout callout-success">';
                        }
                        if ($_GET["pesan"] != "") {

                            echo "<p align='justify'>".$_GET["pesan"]."</p>";
                            if (substr($_GET["pesan"],0,5) != "Gagal") {
                                echo "<p align='justify'>Laporan Rugi Laba dan Neraca dapat dilihat melalui Menu Laporan.</p>";
                            }

                        }
                        echo '</div>';
                        ?>
                    </div>
                </div>
            <?php } ?>
        </section>
        <?php
        if(isset($_GET["tglTransaksi"] )){
            $tglTransaksi = secureParam($_GET["tglTransaksi"], $dbLink);
            $tglTransaksi = explode(" - ", $tglTransaksi);
            $tglTransaksi1 = $tglTransaksi[0];
            $tglTransaksi2 = $tglTransaksi[1];
        }else{
            $tglTransaksi1 = "";
            $tglTransaksi2 = "";
        }
        ?>
        <script type="text/javascript">
            function validate(form){
                if(form.starts.value=='' ){
                    alert("Range Transaction Date cannot be Empty! ");
                    $('#tglTransaksi').focus();
                    return false;
                }
            }
        </script>
    </div>
    <!-- /.row -->
</section>