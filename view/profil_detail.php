<?php
/* ==================================================
==================================================== */
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/profil_detail";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Data Profil Yayasan';
$hakUser = getUserPrivilege($curPage);

if ($hakUser != 90) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User cannot access this page!');
    echo "</p>";
}
?>
<!-- Include script date di bawah jika ada field tanggal -->
<script type="text/javascript" src="js/date.js"></script>
<script type="text/javascript" src="js/jquery.datePicker.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="css/datePicker.css">

<script type="text/javascript" charset="utf-8">
    $(function()
    {
        $('.date-pick').datePicker({startDate:'01/01/1970'});
    });
</script>
<!-- End of Script Tanggal -->
<!-- Include script di bawah jika ada field yang Huruf Besar semua -->
<script src="js/jquery.bestupper.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(".bestupper").bestupper();
    });
</script>
<SCRIPT language="JavaScript" TYPE="text/javascript">
    function validasiForm(form)
    {

        if(form.txtNamaYayasan.value=='' )
        {
            alert("Nama Yayasan harus diisi!");
            form.txtNamaYayasan.focus();
            return false;
        }
        if(form.txtGedung.value=='' )
        {
            alert("Alamat 1 harus diisi!");
            form.txtGedung.focus();
            return false;
        }
        if(form.txtJalan.value=='' )
        {
            alert("Alamat 2 harus diisi!");
            form.txtJalan.focus();
            return false;
        }
        if(form.txtKelurahan.value=='0' )
        {
            alert("Kelurahan harus diisi!");
            form.txtKelurahan.focus();
            return false;
        }
        if(form.txtPropinsi.value=='' )
        {
            alert("Propinsi harus diisi!");
            form.txtPropinsi.focus();
            return false;
        }
        if(form.txtNegara.value=='' )
        {
            alert("Negara harus diisi!");
            form.txtNegara.focus();
            return false;
        }
        if(form.txtTelepon.value=='' )
        {
            alert("Nomor Telepon harus diisi!");
            form.txtTelepon.focus();
            return false;
        }  
        if(form.txtFax.value=='' )
        {
            alert("Nomor Fax harus diisi!");
            form.txtFax.focus();
            return false;
        }  
        if(form.txtEmail.value=='' )
        {
            alert("Email harus diisi!");
            form.txtEmail.focus();
            return false;
        }
        if(form.txtWebsite.value=='' )
        {
            alert("Website harus diisi!");
            form.txtWebsite.focus();
            return false;
        }
        return true;
    }
</SCRIPT>

<section class="content-header">
    <h1>
        DATA PROFIL YAYASAN
        <small>Detail Data Profil Yayasan</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Pengaturan</li>
        <li class="active">Data Profil Yayasan</li>
    </ol>
</section>

