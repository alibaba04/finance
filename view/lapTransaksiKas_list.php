<?php
//=======  : Alibaba
defined('validSession') or die('Restricted access');
$curPage = "view/lapTransaksiKas_list";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Transaksi Bank';
$hakUser = getUserPrivilege($curPage);

if ($hakUser < 10) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User anda tidak terdaftar untuk mengakses halaman ini!');
    echo "</p>";
}
//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {

//Seharusnya semua transaksi Add dan Edit Sukses karena data sudah tervalidasi dengan javascript di form detail.
//Jika masih ada masalah, berarti ada exception/masalah yang belum teridentifikasi dan harus segera diperbaiki!
    if (strtoupper(substr($pesan, 0, 5)) == "GAGAL") {
        global $mailSupport;
        $pesan.="Gagal simpan data, mohon hubungi " . $mailSupport . " untuk keterangan lebih lanjut terkait masalah ini.";
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
        $('#tglJurnal').daterangepicker({ 
            locale: { format: 'DD-MM-YYYY' } });
    });

</script>
<!-- End of Script Tanggal -->
<section class="content-header">
    <h1>
        LAPORAN TRANSAKSI KAS
        <small>List Transaksi Kas</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Output</li>
        <li class="active">Laporan Transaksi Kas</li>
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
                    <h3 class="box-title">Kriteria Pencarian Transaksi Kas </h3>
                </div>
                <form name="frmCariJurnal" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <!-- /.box-header -->
                    <div class="box-body">

                        <input type="hidden" name="page" value="<?php echo $curPage; ?>">

                        <div class="form-group">
                            <label>Range Tanggal Transaksi Kas </label>
                            <input type="text" class="form-control" name="tglJurnal" id="tglJurnal" 
                            <?php
                            if (isset($_GET["tglKirim"])) {
                                echo("value='" . $_GET["tglJurnal"] . "'");
                            }
                            ?>
                            onKeyPress="return handleEnter(this, event)">
                        </div>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer clearfix">
                        <button type="Submit" class="btn btn-default pull-right"><i class="fa fa-search"></i> Tampilkan</button>
                    </div>

                </form>

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
                        <h3 class="box-title">Pesan</h3>
                    </div>
                    <div class="box-body">
                        <?php
                        if (substr($_GET["pesan"], 0, 5) == "Gagal") {
                            echo '<div class="callout callout-danger">';
                        } else {
                            echo '<div class="callout callout-success">';
                        }
                        if ($_GET["pesan"] != "") {

                            echo $_GET["pesan"];
                        }
                        echo '</div>';
                        ?>
                    </div>
                </div>
            <?php } ?>
        </section>
        <!-- /.right col -->

        <section class="col-lg-12 connectedSortable">
            <div class="box box-primary">
                <?php
                if (isset($_GET["tglJurnal"])){
                    $tglJurnal = secureParam($_GET["tglJurnal"], $dbLink);
                    $tglJurnal = explode(" - ", $tglJurnal);
                    $tglJurnal1 = $tglJurnal[0];
                    $tglJurnal2 = $tglJurnal[1];
                }else{
                    $tglJurnal1 = "";
                    $tglJurnal2 = "";
                }
                $filter = "";
                if ($tglJurnal1 && $tglJurnal2)
                    $filter = $filter . " AND t.tanggal_transaksi BETWEEN '" . tgl_mysql($tglJurnal1) . "' 
                AND '" . tgl_mysql($tglJurnal2) . "' ";

//database
                $q = "SELECT t.tanggal_transaksi, t.kode_transaksi, t.kode_rekening, t.keterangan_transaksi, t.debet, t.kredit ";
                $q.= "FROM aki_tabel_transaksi t ";
                $q.= "WHERE t.kode_transaksi like 'K%' " . $filter;
                $q.= " ORDER BY t.tanggal_transaksi, id_transaksi ";
                $rs = mysql_query($q, $dbLink);
                $hasilrs = mysql_num_rows($rs);

