<?php
//Author  : Alba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/bukuBesar_list";
error_reporting( error_reporting() & ~E_NOTICE );
//Periksa hak user pada modul/menu ini
$judulMenu = 'Buku Besar';
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
    $(function () {
        $(".select2").select2();
    });
    $(document).ready(function () {
        $('#tglJurnal').val('');
    });
</script>
<style>
    #results {
        max-height: 50px;
        overflow-y: auto;
        /* prevent horizontal scrollbar */
        overflow-x: hidden;
        /* add padding to account for vertical scrollbar */
    } 
</style>
<script type="text/javascript">
    function auto(tcounter){

        $("#txtKodeRekeningbb").autocomplete({
            source: 'function/autotext.php',
minLength: 1,
appendTo: "#results",
open: function() {
    var position = $("#results").position(),
    left = position.left, top = position.top;

    $("#results > ul").css();

},
select: function( event, ui ) {
    $( "#txtKodeRekeningbb" ).val( ui.item.kode );
    $( "#txtNamaRekeningbb" ).val( ui.item.nama );

    return false;
}
}).autocomplete( "instance" )._renderItem = function( ul, item ) {
            return $( "<li>" )
            .append( "<div>" + item.kode + " " + "("+item.nama +")"+ "</div>" )
            .appendTo( ul );
        };
    }
    function cekKode(tcounter){
        $('#txtNamaRekeningbb').val('Checking...');
        $.post("function/ajax_function.php",{ fungsi: "ambilNamaRekening", kodeRekening:$("#txtKodeRekeningbb").val() },function(data)
        {
            if(data.hasil=='yes') 
            {
                $("#txtNamaRekeningbb").val(data.NamaRekening).fadeIn("slow");
            } else if (data.hasil=='no'){
                $("#txtNamaRekeningbb").val(data.NamaRekening).fadeIn("slow");
            }

        },"json"); 
    }