<section class="content">
    <!-- Main row -->
    <div class="row">
        <section class="col-lg-6">
            <div class="box box-primary">
                <form action="index2.php?page=view/profil_list" method="post" name="frmProfilDetail" onSubmit="return validasiForm(this);">
                    <div class="box-header">
                        <i class="ion ion-clipboard"></i>
                        <?php
                        $dataRekening = "";
                        if ($_GET["mode"] == "edit") {
                            echo '<h3 class="box-title">UBAH DATA PROFIL YAYASAN </h3>';
                            echo "<input type='hidden' name='txtMode' value='Edit'>";

//Secure parameter from SQL injection
                            $kode = "";
                            if (isset($_GET["kode"])){
                                $kode = secureParam($_GET["kode"], $dbLink);
                            }

                            $q = "SELECT id, nama_perusahaan, gedung, jalan, kelurahan, kecamatan, kota, propinsi, negara, telepon, fax, email, website ";
                            $q.= "FROM aki_tabel_profil ";
                            $q.= "WHERE 1=1 AND md5(id)='" . $kode . "'";

                            $rsTemp = mysql_query($q, $dbLink);
                            if ($dataProfil = mysql_fetch_array($rsTemp)) {
                                echo "<input type='hidden' name='id' value='" . $dataProfil[0] . "'>";
                            } else {
                                ?>
                                <script language="javascript">
                                    alert("Invalid Code!");
                                    history.go(-1);
                                </script>
                                <?php
                            }
                        } else {
                            echo '<h3 class="box-title">TAMBAH DATA PROFIL PERUSAHAAN </h3>';
                            echo "<input type='hidden' name='txtMode'  value='Add'>";
                        }
                        ?>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label class="control-label" for="txtNamaYayasan">Nama Yayasan</label>

                            <input name="txtNamaYayasan" id="txtNamaYayasan" maxlength="50" class="form-control" value="<?php if ($_GET['mode']=='edit') { echo $dataProfil['nama_perusahaan']; } ?>" placeholder="-- Empty --" onKeyPress="return handleEnter(this, event)">    
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtGedung">Alamat 1</label>

                            <input name="txtGedung" id="txtGedung"  class="form-control" value="<?php if ($_GET['mode']=='edit') { echo $dataProfil['gedung']; } ?>" placeholder="-- Empty --" onKeyPress="return handleEnter(this, event)">

                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtJalan">Alamat 2</label>

                            <input name="txtJalan" id="txtJalan" class="form-control" value="<?php if ($_GET['mode']=='edit') { echo $dataProfil['jalan']; } ?>" placeholder="-- Empty --" onKeyPress="return handleEnter(this, event)">

                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtKelurahan">Kelurahan</label>

                            <input name="txtKelurahan" id="txtKelurahan" class="form-control" value="<?php if ($_GET['mode']=='edit') { echo $dataProfil['kelurahan']; } ?>" placeholder="-- Empty --" onKeyPress="return handleEnter(this, event)">

                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtKecamatan">Kecamatan</label>

                            <input name="txtKecamatan" id="txtKecamatan" class="form-control" value="<?php if ($_GET['mode']=='edit') { echo $dataProfil['kecamatan']; } ?>" placeholder="-- Empty --" onKeyPress="return handleEnter(this, event)">

                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtKota">Kota</label>

                            <input name="txtKota" id="txtKota" class="form-control" value="<?php if ($_GET['mode']=='edit') { echo $dataProfil['kota']; } ?>" placeholder="-- Empty --" onKeyPress="return handleEnter(this, event)">

                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtPropinsi">Propinsi</label>

                            <input name="txtPropinsi" id="txtPropinsi" class="form-control" value="<?php if ($_GET['mode']=='edit') { echo $dataProfil['propinsi']; } ?>" placeholder="-- Empty --" onKeyPress="return handleEnter(this, event)">

                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtNegara">Negara</label>

                            <input name="txtNegara" id="txtNegara" class="form-control" value="<?php if ($_GET['mode']=='edit') { echo $dataProfil['negara']; }else{ echo "Indonesia"; } ?>" onKeyPress="return handleEnter(this, event)">

                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtTelepon">Telepon</label>

                            <input name="txtTelepon" id="txtTelepon" maxlength="12" class="form-control" value="<?php if ($_GET['mode']=='edit') { echo $dataProfil['telepon']; } ?>" placeholder="-- Empty --" onKeyPress="return handleEnter(this, event)">

                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtFax">Fax</label>

                            <input name="txtFax" id="txtFax" maxlength="12" class="form-control" value="<?php if ($_GET['mode']=='edit') { echo $dataProfil['fax']; } ?>" placeholder="-- Empty --" onKeyPress="return handleEnter(this, event)">

                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtEmail">Email</label>

                            <input name="txtEmail" id="txtEmail" maxlength="25" class="form-control" value="<?php if ($_GET['mode']=='edit') { echo $dataProfil['email']; } ?>" placeholder="-- Empty --" onKeyPress="return handleEnter(this, event)">

                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtWebsite">Website</label>

                            <input name="txtWebsite" id="txtWebsite" class="form-control" value="<?php if ($_GET['mode']=='edit') { echo $dataProfil['website']; } ?>" placeholder="-- Empty --" onKeyPress="return handleEnter(this, event)">

                        </div>

                    </div>
                    <div class="box-footer">
                        <input type="submit" class="btn btn-primary" value="Simpan">

                        <a href="index.php?page=view/profil_list">
                            <button type="button" class="btn btn-default pull-right">&nbsp;&nbsp;Batal&nbsp;&nbsp;</button>    
                        </a>

                    </div>
                </form>
            </div>    
        </section>
    </div>
</section>
