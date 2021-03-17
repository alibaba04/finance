<?php
/* ==================================================
//=======  : Alibaba
==================================================== */
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/perkiraan_detail";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Data Perkiraan';
$hakUser = getUserPrivilege($curPage);

if ($hakUser != 90) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User anda tidak terdaftar untuk mengakses halaman ini!');
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
<script type="text/javascript" src="./js/angka.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(".bestupper").bestupper();
    });
</script>

<SCRIPT language="JavaScript" TYPE="text/javascript">
    function validasiForm(form)
    {

        if(form.txtNIS.value=='' )
        {
            alert("Nomor Induk Siswa harus diisi!");
            form.txtNIS.focus();
            return false;
        }
        if(form.txtnamaSiswa.value=='' )
        {
            alert("Nama Siswa harus diisi!");
            form.txtnamaSiswa.focus();
            return false;
        }
        if(form.txtjKelamin.value=='' )
        {
            alert("Jenis Kelamin harus dipilih!");
            form.txtjKelamin.focus();
            return false;
        }
        if(form.cboKelas.value=='0' )
        {
            alert("Kelas Siswa harus dipilih!");
            form.cboKelas.focus();
            return false;
        }
        if(form.txtnamaOrtu.value=='' )
        {
            alert("Nama Orang Tua harus diisi!");
            form.txtnamaOrtu.focus();
            return false;
        }
        if(form.txtalamatOrtu.value=='' )
        {
            alert("Alamat Orang Tua harus diisi!");
            form.txtaLamatOrtu.focus();
            return false;
        }
        if(form.txtnoHPOrtu.value=='' )
        {
            alert("Nomor HP Orang Tua harus diisi!");
            form.txtnoHPOrtu.focus();
            return false;
        }    
        return true;
    }
</SCRIPT>

<section class="content-header">
    <h1>
        DATA PERKIRAAN
        <small>Detail Data Perkiraan</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Input</li>
        <li class="active">Data Perkiraan</li>
    </ol>
</section>

