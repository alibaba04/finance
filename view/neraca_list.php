<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)

defined('validSession') or die('Restricted access');
$curPage = "view/neraca_list";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Neraca Percobaan';
$hakUser = getUserPrivilege($curPage);

if ($hakUser < 10) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User anda tidak terdaftar untuk mengakses halaman ini!');
    echo "</p>";
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
<?php
if(array_key_exists('htParent', $_POST)){
    require_once("./class/c_hitungParent.php");
    $totParent = new c_hitungParent;
    $totParent->totalParent();
}
?>
<!-- End of Script Tanggal -->
<section class="content-header">
    <h1>
        NERACA 
        <small>List Neraca </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Output</li>
        <li class="active">Neraca </li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <!-- Main row -->
    <div class="row">
        <section class="col-lg-6">
            <!-- TO DO List -->
            <div class="box box-primary">
                <div class="box-header">
                    <i class="ion ion-clipboard"></i>
                    <h3 class="box-title">Periode Bulanan </h3>
                </div>
                <form name="frmCariJurnal" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div class="box-body">
                        <input type="hidden" name="page" value="<?php echo $curPage; ?>">
                        <div class="form-group">
                            <label>Range Tanggal Transaksi Jurnal </label>
                            <input type="text" class="form-control" name="tglJurnal" id="tglJurnal" 
                            <?php
                            if (isset($_GET["tglKirim"])) {
                                echo("value='" . $_GET["tglJurnal"] . "'");
                            }
                            ?>
                            onKeyPress="return handleEnter(this, event)">
                        </div>
                    </div>
                    <div class="box-footer clearfix">
                        <button type="Submit" class="btn btn-default pull-right"><i class="fa fa-search"></i> Tampilkan</button>
                    </div>
                </form>
            </div>
            <!-- /.box -->
        </section>
        <section class="col-lg-6">
            <?php
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
                        $TATetap = $TALancar = $TKewajiban = $TEkuitas = 0;
                        ?>
                    </div>
                </div>
            <?php } ?>
        </section>
        <!-- /.right col -->
        <section class="col-lg-12 connectedSortable">
            <div class="box box-primary">
                <div class="box-header">
                    <i class="ion ion-clipboard"></i>&nbsp;&nbsp;Data Neraca 

                    <?php 
                    
                    if (!empty($tglJurnal1)){ echo " dari tanggal ". $tglJurnal1 . " sampai dengan tanggal ". $tglJurnal2; } 
                    $bulan = $tahun = '';
                    if (isset($_GET["bulan"])){
                        $bulan = $_GET["bulan"];
                        $tahun = $_GET["tahun"];
                    }
                    ?>  
                    <a href="excel/c_exportexcel_neraca.php?&bulan=<?=$bulan;?>&tahun=<?=$tahun; ?>"><button class="btn btn-info pull-right"><i class="ion ion-ios-download"></i> Export Excel</button></a>
                    <!-- <form action="index.php?page=view/neracaPercobaan_list" method="post" name="frmPosting">
                        <a href="pdf/pdf_neraca.php?&bulan=<?=$bulan;?>&tahun=<?=$tahun;?>" title="Cetak PDF Neraca Percobaan"><button type="button" class="btn btn-info pull-right"><i class="fa fa-print "></i> Cetak Neraca</button></a> &nbsp;&nbsp;
                        
                    </a></form> -->
                </div>
                <div class="box-body" style="width: 100%;padding-top: 0">
                    <table class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
                        <thead>
                            <tr>
                                <th style="width: 10%">Kode</th>
                                <th style="width: 20%">Nama Akun</th>
                                <th style="width: 10%">Normal</th>
                                <th style="width: 30%"colspan="2" >Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div class="col-md">
                        <div class="box box-default box-solid collapsed-box"style="margin-bottom: 0;">
                            <div class="box-header with-border">
                                <h3 class="box-title">Aktiva Lancar</h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                                    </button>
                                </div>
                                <!-- /.box-tools -->
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <table class="table table-bordered table-striped table-hover"style="height: auto; overflow-y: scroll;">
                                    <?php
                                    $filter = "";
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
                                    $q = "SELECT m.kode_rekening, m.nama_rekening, m.awal_debet, m.awal_kredit,IFNULL((b.debet),0) as debet,IFNULL((b.kredit),0) as kredit,IFNULL((c.debet),0) as pdebet,IFNULL((c.kredit),0) as pkredit,b.ref,m.normal  FROM `aki_tabel_master` m";
                                    $q.= " left join (SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet,  sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1";
                                    $q.=$filter." and ket_hitungrlneraca!='-' and keterangan_posting='Post' and t.ref='-' GROUP by m.kode_rekening) as b ";
                                    $q.="on m.kode_rekening=b.kode_rekening left join";
                                    $q.="(SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet, sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1 ";
                                    $q.=$filter." and ket_hitungrlneraca!='-' and keterangan_posting='Post' and t.ref!='-' GROUP by m.kode_rekening) as c on m.kode_rekening=c.kode_rekening";
                                    $q.=" where m.kode_rekening BETWEEN '1110.000' and '1140.003' or m.kode_rekening BETWEEN '1300.000' and '1453.000' GROUP by m.kode_rekening ORDER BY m.kode_rekening asc" ;
                                    $rs = mysql_query($q, $dbLink);
                                    $hasilrs = mysql_num_rows($rs);
                                    $totADebet=$totAKredit=0;
                                    $nsdebet=0;
                                    $nskredit=0;
                                    $nspenyesuaianD=0;
                                    $nspenyesuaianK=0;
                                    $a = 0;
                                    if ($hasilrs>0){
                                        while ($query_data = mysql_fetch_array($rs)) {
                                            if ($query_data["awal_debet"] != 0 || $query_data["awal_kredit"]!= 0 || $query_data["debet"] != 0 || $query_data["kredit"]!= 0 || $query_data["pdebet"] != 0 || $query_data["pkredit"]!= 0) {
                                                echo "<tr>";
                                                echo "<td align='center'style='width: 10%'>" . $query_data["kode_rekening"] . "</td>";
                                                echo "<td align='left'style='width: 20%'>" . $query_data["nama_rekening"] . "</td>";
                                                echo "<td align='center'style='width: 10%'>" . $query_data["normal"] ."</td>";
                                                if ($query_data["normal"] == 'Debit') {
                                                    $nsdebet = $query_data["awal_debet"]+$query_data["debet"]-$query_data["awal_kredit"]-$query_data["kredit"];
                                                    $nspenyesuaianD = $nsdebet+$query_data["pdebet"]-$nskredit-$query_data["pkredit"];
                                                }else{
                                                    $nskredit = $query_data["awal_kredit"]+$query_data["kredit"]-$query_data["awal_debet"]-$query_data["debet"];
                                                    $nspenyesuaianD = $nskredit+$query_data["pkredit"]-$nsdebet-$query_data["pdebet"];
                                                }

                                                echo "<td align='right'style='width: 15%'>" . number_format( $nspenyesuaianD, 2). "</td>";
                                                echo "<td align='right' style='width: 15%'> </td>";

                                                $totADebet += $nspenyesuaianD;
                                                $totAKredit += $nspenyesuaianK; 
                                            }

                                        } 
                                        echo "</tr></div>";
                                    }else {
                                        echo("<tr class='even'>");
                                        echo ("<td colspan='8' align='center'>Maaf, data tidak ditemukan</td>");
                                        echo("</tr>");
                                    }
                                    ?>
                                </table>  
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                    <div class=""><table class="table table-bordered table-striped table-hover"style="margin-bottom: 0;"><?php
                    echo "<tfooter><tr>";
                    echo "<td align='right' style='width: 40%' ><b>Total Aktiva Lancar</td>";
                    echo "<td align='right' style='width: 10%' ><b></td>";
                    echo "<td align='center' style='width: 30%' ><b>".number_format( $totADebet+$totAKredit, 2)."</td>";
                    echo "</tr></tfooter>";
                    $TALancar = $totADebet+$totAKredit;
                    ?></table>
                </div>
                <div class="col-md">
                    <div class="box box-default box-solid collapsed-box" style="margin-bottom: 0;">
                        <div class="box-header with-border">
                            <h3 class="box-title">Aktiva Tetap</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                                </button>
                            </div>
                            <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table class="table table-bordered table-striped table-hover"style="height: auto; overflow-y: scroll;">
                                <?php
                                $q = "SELECT m.kode_rekening, m.nama_rekening, m.awal_debet, m.awal_kredit,IFNULL((b.debet),0) as debet,IFNULL((b.kredit),0) as kredit,IFNULL((c.debet),0) as pdebet,IFNULL((c.kredit),0) as pkredit,b.ref,m.normal  FROM `aki_tabel_master` m";
                                $q.= " left join (SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet,  sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1";
                                $q.=$filter." and ket_hitungrlneraca!='-' and keterangan_posting='Post' and t.ref='-' GROUP by m.kode_rekening) as b ";
                                $q.="on m.kode_rekening=b.kode_rekening left join";
                                $q.="(SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet, sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1 ";
                                $q.=$filter." and ket_hitungrlneraca!='-' and keterangan_posting='Post' and t.ref!='-' GROUP by m.kode_rekening) as c on m.kode_rekening=c.kode_rekening";
                                $q.=" where m.kode_rekening BETWEEN '1140.004' and '1270.000' or m.kode_rekening BETWEEN '1500.000' and '1790.000' GROUP by m.kode_rekening ORDER BY m.kode_rekening asc" ;
                                $rs = mysql_query($q, $dbLink);
                                $hasilrs = mysql_num_rows($rs);
                                $totADebet=$totAKredit=0;
                                $nsdebet=0;
                                $nskredit=0;
                                $nspenyesuaianD=0;
                                $nspenyesuaianK=0;
                                if ($hasilrs>0){
                                    while ($query_data = mysql_fetch_array($rs)) {
                                        if ($query_data["awal_debet"] != 0 || $query_data["awal_kredit"]!= 0 || $query_data["debet"] != 0 || $query_data["kredit"]!= 0 || $query_data["pdebet"] != 0 || $query_data["pkredit"]!= 0) {
                                            echo "<tr>";
                                            echo "<td align='center'style='width: 10%'>" . $query_data["kode_rekening"] . "</td>";
                                            echo "<td align='left'style='width: 20%'>" . $query_data["nama_rekening"] . "</td>";
                                            echo "<td align='center'style='width: 10%'>" . $query_data["normal"] ."</td>";
                                            if ($query_data["normal"] == 'Debit') {
                                                $nsdebet = $query_data["awal_debet"]+$query_data["debet"]-$query_data["awal_kredit"]-$query_data["kredit"];
                                                $nspenyesuaianD = $nsdebet+$query_data["pdebet"]-$nskredit-$query_data["pkredit"];
                                                echo "<td align='right'style='width: 15%'>" . number_format( $nspenyesuaianD, 2). "</td>";
                                            }else{
                                                $nskredit = $query_data["awal_kredit"]+$query_data["kredit"]-$query_data["awal_debet"]-$query_data["debet"];
                                                $nspenyesuaianK = $nskredit+$query_data["pkredit"]-$nsdebet-$query_data["pdebet"];
                                                echo "<td align='right'style='width: 15%'>" . number_format( $nspenyesuaianK, 2) . "</td>";
                                            }
                                            echo "<td align='right' style='width: 15%'> </td>";

                                            $totADebet += $nspenyesuaianD;
                                            $totAKredit += $nspenyesuaianK; 
                                        }
                                    } 
                                    echo "</tr></div>";
                                }else {
                                    echo("<tr class='even'>");
                                    echo ("<td colspan='8' align='center'>Maaf, data tidak ditemukan</td>");
                                    echo("</tr>");
                                }
                                ?>
                            </table>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <div class=""><table class="table table-bordered table-striped table-hover"style="margin-bottom: 0;"><?php
                $TATetap = $totADebet+$totAKredit;
                echo "<tfooter><tr>";
                echo "<td align='right' style='width: 40%' ><b>Total Aktiva Tetap</td>";
                echo "<td align='right' style='width: 10%' ><b></td>";
                echo "<td align='center' style='width: 30%' ><b>".number_format( $totADebet+$totAKredit, 2)."</td>";
                echo "</tr><tr>";
                echo "<td align='right' style='width: 40%' ><b>TOTAL AKTIVA</td>";
                echo "<td align='right' style='width: 10%' ><b></td>";
                echo "<td align='center' style='width: 30%' ><b>".number_format( $TALancar+$TATetap, 2)."</td>";
                echo "</tr></tfooter>";

                ?></table>
            </div>
            <div class="col-md">
                <div class="box box-default box-solid collapsed-box" style="margin-bottom: 0;">
                    <div class="box-header with-border">
                        <h3 class="box-title">Total Kewajiban</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                            </button>
                        </div>
                        <!-- /.box-tools -->
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered table-striped table-hover"style="height: auto; overflow-y: scroll;">
                            <?php
                            $q = "SELECT m.kode_rekening, m.nama_rekening, m.awal_debet, m.awal_kredit,IFNULL((b.debet),0) as debet,IFNULL((b.kredit),0) as kredit,IFNULL((c.debet),0) as pdebet,IFNULL((c.kredit),0) as pkredit,b.ref,m.normal  FROM `aki_tabel_master` m";
                            $q.= " left join (SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet,  sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1";
                            $q.=$filter." and ket_hitungrlneraca!='-' and keterangan_posting='Post' and t.ref='-' GROUP by m.kode_rekening) as b ";
                            $q.="on m.kode_rekening=b.kode_rekening left join";
                            $q.="(SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet, sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1 ";
                            $q.=$filter." and ket_hitungrlneraca!='-' and keterangan_posting='Post' and t.ref!='-' GROUP by m.kode_rekening) as c on m.kode_rekening=c.kode_rekening";
                            $q.=" where m.kode_rekening BETWEEN '2110.000' and '2310.000' GROUP by m.kode_rekening ORDER BY m.kode_rekening asc" ;
                            $rs = mysql_query($q, $dbLink);
                            $hasilrs = mysql_num_rows($rs);
                            $totADebet=$totAKredit=0;
                            $nsdebet=0;
                            $nskredit=0;
                            $nspenyesuaianD=0;
                            $nspenyesuaianK=0;
                            if ($hasilrs>0){
                                while ($query_data = mysql_fetch_array($rs)) {
                                    if ($query_data["awal_debet"] != 0 || $query_data["awal_kredit"]!= 0 || $query_data["debet"] != 0 || $query_data["kredit"]!= 0 || $query_data["pdebet"] != 0 || $query_data["pkredit"]!= 0) {
                                        echo "<tr>";
                                        echo "<td align='center'style='width: 10%'>" . $query_data["kode_rekening"] . "</td>";
                                        echo "<td align='left'style='width: 20%'>" . $query_data["nama_rekening"] . "</td>";
                                        echo "<td align='center'style='width: 10%'>" . $query_data["normal"] ."</td>";
                                        if ($query_data["normal"] == 'Debit') {
                                            $nsdebet = $query_data["awal_debet"]+$query_data["debet"]-$query_data["awal_kredit"]-$query_data["kredit"];
                                            $nspenyesuaianD = $nsdebet+$query_data["pdebet"]-$nskredit-$query_data["pkredit"];
                                            echo "<td align='right'style='width: 15%'>" . number_format( $nspenyesuaianD, 2). "</td>";
                                        }else{
                                            $nskredit = $query_data["awal_kredit"]+$query_data["kredit"]-$query_data["awal_debet"]-$query_data["debet"];
                                            $nspenyesuaianK = $nskredit+$query_data["pkredit"]-$nsdebet-$query_data["pdebet"];
                                            echo "<td align='right'style='width: 15%'>" . number_format( $nspenyesuaianK, 2) . "</td>";
                                        }
                                        echo "<td align='right' style='width: 15%'> </td>";

                                        $totADebet += $nspenyesuaianD;
                                        $totAKredit += $nspenyesuaianK; 
                                    }
                                } 
                                echo "</tr></div>";
                            }else {
                                echo("<tr class='even'>");
                                echo ("<td colspan='8' align='center'>Maaf, data tidak ditemukan</td>");
                                echo("</tr>");
                            }
                            ?>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <div class=""><table class="table table-bordered table-striped table-hover"style="margin-bottom: 0;"><?php
            echo "<tfooter><tr>";
            echo "<td align='right' style='width: 40%' ><b>Total Kewajiban</td>";
            echo "<td align='right' style='width: 10%' ><b></td>";
            echo "<td align='center' style='width: 30%' ><b>".number_format( $totADebet+$totAKredit, 2)."</td>";
            echo "</tr></tfooter>";
            $TKewajiban = $totADebet+$totAKredit;
            ?></table>
        </div>
        <div class="col-md">
            <div class="box box-default box-solid collapsed-box" style="margin-bottom: 0;">
                <div class="box-header with-border">
                    <h3 class="box-title">Total Ekuitas</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table class="table table-bordered table-striped table-hover"style="height: auto; overflow-y: scroll;">
                        <?php
                        $q = "SELECT m.kode_rekening, m.nama_rekening, m.awal_debet, m.awal_kredit,IFNULL((b.debet),0) as debet,IFNULL((b.kredit),0) as kredit,IFNULL((c.debet),0) as pdebet,IFNULL((c.kredit),0) as pkredit,b.ref,m.normal  FROM `aki_tabel_master` m";
                        $q.= " left join (SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet,  sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1";
                        $q.=$filter." and ket_hitungrlneraca!='-' and keterangan_posting='Post' and t.ref='-' GROUP by m.kode_rekening) as b ";
                        $q.="on m.kode_rekening=b.kode_rekening left join";
                        $q.="(SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet, sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1 ";
                        $q.=$filter." and ket_hitungrlneraca!='-' and keterangan_posting='Post' and t.ref!='-' GROUP by m.kode_rekening) as c on m.kode_rekening=c.kode_rekening";
                        $q.=" where m.kode_rekening BETWEEN '3000.000' and '3390.000' GROUP by m.kode_rekening ORDER BY m.kode_rekening asc" ;
                        $rs = mysql_query($q, $dbLink);
                        $hasilrs = mysql_num_rows($rs);
                        $totADebet=$totAKredit=0;
                        $nsdebet=0;
                        $nskredit=0;
                        $nspenyesuaianD=0;
                        $nspenyesuaianK=0;
                        if ($hasilrs>0){
                            while ($query_data = mysql_fetch_array($rs)) {
                                if ($query_data["awal_debet"] != 0 || $query_data["awal_kredit"]!= 0 || $query_data["debet"] != 0 || $query_data["kredit"]!= 0 || $query_data["pdebet"] != 0 || $query_data["pkredit"]!= 0) {
                                    echo "<tr>";
                                    echo "<td align='center'style='width: 10%'>" . $query_data["kode_rekening"] . "</td>";
                                    echo "<td align='left'style='width: 20%'>" . $query_data["nama_rekening"] . "</td>";
                                    echo "<td align='center'style='width: 10%'>" . $query_data["normal"] ."</td>";
                                    if ($query_data["normal"] == 'Debit') {
                                        $nsdebet = $query_data["awal_debet"]+$query_data["debet"]-$query_data["awal_kredit"]-$query_data["kredit"];
                                        $nspenyesuaianD = $nsdebet+$query_data["pdebet"]-$nskredit-$query_data["pkredit"];
                                        echo "<td align='right'style='width: 15%'>" . number_format( $nspenyesuaianD, 2). "</td>";
                                    }else{
                                        $nskredit = $query_data["awal_kredit"]+$query_data["kredit"]-$query_data["awal_debet"]-$query_data["debet"];
                                        $nspenyesuaianK = $nskredit+$query_data["pkredit"]-$nsdebet-$query_data["pdebet"];
                                        echo "<td align='right'style='width: 15%'>" . number_format( $nspenyesuaianK, 2) . "</td>";
                                    }
                                    echo "<td align='right' style='width: 15%'> </td>";

                                    $totADebet += $nspenyesuaianD;
                                    $totAKredit += $nspenyesuaianK;
                                } 
                            } 
                            echo "</tr></div>";
                        }else {
                            echo("<tr class='even'>");
                            echo ("<td colspan='8' align='center'>Maaf, data tidak ditemukan</td>");
                            echo("</tr>");
                        }
                        ?>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <div class=""><table class="table table-bordered table-striped table-hover"style="margin-bottom: 0;"><?php
        $TEkuitas = $totADebet+$totAKredit;
        echo "<tfooter><tr>";
        echo "<td align='right' style='width: 40%' ><b>Total Ekuitas</td>";
        echo "<td align='right' style='width: 10%' ><b></td>";
        echo "<td align='center' style='width: 30%' ><b>".number_format( $totADebet+$totAKredit, 2)."</td>";
        echo "</tr><tr>";
        echo "<td align='right' style='width: 40%' ><b>TOTAL KEWAJIBAN DAN EKUITAS</td>";
        echo "<td align='right' style='width: 10%' ><b></td>";
        echo "<td align='center' style='width: 30%' ><b>".number_format( $TKewajiban+$TEkuitas, 2)."</td>";
        echo "</tr></tfooter>";
        ?></table>
    </div>
</div> 
</div>
</section>
</div>
<!-- /.row -->
</section>