<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
require_once("./class/c_hitungrlneraca.php");
defined('validSession') or die('Restricted access');
$curPage = "view/neracaPercobaan_list";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Neraca Percobaan';
$hakUser = getUserPrivilege($curPage);

if ($hakUser < 10) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User cannot access this page!');
    echo "</p>";
}
//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {
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
        $('#tglJurnal').daterangepicker({ 
            locale: { format: 'DD-MM-YYYY' } });
    });

</script>
<section class="content-header">
    <h1>
        TRIAL BALANCE
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Output</li>
        <li class="active">Trial Balance</li>
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
                    <h3 class="box-title">Monthly Period </h3>
                </div>

                <form name="frmCariJurnal" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div class="box-body">
                        <input type="hidden" name="page" value="<?php echo $curPage; ?>">
                        <div class="form-group">
                            <select class="form-control col-lg-6" name="bulan" id="bulan"style="width: 50%">
                                <option value="01">January</option>
                                <option value="02">February</option>
                                <option value="03">March</option>
                                <option value="04">April</option>
                                <option value="05">May</option>
                                <option value="06">June</option>
                                <option value="07">July</option>
                                <option value="08">August</option>
                                <option value="09">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                            <select class="form-control col-lg-6" name="tahun" id="tahun" style="width: 50%">
                                <?php
                                $qry=mysql_query("SELECT tanggal_transaksi FROM aki_tabel_transaksi where tanggal_posting!='0000-00-00' GROUP BY year(tanggal_posting)");
                                while($t=mysql_Fetch_array($qry)){
                                    $data = explode('-',$t['tanggal_transaksi']);
                                    $tahun = $data[0];
                                    echo "<option value='$tahun'>$tahun</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="box-footer clearfix">
                        <button type="Submit" class="btn btn-default pull-right"><i class="fa fa-search"></i> Show</button>
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
                        <h3 class="box-title">Message</h3>
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
                $filter = "";
                $bln = $_GET["bulan"];
                $thn = $_GET["tahun"];
                if (isset($_GET["bulan"])){
                    $filter = $filter . "AND month(t.tanggal_transaksi)= '" . $_GET["bulan"] . "' AND year(t.tanggal_transaksi)= '" . $_GET["tahun"] ."'";
                }else{
                    $filter = "";
                }
                $bln = $_GET["bulan"];
                $thn = $_GET["tahun"];
                //database
                $q = "SELECT m.kode_rekening, m.nama_rekening, m.awal_debet, m.awal_kredit,IFNULL((b.debet),0) as debet,IFNULL((b.kredit),0) as kredit,IFNULL((c.debet),0) as pdebet,IFNULL((c.kredit),0) as pkredit,b.ref,m.normal,m.posisi  FROM `aki_tabel_master` m";
                $q.= " left join (SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet,  sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1 ";
                $q.=$filter." and keterangan_posting='Post' and t.ref='-' GROUP by m.kode_rekening) as b ";
                $q.="on m.kode_rekening=b.kode_rekening left join";
                $q.="(SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet, sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1 ";
                $q.=$filter." and keterangan_posting='Post' and t.ref!='-' GROUP by m.kode_rekening) as c on m.kode_rekening=c.kode_rekening where 1=1 ";
                $q.=" GROUP by m.kode_rekening ORDER BY m.kode_rekening asc" ;
                $rs = mysql_query($q, $dbLink);
                $hasilrs = mysql_num_rows($rs);
                ?>
                <div class="box-header">
                    <i class="ion ion-clipboard"></i>&nbsp;&nbsp;Data <?php if (!empty($tglJurnal1)){ echo  $tglJurnal1 . " to ". $tglJurnal2; } ?> 
                    <a href="excel/c_exportexcel_npercobaan.php?&bulan=<?=$bln; ?>&tahun=<?=$thn; ?>"><button class="btn btn-info pull-right"><i class="ion ion-ios-download"></i> Export Excel</button></a>
                </div>
                <style type="text/css">
                    .ea_table{
                        overflow-x: auto;
                        height:600px;
                        overflow-y: auto;
                    }
                </style>

                <div class="box-body ea_table" >
                    <table class="table table-bordered table-striped table-hover" >
                        <thead>
                            <th style="width: 15%"rowspan="2">Account</th>
                            <th style="width: 10%"colspan="2">Balance</th>
                            <th style="width: 10%"rowspan="2">Debt</th>
                            <th style="width: 10%"rowspan="2">Credit</th>
                            <th style="width: 10%"colspan="2">Trial Balance</th>
                            <th style="width: 10%"colspan="2">Adjustment</th>
                            <th style="width: 10%"colspan="2">Balance after Adjustment</th>
                            <th style="width: 10%"colspan="2">Rugi Laba</th>
                            <th style="width: 10%"colspan="2">Neraca</th>
                            <tr>
                                <th style="width: 5%">Debt</th>
                                <th style="width: 5%">Credit</th>
                                <th style="width: 5%">Debt</th>
                                <th style="width: 5%">Credit</th>
                                <th style="width: 5%">Debt</th>
                                <th style="width: 5%">Credit</th>
                                <th style="width: 5%">Debt</th>
                                <th style="width: 5%">Credit</th>
                                <th style="width: 5%">Debt</th>
                                <th style="width: 5%">Credit</th>
                                <th style="width: 5%">Debt</th>
                                <th style="width: 5%">Credit</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            $rowCounter = 1; +
                            $totADebet=$totAKredit=$totMutDebet=$totMutKredit=$totNDebet=$totNKredit=0;
                            $totPDebet=$totPKredit=$totNsDebet=$totNsKredit=$totRlDebet=$totRlKredit=$totNeDebet=$totNeKredit=0;
                                    
                            if ($hasilrs>0){
                                while ($query_data = mysql_fetch_array($rs)) {
                                    $nsdebet=0;
                                    $nskredit=0;
                                    $nspenyesuaianD=0;
                                    $nspenyesuaianK=0;
                                    echo "<tr>";
                                    echo "<td>" . $query_data["kode_rekening"] . " - " . $query_data["nama_rekening"] . "</td>";
                                    /*echo "<td>" . $query_data["normal"] . "</td>";
                                    echo "<td>" . $query_data["posisi"] . "</td>";*/
                                    echo "<td align='right'>" . number_format($query_data["awal_debet"], 2) ."</td>";
                                    echo "<td align='right'>" . number_format($query_data["awal_kredit"], 2) . "</td>";
                                    echo "<td align='right'>" . number_format($query_data["debet"], 2) . "</td>";
                                    echo "<td align='right'>" . number_format($query_data["kredit"], 2) . "</td>";

                                    if ($query_data["normal"] == 'Debit') {
                                        $nsdebet = $query_data["awal_debet"]+$query_data["debet"]-$query_data["awal_kredit"]-$query_data["kredit"];
                                        $nspenyesuaianD = $nsdebet+$query_data["pdebet"]-$nskredit-$query_data["pkredit"];
                                        echo "<td align='right'>" . number_format($nsdebet, 2) . "</td>";
                                    }else{
                                        echo "<td align='right'>" . number_format(0, 2) . "</td>";
                                    }
                                    if($query_data["normal"] == 'Kredit'){
                                        $nskredit = $query_data["awal_kredit"]+$query_data["kredit"]-$query_data["awal_debet"]-$query_data["debet"];
                                        $nspenyesuaianK = $nskredit+$query_data["pkredit"]-$nsdebet-$query_data["pdebet"];
                                        echo "<td align='right'>" . number_format($nskredit, 2) . "</td>";
                                    }else{
                                        echo "<td align='right'>" . number_format(0, 2) . "</td>";
                                    }
                                    //penyesuaian
                                    echo "<td align='right'>" . number_format($query_data["pdebet"], 2) . "</td>";
                                    echo "<td align='right'>" . number_format($query_data["pkredit"], 2) . "</td>";
                                    //NS Setelah penyesuaian
                                    echo "<td align='right'>" . number_format($nspenyesuaianD, 2) . "</td>";
                                    echo "<td align='right'>" . number_format($nspenyesuaianK, 2) . "</td>";
                                    //LR
                                    if ($query_data["posisi"] == 'LR') {
                                        $totRlDebet += $nspenyesuaianD;
                                        echo "<td align='right'>" . number_format($nspenyesuaianD, 2) . "</td>";
                                    }else{
                                        echo "<td align='right'>" . number_format(0, 2) . "</td>";
                                    }
                                    if($query_data["posisi"] == 'LR'){
                                        $totRlKredit += $nspenyesuaianK;
                                        echo "<td align='right'>" . number_format($nspenyesuaianK, 2) . "</td>";
                                    }else{
                                        echo "<td align='right'>" . number_format(0, 2) . "</td>";
                                    }

                                    //NRC
                                    if ($query_data["posisi"] == 'NRC') {
                                        $totNeDebet += $nspenyesuaianD;
                                        echo "<td align='right'>" . number_format($nspenyesuaianD, 2) . "</td>";
                                    }else{
                                        echo "<td align='right'>" . number_format(0, 2) . "</td>";
                                    }
                                    if($query_data["posisi"] == 'NRC'){
                                        $totNeKredit += $nspenyesuaianK;
                                        echo "<td align='right'>" . number_format($nspenyesuaianK, 2) . "</td>";
                                    }else{
                                        echo "<td align='right'>" . number_format(0, 2) . "</td>";
                                    }
                                    echo("</tr>");
// $rowCounter++;
                                    $totADebet += $query_data["awal_debet"];
                                    $totAKredit += $query_data["awal_kredit"]; 
                                    $totMutDebet += $query_data["debet"];
                                    $totMutKredit += $query_data["kredit"];
                                    $totNDebet += $nsdebet;
                                    $totNKredit += $nskredit; 
                                    $totPDebet += $query_data["pdebet"];
                                    $totPKredit += $query_data["pkredit"];
                                    $totNsDebet += $nspenyesuaianD;
                                    $totNsKredit += $nspenyesuaianK; 
                                }
                                echo "</tr>";
                                echo "<tfoot><tr>";
                                echo "<td colspan='2'></td>";
                                echo "<td align='right'>" . number_format($totADebet, 2) ."</td>";
                                echo "<td align='right'>" . number_format($totAKredit, 2) . "</td>";
                                echo "<td align='right'>" . number_format($totMutDebet, 2) ."</td>";
                                echo "<td align='right'>" . number_format($totMutKredit, 2) . "</td>";
                                echo "<td align='right'>" . number_format($totNDebet, 2) ."</td>";
                                echo "<td align='right'>" . number_format($totNKredit, 2) . "</td>";
                                echo "<td align='right'>" . number_format($totPDebet, 2) . "</td>";
                                echo "<td align='right'>" . number_format($totPKredit, 2) . "</td>";
                                echo "<td align='right'>" . number_format($totNsDebet, 2) . "</td>";
                                echo "<td align='right'>" . number_format($totNsKredit, 2) . "</td>";
                                echo "<td align='right'>" . number_format($totRlDebet, 2) . "</td>";
                                echo "<td align='right'>" . number_format($totRlKredit, 2) . "</td>";
                                echo "<td align='right'>" . number_format($totNeDebet, 2) . "</td>";
                                echo "<td align='right'>" . number_format($totNeKredit, 2) . "</td></tr>";
                                echo "<tr>";
                                echo "<td colspan='11'></td>";

                                $totDneraca = $totKneraca = 0;

                                if ($totRlDebet>$totRlKredit) {
                                    $totDneraca = $totRlDebet-$totRlKredit;
                                    echo "<td align='right'><font color='red'><b>Rugi</td>";
                                    echo "<td align='right'>" . number_format($totDneraca, 2) . "</td>";
                                }else{
                                    echo "<td align='right'>" . number_format(0, 2) . "</td>";
                                }
                                if ($totRlDebet<$totRlKredit) {
                                    $totKneraca = $totRlKredit-$totRlDebet;
                                    echo "<td align='right'><font color='blue'><b>Laba</td>";
                                    echo "<td align='right'>" . number_format($totKneraca, 2) . "</td>";
                                }else{
                                    echo "<td align='right'>" . number_format(0, 2) . "</td>";
                                }
                                echo "<td align='right'>" . number_format($totDneraca, 2) . "</td>";
                                echo "<td align='right'>" . number_format($totKneraca, 2) . "</td></tr>";
                                echo "<tr>";
                                echo "<td colspan='14'></td>";
                                echo "<td align='right'>" . number_format($totNeDebet+$totDneraca, 2) . "</td>";
                                echo "<td align='right'>" . number_format($totNeKredit+$totKneraca, 2) . "</td></tr></tfoot>";

                            } else {
                                echo("<tr class='even'>");
                                echo ("<td colspan='8' align='center'>No data Found!</td>");
                                echo("</tr>");
                            }
                            ?>
                        </tbody>
                    </table>
                </div> 
            </div>
        </section>

    </div>
    <!-- /.row -->
</section>