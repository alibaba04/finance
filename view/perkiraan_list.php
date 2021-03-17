<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/perkiraan_list";
error_reporting( error_reporting() & ~E_NOTICE );
//Periksa hak user pada modul/menu ini
$judulMenu = 'Data Perkiraan';
$hakUser = getUserPrivilege($curPage);

if ($hakUser < 10) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User anda tidak terdaftar untuk mengakses halaman ini!');
    echo "</p>";
}
//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {

    require_once("./class/c_perkiraan.php");
    $tmpPerkiraan = new c_perkiraan();

//Jika Mode Tambah/Add
    if ($_POST["txtMode"] == "Add") {
        $pesan = $tmpPerkiraan->add($_POST);
    }

//Jika Mode Ubah/Edit
    if ($_POST["txtMode"] == "Edit") {
        $pesan = $tmpPerkiraan->edit($_POST);
    }

//Jika Mode Upload
    if ($_POST["txtMode"] == "Upload") {
        $pesan = $tmpPerkiraan->upload($_POST);
    }

//Jika Mode Hapus/Delete
    if ($_GET["txtMode"] == "Delete") {
        $pesan = $tmpPerkiraan->delete($_GET["kodePerkiraan"]);
    }

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
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<section class="content-header">
    <h1>
        DATA PERKIRAAN
        <small>List Perkiraan</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Input</li>
        <li class="active">Data Perkiraan</li>
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
                    <h3 class="box-title">Pencarian Data Perkiraan </h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <form name="frmCariPerkiraan" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>"autocomplete="off">
                        <input type="hidden" name="page" value="<?php echo $curPage; ?>">

                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" name="namaPerkiraan" id="namaPerkiraan" placeholder="Nama Akun..."
                            <?php
                            if (isset($_GET["namaSiswa"])) {
                                echo("value='" . $_GET["namaSiswa"] . "'");
                            }
                            ?>
                            onKeyPress="return handleEnter(this, event)">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-search"></i></button>
                            </span>
                        </div>
                        <p>- atau -</p>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" name="kodePerkiraan" id="kodePerkiraan" placeholder="Kode Akun..."
                            <?php
                            if (isset($_GET["kodePerkiraan"])) {
                                echo("value='" . $_GET["kodePerkiraan"] . "'");
                            }
                            ?>
                            onKeyPress="return handleEnter(this, event)">
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
                        <a href="<?php echo $_SERVER['PHP_SELF']."?page=html/perkiraan_detail&mode=add";?>"><button type="button" class="btn btn-default pull-right"><i class="fa fa-plus"></i> Tambah Data</button></a>
                        <?php
                    }
                    ?>
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
                        <h3 class="box-title">Pesan</h3>
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
                if (isset($_GET["namaPerkiraan"])){
                    $namaPerkiraan = secureParam($_GET["namaPerkiraan"], $dbLink);
                }else{
                    $namaPerkiraan = "";
                }

                if (isset($_GET["kodePerkiraan"])){
                    $kodePerkiraan = secureParam($_GET["kodePerkiraan"], $dbLink);
                }else{
                    $kodePerkiraan = "";
                }

//Set Filter berdasarkan query string
                $filter="";
                if ($namaPerkiraan)
                    $filter = $filter . " AND m.nama_rekening LIKE '%" . $namaPerkiraan . "%'";
                if ($kodePerkiraan)
                    $filter = $filter . " AND m.kode_rekening LIKE '%" . $kodePerkiraan . "%'";

//database
                $q = "SELECT m.kode_rekening, m.nama_rekening, m.awal_debet, m.awal_kredit, m.posisi, m.normal ";
                $q.= "FROM aki_tabel_master m ";
                $q.= "WHERE 1=1 " . $filter;
                $q.= " ORDER BY m.kode_rekening asc ";
