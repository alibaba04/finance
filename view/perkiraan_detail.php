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
    die('User cannot access this page!');
    echo "</p>";
}
?>
<!-- Include script date di bawah jika ada field tanggal -->
<script type="text/javascript" src="js/date.js"></script>
<script type="text/javascript" src="js/jquery.datePicker.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="css/datePicker.css">

<!-- Include script di bawah jika ada field yang Huruf Besar semua -->
<script type="text/javascript" src="./js/angka.js"></script>

<SCRIPT language="JavaScript" TYPE="text/javascript">
    function omodal() {
        $("#myModal").modal({backdrop: 'static'});
        $("#txtUpdate1").val();
        $('#btnUpdate').click(function(){
            if($("#txtUpdate1").val()== ''){
                alert('Description Cannot Empty!');
                $("#txtUpdate1").focus();
                return false;
            }
            $("#txtUpdate").val($("#txtUpdate1").val());
        });
    }
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
        CHART OF ACCOUNT
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Input</li>
        <li class="active">Chart of Account</li>
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
                            echo '<h3 class="box-title">UPDATE</h3>';
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
                                    alert("Invalid Code!");
                                    history.go(-1);
                                </script>
                                <?php
                            }
                        } else {
                            echo '<h3 class="box-title">ADD</h3>';
                            echo "<input type='hidden' name='txtMode'  value='Add'>";
                        }
                        ?>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label class="control-label" for="txtKodePerkiraan">Account</label>

                            <input name="txtKodePerkiraan" id="txtKodePerkiraan" maxlength="30" class="form-control" <?php if ($_GET['mode']=='edit') { echo "readonly"; } ?> value="<?php if ($_GET['mode']=='edit') { echo $dataRekening['kode_rekening']; } ?>" placeholder="-- Empty --" onKeyPress="return handleEnter(this, event)">    
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtNamaPerkiraan">Name</label>

                            <input name="txtNamaPerkiraan" id="txtNamaPerkiraan" maxlength="100" class="form-control" value="<?php if ($_GET['mode']=='edit') { echo $dataRekening['nama_rekening']; } ?>" placeholder="-- Empty --" onKeyPress="return handleEnter(this, event)">

                        </div>
                        <div class="form-group">
                            <label class="control-label" for="cboNormal">Normal Balance</label>
                            <select name="cboNormal" id="cboNormal" class="form-control" onKeyPress="return handleEnter(this, event)">
                                <?php
                                $selected = "";
                                if ($_GET['mode'] == 'edit') {
                                    if ($dataRekening['normal']=="Debit") {
                                        $selected = " selected";
                                        echo "<option value=Debit" . $selected . ">Debit</option>";
                                        echo "<option value=Kredit>Credit</option>";
                                    }else{
                                        $selected = " selected";
                                        echo "<option value=Debit>Debit</option>";
                                        echo "<option value=Kredit" . $selected . ">Credit</option>";
                                    }
                                }else{
                                    echo "<option value=Debit>Debit</option>";
                                    echo "<option value=Kredit>Credit</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="cboPosisi">Position</label>
                            <select name="cboPosisi" id="cboPosisi" class="form-control" onKeyPress="return handleEnter(this, event)">
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
                            <label class="control-label" for="txtAwalDebet">Debit Balance</label>

                            <input name="txtAwalDebet" id="txtAwalDebet" maxlength="30" class="form-control" 
                            value="<?php if ($_GET['mode']=='edit') { echo $dataRekening['awal_debet']; }else{ echo "0";} ?>" placeholder="-- Empty --" 
                            onKeyPress="return handleEnter(this, event)" onkeydown="return numbersonly(this, event);" onkeyup="javascript:tandaPemisahTitik(this);">

                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtAwalKredit">Credit Balance</label>

                            <input name="txtAwalKredit" id="txtAwalKredit" maxlength="30" class="form-control" 
                            value="<?php if ($_GET['mode']=='edit') { echo $dataRekening['awal_kredit']; }else{ echo "0";} ?>" placeholder="-- Empty --" 
                            onKeyPress="return handleEnter(this, event)" onkeydown="return numbersonly(this, event);" onkeyup="javascript:tandaPemisahTitik(this);">
                            <input type="hidden" name="txtUpdate" id="txtUpdate" class="form-control" 
                            value="" placeholder="Empty" onKeyPress="return handleEnter(this, event)">

                        </div>
                    </div>
                    <!-- Modal -->
                    <div class="modal fade" id="myModal" role="dialog">
                        <div class="modal-dialog">
                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Upadate Description</h4>
                                </div>
                                <div class="modal-body">
                                    <textarea class="form-control" id="txtUpdate1"></textarea>
                                </div>
                                <div class="modal-footer">
                                    <input type="submit" class="btn btn-primary" value="Save"  id="btnUpdate">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <?php 
                        if ($_GET['mode']=='edit'){
                            echo '<input type="button" class="btn btn-primary" onclick="omodal()" value="Save">';
                        }else{
                            echo '<input type="submit" class="btn btn-primary" value="Save">';
                        }
                        ?>
                        <a href="index.php?page=view/perkiraan_list">
                            <button type="button" class="btn btn-default pull-right">&nbsp;&nbsp;Cancel&nbsp;&nbsp;</button>    
                        </a>
                    </div>
                </form>
            </div>    
        </section>
    </div>
</section>