</script>
<section class="content-header">
    <h1>
        LEDGER
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Output</li>
        <li class="active">Ledger</li>
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
                <form name="frmCariJurnal" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>" autocomplete="off">
                    <div class="box-body">
                        <input type="hidden" name="page" value="<?php echo $curPage; ?>">
                        <div class="form-group">
                            <label>Chart of Account</label><br>
                            <?php  
                            $q = "SELECT m.kode_rekening, m.nama_rekening, m.awal_debet, m.awal_kredit, m.posisi, m.normal ";
                            $q.= "FROM aki_tabel_master m  ";
                            $q.= "WHERE 1=1 ORDER BY m.kode_rekening asc ";
                            $sql_coa = mysql_query($q,$dbLink);
                            ?>
                            <select class="form-control select2" name="txtKodeRekeningbb" id="txtKodeRekeningbb">
                                <?php
                                $selected = "";
                                echo '<option value="">Chart Of Account</option>';
                                while($rs_coa = mysql_fetch_assoc($sql_coa)){ 
                                    echo '<option value="'.$rs_coa['kode_rekening'].'">'.$rs_coa['kode_rekening'].' - '.$rs_coa['nama_rekening'].'</option>';
                                }  
                                ?>
                            </select>
                            <div id="results"></div>
                        </div>
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
                        <button type="Submit" class="btn btn-primary pull-right"><i class="fa fa-search"></i> Show</button>
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
                    $namarek=$_GET["txtKodeRekeningbb"];
                    $tgl=$_GET["tglJurnal"];
                }else{
                    $tglJurnal1 = "";
                    $tglJurnal2 = "";
                }
                $filter = "";
                if ($tglJurnal1 && $tglJurnal2)
                    $filter = $filter . " AND t.tanggal_transaksi BETWEEN '" . tgl_mysql($tglJurnal1) . "' 
                AND '" . tgl_mysql($tglJurnal2) . "'  ";

                $q = "SELECT t.no_transaksi,t.tanggal_transaksi, t.kode_transaksi, t.kode_rekening, m.nama_rekening, t.keterangan_transaksi, t.debet, t.kredit ";
                $q.= "FROM aki_tabel_transaksi t INNER JOIN aki_tabel_master m ON t.kode_rekening=m.kode_rekening  AND t.kode_rekening= '". $_GET["txtKodeRekeningbb"]."'  ";
                $q.= "WHERE 1=1 and t.aktif=1 " . $filter;
                $q.= " ORDER BY t.kode_transaksi asc,t.no_transaksi,t.keterangan_transaksi,t.debet desc";
                $rs = mysql_query($q, $dbLink);
                
                $hasilrs = mysql_num_rows($rs);

                $filter2 = "";
                if ($tglJurnal1 && $tglJurnal2){
                    $filter2 = " AND tanggal_transaksi< '" . tgl_mysql($tglJurnal1) . "'  ";
                    $q2 = "SELECT *,(awal_debet+d) as saldo_d, (awal_kredit+k) as saldo_k FROM `aki_tabel_master` m inner join (SELECT tanggal_transaksi,kode_rekening,sum(debet) as d, sum(kredit) as k FROM `aki_tabel_transaksi` WHERE kode_rekening= '". $_GET["txtKodeRekeningbb"]."' ".$filter2.") as t on m.kode_rekening=t.kode_rekening WHERE m.kode_rekening= '". $_GET["txtKodeRekeningbb"]."'";
                }else{
                    $q2 = "SELECT awal_debet as saldo_d, awal_kredit as saldo_k FROM `aki_tabel_master` WHERE kode_rekening= '". $_GET["txtKodeRekeningbb"]."'";
                }
                
                $q3 = "SELECT awal_debet as saldo_d, awal_kredit as saldo_k FROM `aki_tabel_master` WHERE kode_rekening= '". $_GET["txtKodeRekeningbb"]."'";
                $rs2 = mysql_query($q2, $dbLink);
                $rs3 = mysql_query($q3, $dbLink);
                $hasilrs2 = mysql_num_rows($rs2);
                ?>
                <div class="box-header">
                    <i class="ion ion-clipboard"></i>&nbsp;&nbsp;No COA <?php echo $namarek." tanggal : ".$tgl?> 
                    <!-- <a href="pdf/pdf_bukubesar.php?&tglJurnal1=<?=$tglJurnal1; ?>&tglJurnal2=<?=$tglJurnal2;?>&no=<?=$namarek; ?>" title="Cetak PDF Buku Jurnal"><button type="button" class="btn btn-info pull-right"><i class="fa fa-print "></i> Cetak Buku Besar</button></a> -->
                    <a href="excel/c_exportexcel_bb.php?&tglJurnal1=<?=$tglJurnal1; ?>&tglJurnal2=<?=$tglJurnal2; ?>&no=<?=$namarek; ?>"><button class="btn btn-info pull-right"><i class="ion ion-ios-download"></i> Export Excel</button></a>
                </div>

                <div class="box-body">
                    <table class="table table-bordered table-striped table-hover" >
                        <thead>
                            <tr>
                                <th style="width: 5%">Date</th>
                                <th style="width: 5%">Transaction Number</th>
                                <th style="width: 15%">Account</th>
                                <th style="width: 30%">Description</th>
                                <th style="width: 10%">Debit</th>
                                <th style="width: 10%">Credit</th>
                                <th style="width: 10%">Balance</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            $rowCounter = 1; $totDebet=$totKredit=0;
                            $saldo=0;
                            
                            if ($hasilrs>0){
                                if ($hasilrs2>0){
                                    $query_data = mysql_fetch_array($rs2);
                                    echo "<tr>";
                                    echo "<td></td>";
                                    echo "<td></td>";
                                    echo "<td>" . $query_data["kode_rekening"] ." - ".$query_data["nama_rekening"]. ".</td>";
                                    echo "<td> saldo</td>";
                                    echo "<td align='right'>" . number_format($query_data["saldo_d"], 0) . "</td>";
                                    echo "<td align='right'>" . number_format($query_data["saldo_k"], 0) . "</td>";
                                    $saldo = $saldo+$query_data["saldo_d"]-$query_data["saldo_k"];
                                    echo "<td align='right'>" . number_format($saldo, 0) . "</td>";
                                    echo("</tr>");
                                }else{
                                    $query_data = mysql_fetch_array($rs3);
                                    echo "<tr>";
                                    echo "<td></td>";
                                    echo "<td></td>";
                                    echo "<td>" . $query_data["kode_rekening"] ." - ".$query_data["nama_rekening"]. ".</td>";
                                    echo "<td> saldo</td>";
                                    echo "<td align='right'>" . number_format($query_data["saldo_d"], 0) . "</td>";
                                    echo "<td align='right'>" . number_format($query_data["saldo_k"], 0) . "</td>";
                                    $saldo = $saldo+$query_data["saldo_d"]-$query_data["saldo_k"];
                                    echo "<td align='right'>" . number_format($saldo, 0) . "</td>";
                                    echo("</tr>");
                                }
                                
                                while ($query_data = mysql_fetch_array($rs)) {
                                    echo "<tr>";
                                    echo "<td>" . tgl_ind($query_data["tanggal_transaksi"]) . "</td>";
                                    if (strlen($query_data["no_transaksi"]) == 4) {
                                        echo "<td>" . $query_data["kode_transaksi"] . "</td>";
                                    }else{
                                        echo "<td>" . $query_data["no_transaksi"] . "</td>";
                                    }
                                    echo "<td>" . $query_data["kode_rekening"] ." - ".$query_data["nama_rekening"]. ".</td>";
                                    echo "<td>" . $query_data["keterangan_transaksi"] . "</td>";
                                    echo "<td align='right'>" . number_format($query_data["debet"], 0) . "</td>";
                                    echo "<td align='right'>" . number_format($query_data["kredit"], 0) . "</td>";
                                    $saldo = $saldo+$query_data["debet"]-$query_data["kredit"];
                                    echo "<td align='right'>" . number_format($saldo, 0) . "</td>";
                                    echo("</tr>");
                                    $totDebet += $query_data["debet"];
                                    $totKredit += $query_data["kredit"]; 
                                }
                                echo "<tfoot><tr>";
                                echo "<td colspan='4' align='right'>Amount</td>";
                                echo "<td align='right'>". number_format($totDebet, 0) ."</td>";
                                echo "<td align='right'>". number_format($totKredit, 0) ."</td>";
                                echo "<td align='right'><b>". number_format($saldo, 0) ."</b></td>";
                                echo "</tr></tfoot>";
                            } else {
                                if ($hasilrs2>0){
                                    $query_data = mysql_fetch_array($rs2);
                                    echo "<tfoot><tr>";
                                    echo "<td></td>";
                                    echo "<td></td>";
                                    echo "<td>" . $query_data["kode_rekening"] ." - ".$query_data["nama_rekening"]. ".</td>";
                                    echo "<td> Saldo Akhir</td>";
                                    echo "<td align='right'>" . number_format($query_data["saldo_d"], 0) . "</td>";
                                    echo "<td align='right'>" . number_format($query_data["saldo_k"], 0) . "</td>";
                                    $saldo = $saldo+$query_data["saldo_d"]-$query_data["saldo_k"];
                                    echo "<td align='right'><b>". number_format($saldo, 0) ."</b></td>";
                                    echo("</tr></tfoot>");
                                }else{
                                    $query_data = mysql_fetch_array($rs3);
                                    echo "<tfoot><tr>";
                                    echo "<td></td>";
                                    echo "<td></td>";
                                    echo "<td>" . $query_data["kode_rekening"] ." - ".$query_data["nama_rekening"]. ".</td>";
                                    echo "<td> Saldo Akhir</td>";
                                    echo "<td align='right'>" . number_format($query_data["saldo_d"], 0) . "</td>";
                                    echo "<td align='right'>" . number_format($query_data["saldo_k"], 0) . "</td>";
                                    $saldo = $saldo+$query_data["saldo_d"]-$query_data["saldo_k"];
                                    echo "<td align='right'><b>". number_format($saldo, 0) ."</b></td>";
                                    echo("</tr></tfoot>");
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div> 
            </div>
        </section>
    </div>
</section>