//Paging
//$rs = new MySQLPagedResultSet($q, $recordPerPage, $dbLink);
                $rs = new MySQLPagedResultSet($q, 500, $dbLink);
                ?>
                <div class="box-header">
                    <i class="ion ion-clipboard"></i>
                    <ul class="pagination pagination-sm inline"><?php echo $rs->getPageNav($_SERVER['QUERY_STRING']) ?></ul>
                    <!--Cetak PDF dan Export Excel -->
                    <!-- <a href="index2.php?page=<?= $curPage; ?>&mode=lap&tgl1=<?= $tglKirim1; ?>&tgl2=<?= $tglKirim2; ?>" title="Expot Excel"><i class="fa fa-file-excel-o pull-right inline"></i></a><i></i> -->
                    <a href="pdf/pdf_perkiraan.php" title="Cetak PDF CoA"><button type="button" class="btn btn-primary pull-right"><i class="fa fa-print "></i> Cetak CoA</button></a>
                    <!--End Cetak PDF dan Export Excel -->
                </div>

                <div class="box-body">
                    <table class="table table-bordered table-striped table-hover" >
                        <thead>
                            <tr>
                                <th style="width: 5%">Kode Akun</th>
                                <th style="width: 15%">Nama Akun</th>
                                <th style="width: 25%">Awal Debet</th>
                                <th style="width: 25%">Awal Kredit</th>
                                <th style="width: 25%">Posisi</th>
                                <th style="width: 5%">Normal</th>
                                <th colspan="2" width="3%">Aksi</th>

                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            $rowCounter=1;
                            $totDebet = 0; $totKredit = 0;
                            while ($query_data = $rs->fetchArray()) {
                                echo "<tr>";
                                echo "<td>" . $query_data["kode_rekening"] . "</td>";
                                echo "<td>" . $query_data["nama_rekening"] . "</td>";
                                echo "<td align='right'>" . number_format($query_data["awal_debet"],2) . "</td>";
                                echo "<td align='right'>" . number_format($query_data["awal_kredit"],2) . "</td>";
                                echo "<td>" . $query_data["posisi"] . "</td>";
                                echo "<td>" . $query_data["normal"] . "</td>";

                                if ($hakUser == 90) {
                                    echo "<td><span class='label label-success' style='cursor:pointer;' onclick=location.href='" . $_SERVER['PHP_SELF'] . "?page=view/perkiraan_detail&mode=edit&kode=" . md5($query_data["kode_rekening"]) . "'><i class='fa fa-edit'></i>&nbsp;Ubah</span></td>";
                                } else {
                                    echo("<td>&nbsp;</td>");
                                    echo("<td>&nbsp;</td>");
                                }
                                echo("</tr>");
                                $rowCounter++;

                                if (($query_data["kode_rekening"]) == '1110.000' OR ($query_data["kode_rekening"]) == '2110.000' OR ($query_data["kode_rekening"]) == '3000.000' OR ($query_data["kode_rekening"]) == '4000.000' OR ($query_data["kode_rekening"]) == '5000.000') {
                                    $totDebet += $query_data["awal_debet"];
                                    $totKredit += $query_data["awal_kredit"];
                                }
                            }
                            if (!$rs->getNumPages()) {
                                echo("<tr class='even'>");
                                echo ("<td colspan='10' align='center'>Maaf, data tidak ditemukan</td>");
                                echo("</tr>");
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" align="right">JUMLAH</td>
                                <td align="right"><?php echo number_format($totDebet,2); ?></td>
                                <td align="right"><?php echo number_format($totKredit,2); ?></td>
                                <td colspan="3">
                                    <!-- <?php 
                                    if ($totDebet == $totKredit){
                                        echo "<font color='blue'><strong>Balance</strong></font>";
                                    }else{
                                        $selisih = $totDebet-$totKredit;
                                        echo "<font color='red'><strong>Not Balance : ". number_format($selisih)."</strong></font>";
                                    }
                                    ?>   -->  
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div> 
            </div>
        </section>

    </div>
    <!-- /.row -->
</section>