<section class="content">
    <!-- Main row -->
    <div class="row">
        <section class="col-lg-6">
            <div class="box box-primary">
                <form action="index2.php?page=view/perkiraan_list" method="post" name="frmPerkiraanDetail" onSubmit="return validasiForm(this);" autocomplete="off">
                    <div class="box-header">
                        <i class="ion ion-clipboard"></i>
                        <?php
                        $dataRekening = "";
                        if ($_GET["mode"] == "edit") {
                            echo '<h3 class="box-title">UBAH DATA PERKIRAAN </h3>';
                            echo "<input type='hidden' name='txtMode' value='Edit'>";

//Secure parameter from SQL injection
                            $kode = "";
                            if (isset($_GET["kode"])){
                                $kode = secureParam($_GET["kode"], $dbLink);
                            }

                            $q = "SELECT m.kode_rekening, m.nama_rekening, m.awal_debet, m.awal_kredit, m.posisi, m.normal ";
                            $q.= "FROM aki_tabel_master m ";
                            $q.= "WHERE 1=1 AND md5(m.kode_rekening)='" . $kode . "'";

                            $rsTemp = mysql_query($q, $dbLink);
                            if ($dataRekening = mysql_fetch_array($rsTemp)) {
                                echo "<input type='hidden' name='kodePerkiraan' value='" . $dataRekening[0] . "'>";
                            } else {
                                ?>
                                <script language="javascript">
                                    alert("Kode Tidak Valid");
                                    history.go(-1);
                                </script>
                                <?php
                            }
                        } else {
                            echo '<h3 class="box-title">TAMBAH DATA REKENING </h3>';
                            echo "<input type='hidden' name='txtMode'  value='Add'>";
                        }
                        ?>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label class="control-label" for="txtKodePerkiraan">Kode Akun</label>

                            <input name="txtKodePerkiraan" id="txtKodePerkiraan" maxlength="30" class="form-control" <?php if ($_GET['mode']=='edit') { echo "readonly"; } ?> value="<?php if ($_GET['mode']=='edit') { echo $dataRekening['kode_rekening']; } ?>" placeholder="Wajib diisi" onKeyPress="return handleEnter(this, event)">    
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtNamaPerkiraan">Nama Perkiraan</label>

                            <input name="txtNamaPerkiraan" id="txtNamaPerkiraan" maxlength="100" class="form-control" value="<?php if ($_GET['mode']=='edit') { echo $dataRekening['nama_rekening']; } ?>" placeholder="Wajib diisi" onKeyPress="return handleEnter(this, event)">

                        </div>
                        <div class="form-group">
                            <label class="control-label" for="cboNormal">Normal Balance</label>
                            <select name="cboNormal" id="cboNormal" class="form-control" onKeyPress="return handleEnter(this, event)">
                                <option value="0">--Wajib Pilih Normal Balance--</option>
                                <?php
                                $selected = "";
                                if ($_GET['mode'] == 'edit') {
                                    if ($dataRekening['normal']=="Debit") {
                                        $selected = " selected";
                                        echo "<option value=Debit" . $selected . ">Debit</option>";
                                        echo "<option value=Kredit>Kredit</option>";
                                    }else{
                                        $selected = " selected";
                                        echo "<option value=Debit>Debit</option>";
                                        echo "<option value=Kredit" . $selected . ">Kredit</option>";
                                    }
                                }else{
                                    echo "<option value=Debit>Debit</option>";
                                    echo "<option value=Kredit>Kredit</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="cboPosisi">Posisi</label>
                            <select name="cboPosisi" id="cboPosisi" class="form-control" onKeyPress="return handleEnter(this, event)">
                                <option value="0">--Wajib Pilih Posisi--</option>
                                <?php
                                $selected = "";
                                if ($_GET['mode'] == 'edit') {
                                    if ($dataRekening['posisi']=="LR") {
                                        $selected = " selected";
                                        echo "<option value='LR'" . $selected . ">LABA RUGI</option>";
                                        echo "<option value='NRC'>NERACA</option>";
                                        echo "<option value=''>-</option>";
                                    }else if($dataRekening['posisi']=="NRC"){
                                        $selected = " selected";
                                        echo "<option value='LR'>LAPORAN LABA RUGI</option>";
                                        echo "<option value='NRC'" . $selected . ">NERACA</option>";
                                        echo "<option value=''>-</option>";
                                    }else{
                                        $selected = " selected";
                                        echo "<option value='LR'>LABA RUGI</option>";
                                        echo "<option value='NRC'>NERACA</option>";
                                        echo "<option value=''" . $selected . ">-</option>";
                                    }
                                }else{
                                    echo "<option value='LR'>LABA RUGI</option>";
                                    echo "<option value='NRC'>NERACA</option>";
                                    echo "<option value=''>-</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtAwalDebet">Saldo Awal Debet</label>

                            <input name="txtAwalDebet" id="txtAwalDebet" maxlength="30" class="form-control" 
                            value="<?php if ($_GET['mode']=='edit') { echo $dataRekening['awal_debet']; }else{ echo "0";} ?>" placeholder="Wajib diisi" 
                            onKeyPress="return handleEnter(this, event)" onkeydown="return numbersonly(this, event);" onkeyup="javascript:tandaPemisahTitik(this);">

                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtAwalKredit">Saldo Awal Kredit</label>

                            <input name="txtAwalKredit" id="txtAwalKredit" maxlength="30" class="form-control" 
                            value="<?php if ($_GET['mode']=='edit') { echo $dataRekening['awal_kredit']; }else{ echo "0";} ?>" placeholder="Wajib diisi" 
                            onKeyPress="return handleEnter(this, event)" onkeydown="return numbersonly(this, event);" onkeyup="javascript:tandaPemisahTitik(this);">

                        </div>
                    </div>
                    <div class="box-footer">
                        <input type="submit" class="btn btn-primary" value="Simpan">

                        <a href="index.php?page=view/perkiraan_list">
                            <button type="button" class="btn btn-default pull-right">&nbsp;&nbsp;Batal&nbsp;&nbsp;</button>    
                        </a>
                    </div>
                </form>
            </div>    
        </section>
    </div>
</section>
