<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/bukuJurnal_list";
//Periksa hak user pada modul/menu ini
$judulMenu = 'Buku Jurnal';
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
        GENERAL LEDGER
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Output</li>
        <li class="active">General Ledger</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <section class="col-lg-6">
            <div class="box box-primary">
                <div class="box-header">
                    <i class="ion ion-clipboard"></i>
                    <h3 class="box-title">Search</h3>
                </div>
                <form name="frmCariJurnal" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div class="box-body">
                        <input type="hidden" name="page" value="<?php echo $curPage; ?>">
                        <div class="form-group">
                            <label>Range Transaction Date</label>
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
                        <button type="Submit" class="btn btn-default pull-right"><i class="fa fa-search"></i> Show</button>
                    </div>
                </form>
            </div>
        </section>
        <section class="col-lg-6">
            <?php
//informasi hasil input/update Sukses atau Gagal
            if (isset($_GET["pesan"]) != "") {
                ?>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <i class="fa fa-warning"></i>
                        <h3 class="box-title"></h3>
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
                $q = "SELECT t.tanggal_transaksi, t.kode_transaksi, t.kode_rekening, m.nama_rekening, t.keterangan_transaksi, t.debet, t.kredit ";
                $q.= "FROM aki_tabel_transaksi t INNER JOIN aki_tabel_master m ON t.kode_rekening=m.kode_rekening ";
                $q.= "WHERE 1=1 AND t.ref not like 'RL%' " . $filter;
                $q.= " ORDER BY t.tanggal_transaksi, id_transaksi ";
                $rss = new MySQLPagedResultSet($q, 100, $dbLink);
                $rs = mysql_query($q, $dbLink);

                $hasilrs = mysql_num_rows($rs);

                ?>
                <div class="box-header">
                    <i class="ion ion-clipboard"></i><ul class="pagination pagination-sm inline"><?php echo $rss->getPageNav($_SERVER['QUERY_STRING']) ?></ul>
                    <!-- <a href="pdf/pdf_bukujurnal.php?&tglJurnal1=<?=$tglJurnal1; ?>&tglJurnal2=<?=$tglJurnal2; ?>" title="Cetak PDF Buku Jurnal"><button type="button" class="btn btn-info pull-right"><i class="fa fa-print "></i> Cetak Buku Jurnal</button></a>&nbsp;&nbsp; -->
                    <a href="excel/c_exportexcel_jurnal.php?&tglJurnal1=<?=$tglJurnal1; ?>&tglJurnal2=<?=$tglJurnal2; ?>"><button class="btn btn-info pull-right"><i class="ion ion-ios-download"></i> Export Excel</button></a>
                    <a href="index.php?page=view/bukuJurnal_list"><button class="btn btn-info pull-right"><i class="ion ion-refresh"></i> Refresh</button></a> 
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped table-hover" >
                        <thead>
                            <tr>
                                <th style="width: 5%">Date</th>
                                <th style="width: 5%">Transaction Number</th>
                                <th style="width: 15%">Description</th>
                                <th style="width: 10%">Account</th>
                                <th style="width: 10%">Debt</th>
                                <th style="width: 10%">Credit</th>

                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $rowCounter = 1; $totDebet=$totKredit=0;
                            if ($hasilrs>0){
                                while ($query_data = mysql_fetch_array($rs)) {
                                    echo "<tr>";
                                    echo "<td>" . tgl_ind($query_data["tanggal_transaksi"]) . "</td>";
                                    echo "<td>" . $query_data["kode_transaksi"] . "</td>";
                                    echo "<td>" . $query_data["keterangan_transaksi"] . "</td>";
                                    echo "<td>" . $query_data["kode_rekening"] ." - ".$query_data["nama_rekening"]. ".</td>";
                                    echo "<td align='right'>" . number_format($query_data["debet"], 2) . "</td>";
                                    echo "<td align='right'>" . number_format($query_data["kredit"], 2) . "</td>";
                                    echo("</tr>");
                                    $totDebet += $query_data["debet"];
                                    $totKredit += $query_data["kredit"]; 
                                }
                                echo "<tr>";
                                echo "<td colspan='4' align='right'>Amount</td>";
                                echo "<td align='right'>". number_format($totDebet, 2) ."</td>";
                                echo "<td align='right'>". number_format($totKredit, 2) ."</td>";
                                echo "</tr>";
                            } else {
                                echo("<tr class='even'>");
                                echo ("<td colspan='6' align='center'>No data found!</td>");
                                echo("</tr>");
                            }
                            ?>
                        </tbody>
                    </table>
                </div> 
            </div>
        </section>
    </div>
</section>