//Paging
                ?>
                <div class="box-header">
                    <i class="ion ion-clipboard"></i>&nbsp;&nbsp;Data Transaksi Kas <?php if (!empty($tglJurnal1)){ echo " dari tanggal ". $tglJurnal1 . " sampai dengan tanggal ". $tglJurnal2; } ?> 
                    <!-- <a href="pdf/pdf_lapkas.php?&tglJurnal1=<?=$tglJurnal1; ?>&tglJurnal2=<?=$tglJurnal2; ?>" title="Cetak PDF Transaksi Kas"><button type="button" class="btn btn-info pull-right"><i class="fa fa-print "></i> Cetak Transaksi Kas</button></a>&nbsp;&nbsp; -->
                    <a href="excel/c_exportexcel_lkas.php?&tglJurnal1=<?=$tglJurnal1; ?>&tglJurnal2=<?=$tglJurnal2; ?>"><button class="btn btn-info pull-right"><i class="ion ion-ios-download"></i> Export Excel</button></a>
                </div>

                <div class="box-body">
                    <table class="table table-bordered table-striped table-hover" >
                        <thead>
                            <tr>
                                <th style="width: 10%">Tanggal Transaksi</th>
                                <th style="width: 10%">Nomor Bukti</th>
                                <th style="width: 8%">Kode Rekening</th>
                                <th style="width: 42%">Keterangan</th>
                                <th style="width: 15%">Debet</th>
                                <th style="width: 15%">Kredit</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            $rowCounter = 1; $totDebet=$totKredit=0; $selisih = 0;
                            $rowCounter = 1; $totDebet=$totKredit=0; $selisih = 0;
                            $q = "SELECT t.tanggal_transaksi, t.kode_transaksi,m.nama_rekening, t.kode_rekening, t.keterangan_transaksi, t.debet, t.kredit ";
                            $q.= "FROM aki_tabel_transaksi t left join aki_tabel_master m on t.kode_rekening=m.kode_rekening  ";
                            $q.= "WHERE m.nama_rekening like 'Kas%'" . $filter;
                            $q.= " ORDER BY t.tanggal_transaksi, t.id_transaksi ";
                            $rsLap = mysql_query($q, $dbLink);
                            $q_awaldk = mysql_query("SELECT t.tanggal_transaksi, t.kode_transaksi,m.nama_rekening, t.kode_rekening, t.keterangan_transaksi, t.debet, t.kredit FROM aki_tabel_master m WHERE m.nama_rekening like 'Kas Di Bank%' ", $dbLink);
                            while ($query_data = mysql_fetch_array($rsLap)) {
                                echo "<tr>";
                                echo "<td>" . tgl_ind($query_data["tanggal_transaksi"]) . "</td>";
                                echo "<td>" . $query_data["kode_transaksi"] . "</td>";
                                echo "<td>" . $query_data["kode_rekening"] ."</td>";
                                echo "<td>" . $query_data["keterangan_transaksi"] . "</td>";
                                echo "<td align='right'>" . number_format($query_data["debet"], 2) . "</td>";
                                echo "<td align='right'>" . number_format($query_data["kredit"], 2) . "</td>";
                                echo "</tr>";
// $rowCounter++;
                                $totDebet += $query_data["debet"];
                                $totKredit += $query_data["kredit"]; 
                                $selisih = $totDebet - $totKredit;
                            }
                            echo "<tr>";
                            echo "<td colspan='4' align='right'>TOTAL TRANSAKSI</td>";
                            echo "<td align='right'>". number_format($totDebet, 2) ."</td>";
                            echo "<td align='right'>". number_format($totKredit, 2) ."</td>";
                            echo "</tr>";
                            echo "<tr>";
                            echo "<td colspan='4' align='right'>SELISIH</td>";
                            if ($selisih>0){
                                echo "<td align='right'><font color='blue'>". number_format($selisih, 2) ."</font></td>";
                                echo "<td align='right'></td>";
                            }else{
                                echo "<td align='right'></td>";
                                echo "<td align='right'><font color='red'>". number_format($selisih, 2) ."</font></td>";
                            }
                            echo "</tr>";
                            ?>
                        </tbody>
                    </table>
                </div> 
            </div>
        </section>

    </div>
    <!-- /.row -->
</section>
