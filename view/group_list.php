<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/group_list";
$judulMenu = 'Pengaturan Group';
$hakUser = getUserPrivilege($curPage);
if ($hakUser < 10) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User cannot access this page!');
    echo "</p>";
}
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {

    require_once("./class/c_setting.php");
    $tmpGroup=new c_setting();
//Jika Mode Tambah/Add

    if ($_POST["txtMode"]=="Add")
    {
        $pesan=$tmpGroup->addGroup($_POST);
    }

//Jika Mode Ubah/Edit
    if ($_POST["txtMode"]=="Edit")
    {
        $pesan=$tmpGroup->editGroup($_POST); 
    }

//Jika Mode Hapus/Delete
    if ($_GET["txtMode"]=="Delete")
    {
        $pesan=$tmpGroup->deleteGroup($_GET["kodeUser"]);
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
<section class="content-header">
    <h1>
        PENGATURAN GROUP
        <small>List GROUP</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Pengaturan</li>
        <li class="active">Group</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <section class="col-lg-6">
            <div class="box box-primary">
                <div class="box-header">
                    <i class="ion ion-clipboard"></i>
                    <h3 class="box-title">Kriteria Pencarian Group </h3>
                </div>
                <div class="box-body">
                    <form name="frmCariInisiasi" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <input type="hidden" name="page" value="<?php echo $curPage; ?>">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" name="kodeGroup" id="kodeGroup" placeholder="Kode Group..."
                            <?php
                            if (isset($_GET["kodeGroup"])) {
                                echo("value='" . $_GET["kodeGroup"] . "'");
                            }
                            ?>
                            onKeyPress="return handleEnter(this, event)">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-info btn-flat">Go!</button>
                            </span>
                        </div>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" name="namaGroup" id="namaGroup" placeholder="Nama Group..."
                            <?php
                            if (isset($_GET["namaGroup"])) {
                                echo("value='" . $_GET["namaGroup"] . "'");
                            }
                            ?>
                            onKeyPress="return handleEnter(this, event)">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-info btn-flat">Go!</button>
                            </span>
                        </div>
                    </form>
                </div>
                <div class="box-footer clearfix">
                    <?php
                    if ($hakUser == 90) {
                        ?>
                        <a href="<?php echo $_SERVER['PHP_SELF'] . "?page=html/group_detail&mode=add"; ?>"><button type="button" class="btn btn-default pull-right"><i class="fa fa-plus"></i> Tambah Data</button></a>
                        <?php
                    }
                    ?>
                </div>
            </div>
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
                            echo '<div class="callout callout-danger">'. $_GET["pesan"] . '</div>';
                        } else {
                            echo '<div class="callout callout-success">'. $_GET["pesan"] . '</div>';
                        }
                        ?>
                    </div>
                </div>
            <?php } ?>
        </section>
        <section class="col-lg-12 connectedSortable">
            <div class="box box-primary">
                <?php
                if (isset($_GET["namaGroup"])){
                    $namaGroup=secureParam($_GET["namaGroup"], $dbLink);
                }else{
                    $namaGroup="";
                }
                if (isset($_GET["kodeGroup"])){
                    $kodeGroup=secureParam($_GET["kodeGroup"], $dbLink);
                }else{
                    $kodeGroup="";
                }
                $filter = "";
                if($kodeGroup)
                    $filter= $filter." AND g.kodeGroup LIKE '%".$kodeGroup."%'";
                if($namaGroup)
                    $filter= $filter." AND g.nama LIKE '%".$namaGroup."%'";
                $q = "SELECT g.kodeGroup, g.nama ";
                $q.= "FROM aki_groups g ";
                $q.= "WHERE 1 ".$filter;
                $q.= " ORDER BY g.nama";
                $rs = new MySQLPagedResultSet($q, $recordPerPage, $dbLink);
                ?>
                <div class="box-header">
                    <i class="ion ion-clipboard"></i>
                    <ul class="pagination pagination-sm inline"><?= $rs->getPageNav($_SERVER['QUERY_STRING']) ?></ul>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped table-hover" >
                        <thead>
                            <tr>
                                <th style="width: 3%">#</th>
                                <th width="30%" class="sort-alpha">Kode Group</th>
                                <th width="50%" class="sort-alpha">Nama Group</th>
                                <th colspan="2" width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            $rowCounter = 1;
                            while ($query_data = $rs->fetchArray()) {
                                echo "<tr>";
                                echo "<td>" . $rowCounter . "</td>";
                                echo "<td>" . $query_data["kodeGroup"] . "</td>";
                                echo "<td>" . $query_data["nama"] . "</td>";

                                if ($hakUser == 90) {
                                    echo "<td><span class='label label-success' style='cursor:pointer;' onclick=location.href='" . $_SERVER['PHP_SELF'] . "?page=view/group_detail&mode=edit&kode=" . md5($query_data["kodeGroup"]) . "'><i class='fa fa-edit'></i>&nbsp;Ubah</span></td>";

                                    echo("<td><span class='label label-danger' onclick=\"if(confirm('Apakah anda yakin akan menghapus data Group " . $query_data["nama"] . " ?')){location.href='index2.php?page=" . $curPage . "&txtMode=Delete&kodeGroup=" . md5($query_data["kodeGroup"]) . "'}\" style='cursor:pointer;'><i class='fa fa-trash'></i>&nbsp;Hapus</span></td>");

                                } else {
                                    echo("<td>&nbsp;</td>");
                                    echo("<td>&nbsp;</td>");
                                    echo("<td>&nbsp;</td>");
                                }
                                echo("</tr>");
                                $rowCounter++;
                            }
                            if (!$rs->getNumPages()) {
                                echo("<tr class='even'>");
                                echo ("<td colspan='3' align='center'>No data Found!</td>");
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
