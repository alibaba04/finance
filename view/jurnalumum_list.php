<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/jurnalumum_list";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Jurnal Umum';
$hakUser = getUserPrivilege($curPage);

if ($hakUser < 10) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User cannot access this page!');
    echo "</p>";
}

//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {

    require_once("./class/c_jurnalumum.php");
    $tmpJurnalUmum = new c_jurnalumum;

//Jika Mode Tambah/Add
    if ($_POST["txtMode"] == "Add") {
        $pesan = $tmpJurnalUmum->add($_POST);
    }

//Jika Mode Ubah/Edit
    if ($_POST["txtMode"] == "Edit") {
        $pesan = $tmpJurnalUmum->edit($_POST);
    }

//Jika Mode Upload
    if ($_POST["txtMode"] == "Upload") {
        $pesan = $tmpJurnalUmum->upload($_POST);
    }

//Jika Mode Hapus/Delete
    if ($_GET["txtMode"] == "Delete") {
        $pesan = $tmpJurnalUmum->delete($_GET["no"],$_GET["kode"],$_GET["ket"],$_GET["desc"]);
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
    });
    function omodal(no,kode,ket) {
        $("#myModal").modal({backdrop: false});
        $("#txtDelete").val();
        $('#btnDelete').click(function(){
            if($("#txtDelete").val()!= ''){
                var desc = $("#txtDelete").val();
                window.location.href='index2.php?page=view/jurnalumum_list&txtMode=Delete&no='+no+'&kode='+kode+'&ket='+ket+'&desc='+desc;
            }else{
                alert('Description Cannot Empty!');
                $("#txtDelete").focus();
            }
        });
    }

