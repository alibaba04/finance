<?php
//=======  : Alibaba
//Created : November 2020
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/posting_list";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Posting Jurnal';
$hakUser = getUserPrivilege($curPage);

if ($hakUser < 10) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User cannot access this page!');
    echo "</p>";
}
$tglPosting1 = "";
$tglPosting2 = "";
//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {

    require_once("./class/c_posting.php");
    $tmpPosting = new c_posting;

//Jika Mode Tambah/Add
    if ($_POST["sbmPosting"] == "Posting") {
        $tglPosting1 = $_POST['starts'];
        $tglPosting2 = $_POST['ends'];
        $pesan = $tmpPosting->posting($tglPosting1,$tglPosting2);
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
        POSTING 
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Process</li>
        <li class="active">Posting </li>
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
                    <h3 class="box-title"></h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <form name="frmCariJurnalMasuk" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
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
                    <form action="index2.php?page=view/posting_list" method="post" name="frmPosting" onSubmit="return validate(this);" style="text-align: right;">
                        <input type="hidden" id="starts" name="starts">
                        <input type="hidden" id="ends" name="ends" >

                        <input  type="submit" onclick="return confirm('Continue to posting transaction?');" name="sbmPosting" class="btn btn-primary" value="Posting">
                    </form>
                </div>
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
                if(isset($_GET["kodeTransaksi"])){
                    $kodeTransaksi = secureParam($_GET["kodeTransaksi"], $dbLink);
                }else{
                    $kodeTransaksi = "";
                }
                if(isset($_GET["tglTransaksi"] )){
                    $tglTransaksi = secureParam($_GET["tglTransaksi"], $dbLink);
                    $tglTransaksi = explode(" - ", $tglTransaksi);
                    $tglTransaksi1 = $tglTransaksi[0];
                    $tglTransaksi2 = $tglTransaksi[1];
                }else{
                    $tglTransaksi1 = "";
                    $tglTransaksi2 = "";
                }

//Set Filter berdasarkan query string
                $filter="";
                if ($kodeTransaksi)
                    $filter = $filter . " AND j.kode_transaksi LIKE '%" . $kodeTransaksi . "%'";
                if (!empty($tglTransaksi1) || !empty($tglTransaksi2) && ($tglTransaksi1<>$tglTransaksi2))
                    $filter = $filter . " AND t.tanggal_transaksi BETWEEN '" . tgl_mysql($tglTransaksi1) . "' AND '" . tgl_mysql($tglTransaksi2) . "'";
//database
                $q = "SELECT m.nama_rekening,t.kode_transaksi, t.kode_rekening, t.tanggal_transaksi, t.keterangan_transaksi, t.debet, t.kredit, t.tanggal_posting, t.keterangan_posting ";
                $q.= "FROM aki_tabel_transaksi t INNER JOIN aki_tabel_master m ON t.kode_rekening=m.kode_rekening ";
                $q.= "WHERE 1=1 and t.aktif=1 " . $filter;
                $q.= " ORDER BY t.tanggal_transaksi desc, t.id_transaksi, t.kode_rekening desc ";
                $rs = mysql_query($q, $dbLink);
                $hasilrs = mysql_num_rows($rs);
//Paging
//$rs = new MySQLPagedResultSet($q, $recordPerPage, $dbLink);
                ?>
                <div class="box-header">
                    <i class="ion ion-clipboard"></i>
                    &nbsp; <i class='fa fa-check-square-o' aria-hidden='true'></i>in the "Post" column, means the transaction has been posted!
                    <!-- <ul class="pagination pagination-sm inline"></ul> --><!--Tanpa Paging supaya data muncul semua -->
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped table-hover" >
                        <thead>
                            <tr>
                                <th style="width: 2%">#</th>
                                <th style="width: 5%">Transaction Date</th>
                                <th style="width: 5%">Transaction Code</th>
                                <th style="width: 20%">Account</th>
                                <th style="width: 30%">Description</th>
                                <th style="width: 10%">Debit</th>
                                <th style="width: 10%">Credit</th>
                                <th style="width: 2%">Posted?</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $rowCounter=1;
                            $totDebet = 0; $totKredit = 0;
                            if ($hasilrs>0){
                                while ($query_data = mysql_fetch_array($rs)) {
                                    $ketpost="";
                                    if ($query_data["keterangan_posting"]=='Post'){
                                        $ketpost = "<i class='fa fa-check-square-o' aria-hidden='true'></i>";
                                    }
                                    echo "<tr>";
                                    echo "<td>" . $rowCounter . "</td>";
                                    echo "<td>" . tgl_ind($query_data["tanggal_transaksi"]) . "</td>";
                                    echo "<td>" . $query_data["kode_transaksi"] . "</td>";
                                    echo "<td>" . $query_data["kode_rekening"] ." - ".$query_data["nama_rekening"]. "</td>";
                                    echo "<td>" . $query_data["keterangan_transaksi"] . "</td>";
                                    echo "<td align='right'>" . number_format($query_data["debet"],0) . "</td>";
                                    echo "<td align='right'>" . number_format($query_data["kredit"],0) . "</td>";
                                    echo "<td align='center'>" . $ketpost . "</td>";
                                    echo("</tr>");
                                    $totDebet += $query_data["debet"];
                                    $totKredit += $query_data["kredit"];
                                    $rowCounter++;
                                }
                            } else {
                                echo("<tr class='even'>");
                                echo ("<td colspan='8' align='center'>No data found!</td>");
                                echo("</tr>");
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" align="center"><b>Count</td>
                                <td align="right"><b><?php echo number_format($totDebet,0); ?></td>
                                <td align="right"><b><?php echo number_format($totKredit,0); ?></td>
                            </tr>
                            <tr>
                                <td colspan="5" align="center"><b></td>
                                <td colspan="2  "><center>
                                    <?php 
                                    if ($totDebet == $totKredit){
                                        echo "<font color='blue'><strong>Balance</strong></font>";
                                    }else{
                                        $selisih = $totDebet-$totKredit;
                                        echo "<font color='red'><strong>Not Balance : ". number_format($selisih)."</strong></font>";
                                    }
                                    ?></center> 
                                </td>
                            </tr>
                        </tfoot>                              
                    </table>
                </div> 
                <div class="box-footer">
                    
                </div>
            </div>
        </section>
        <script type="text/javascript">
            function validate(form){
                if(form.starts.value=='' )
                {
                    alert("Range Transaction Date cannot be Empty! ");
                    $('#tglTransaksi').focus();
                    return false;
                }
            }
        </script>
    </div>
    <!-- /.row -->
</section>