</script>
<!-- End of Script Tanggal -->
<section class="content-header">
    <h1>
        GENERAL ENTRIES
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i>Home</a></li>
        <li class="active">Input</li>
        <li class="active">General Entries</li>
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
                    <h3 class="box-title">Search</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <form name="frmCariSiswa" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>" autocomplete="off">
                        <input type="hidden" name="page" value="<?php echo $curPage; ?>">
                        <div class="form-group">
                            <label>Range Transaction Date</label>
                            
                        </div>
                        <div class="input-group input-group-sm">
                            <div class="form-group">
                                <input type="text" class="form-control" name="tglTransaksi" id="tglTransaksi" 
                                <?php
                                if (isset($_GET["tglTransaksi"])) {
                                    echo("value='" . $_GET["tglTransaksi"] . "'");
                                }
                                ?>
                                onKeyPress="return handleEnter(this, event)">
                            </div>
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-search"></i></button>
                            </span>
                        </div>
                    </form>
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix">
                    <?php
                    if ($hakUser==90){
                        ?>
                        <a href="<?php echo $_SERVER['PHP_SELF']."?page=html/jurnalumum_detail&mode=add";?>"><button type="button" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Data</button></a>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <!-- /.box -->
        </section>
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
                    $filter = $filter . " AND t.tanggal_transaksi BETWEEN '" . tgl_mysql($tglTransaksi1) . "' 
                AND '" . tgl_mysql($tglTransaksi2) . "'";

//database
                $q = "SELECT  m.nama_rekening,t.no_transaksi,t.debet,t.kredit,j.nomor_jurnal, j.kode_transaksi, j.tanggal_selesai, t.kode_rekening, t.tanggal_transaksi, t.keterangan_transaksi, t.tanggal_posting, t.keterangan_posting ";
                $q.= "FROM aki_jurnal_umum j INNER JOIN aki_tabel_transaksi t ON j.kode_transaksi=t.kode_transaksi INNER JOIN aki_tabel_master m ON t.kode_rekening=m.kode_rekening ";
                $q.= "WHERE 1=1 and t.aktif=1 and t.ref='-' " . $filter;
                $q.= " group by t.id_transaksi ORDER BY t.id_transaksi,j.kode_transaksi,t.keterangan_transaksi,t.debet desc";
//Paging
                $rs = new MySQLPagedResultSet($q, 100, $dbLink);
                echo $q;
                ?>
                <div class="box-header">
                    <i class="ion ion-clipboard"></i>
                    <ul class="pagination pagination-sm inline"><?php echo $rs->getPageNav($_SERVER['QUERY_STRING']) ?></ul>
                </div>

                <div class="box-body">
                    <table class="table table-bordered table-striped table-hover" >
                        <thead>
                            <tr>
                                <th style="width: 1%">#</th>
                                <th style="width: 5%">Transaction Code</th>
                                <th style="width: 15%">Account</th>
                                <th style="width: 5%">Transaction Date</th>
                                <th style="width: 25%">Description</th>
                                <th style="width: 10%">Debit</th>
                                <th style="width: 10%">Credit</th>
                                <th colspan="2" width="1%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $rowCounter=1;
                            while ($query_data = $rs->fetchArray()) {
                                echo "<tr>";
                                echo "<td>" . $rowCounter . "</td>";
                                if (strlen($query_data["no_transaksi"]) < 11) {
                                    echo "<td>" . $query_data["kode_transaksi"] . "</td>";
                                }else{
                                    echo "<td>" . $query_data["no_transaksi"] . "</td>";
                                }
                                $ket='';
                                if (strpos($query_data["keterangan_transaksi"], 'payin') !== FALSE) {
                                    $tket = explode("ayin",$query_data["keterangan_transaksi"]);
                                    $ket=$tket[1];
                                }else if(strpos($query_data["keterangan_transaksi"], 'payout') !== FALSE){
                                    $tket = explode("ayout",$query_data["keterangan_transaksi"]);
                                    $ket=$tket[1];
                                }else{
                                    $ket=$query_data["keterangan_transaksi"];
                                }
                                echo "<td>" . $query_data["kode_rekening"] ." - ".$query_data["nama_rekening"]. "</td>";
                                echo "<td>" . tgl_ind($query_data["tanggal_transaksi"]) . "</td>";
                                echo "<td>" . $ket . "</td>";
                                echo "<td style='text-align: right;'>" . number_format($query_data["debet"], 0) . "</td>";
                                echo "<td style='text-align: right;'>" . number_format($query_data["kredit"], 0) . "</td>";
                                if ($hakUser == 90) {
                                    
                                    if(empty($query_data["keterangan_posting"])){
                                        echo "<td><a class='label label-success' style='cursor:pointer;' onclick=location.href='" . $_SERVER['PHP_SELF'] . "?page=view/jurnalumum_detail&mode=edit&kode=" . md5($query_data["no_transaksi"]) . "&ket=" . md5($query_data["keterangan_transaksi"]) . "'><i class='fa fa-edit'></i>&nbsp;Update</span></td>";

                                        echo("<td><span class='label label-danger' onclick=\"if(confirm('Apakah anda yakin akan menghapus data Transaksi Jurnal Umum " . $query_data["no_transaksi"] . " ?')){location.href='index2.php?page=" . $curPage . "&txtMode=Delete&no=" . md5($query_data["no_transaksi"]) . "&kode=". md5($query_data["kode_transaksi"]) . "&ket=".md5($query_data["keterangan_transaksi"])."'}\" style='cursor:pointer;'><i class='fa fa-trash'></i>&nbsp;Delete</span></td>");
                                    }else{
                                        if ($_SESSION["my"]->privilege == 'GODMODE' or $_SESSION["my"]->privilege == "ADMIN1") {
                                             echo "<td><a class='label label-success' style='cursor:pointer;' onclick=location.href='" . $_SERVER['PHP_SELF'] . "?page=view/jurnalumum_detail&mode=edit&kode=" . md5($query_data["no_transaksi"]) . "&ket=" . md5($query_data["keterangan_transaksi"]) . "'><i class='fa fa-edit'></i>&nbsp;Update</span></td>";

                                            echo("<td><span class='label label-danger' onclick=omodal('" . md5($query_data['no_transaksi']) . "','" . md5($query_data['kode_transaksi']) ."','" . md5($query_data['keterangan_transaksi']) . "') value='' id='btnModal'style='cursor:pointer;'><i class='fa fa-trash'></i>&nbsp;Delete</span></td>");
                                        }else{
                                            //if (($rowCounter %2) == 0) {
                                               echo("<td><span class='label label-default' ><i class='fa fa-edit'></i>&nbsp;Update</span></td>");
                                               echo("<td><span class='label label-default' ><i class='fa fa-trash'></i>&nbsp;Delete</span></td>");
                                            //}
                                           
                                        }
                                    }

                                } else {
                                    echo("<td>&nbsp;</td>");
                                    echo("<td>&nbsp;</td>");
                                }
                                echo("</tr>");
                                $rowCounter++;
                            }
                            if (!$rs->getNumPages()) {
                                echo("<tr class='even'>");
                                echo ("<td colspan='10' align='center'>No Data Found!</td>");
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

<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Description</h4>
            </div>
            <div class="modal-body">
                <textarea class="form-control" id="txtDelete"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="btnDelete" >Delete</button>
            </div>
        </div>
    </div>
</div>
