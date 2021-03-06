<?php
/* ==================================================
//=======  : Alibaba
==================================================== */
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/jurnalpenyesuaian_detail";
//Periksa hak user pada modul/menu ini
$judulMenu = 'Jurnal Penyesuaian';
$hakUser = getUserPrivilege($curPage);
if ($hakUser != 90) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User cannot access this page!');
    echo "</p>";
}
//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {
    require_once("./class/c_jurnalpenyesuaian.php");
    $tmpJurnalPenyesuaian = new c_jurnalpenyesuaian;
//Jika Mode Tambah/Add
    if ($_POST["txtMode"] == "Add") {
        $pesan = $tmpJurnalPenyesuaian->add($_POST);
    }
//Jika Mode Ubah/Edit
    if ($_POST["txtMode"] == "Edit") {
        $pesan = $tmpJurnalPenyesuaian->edit($_POST);
    }
//Jika Mode Upload
    if ($_POST["txtMode"] == "Upload") {
        $pesan = $tmpJurnalPenyesuaian->upload($_POST);
    }
//Jika Mode Hapus/Delete
    if ($_GET["txtMode"] == "Delete") {
        $pesan = $tmpJurnalPenyesuaian->delete($_GET["kodeTransaksi"]);
    }
//Seharusnya semua transaksi Add dan Edit Sukses karena data sudah tervalidasi dengan javascript di form detail.
//Jika masih ada masalah, berarti ada exception/masalah yang belum teridentifikasi dan harus segera diperbaiki!
    if (strtoupper(substr($pesan, 0, 5)) == "GAGAL") {
        global $mailSupport;
        $pesan.="Warning!!, please text to " . $mailSupport . " for support this error!.";
    }
    header("Location:index.php?page=view/jurnalpenyesuaian_list&pesan=" . $pesan);
    exit;
}
?>
<!-- Include script date di bawah jika ada field tanggal -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="dist/js/jquery-ui.min.js"></script>
<script src="plugins/iCheck/icheck.min.js"></script>
<script src="js/angka.js"></script>
<script type="text/javascript" charset="utf-8">
    $(document).ready(function () { 
        $("#txtTglTransaksi").datepicker({ format: 'dd-mm-yyyy', autoclose:true }); 
    });
</script>
<!-- Include script untuk function auto complete -->
<script type="text/javascript" src="js/autoCompletebox.js"></script>
<SCRIPT language="JavaScript" TYPE="text/javascript">
    function ceksaldo(tcounter){
        var debet = $('#txtDebet_'+tcounter).val();
        var saldo = $('#txtSaldo_'+tcounter).val();
        if((saldo-debet)<0){
            alert('Saldo Akhir Minus!');
            $('#txtDebet_'+tcounter).focus();
        }
    }
  
    function omodal() {
        $("#myModal").modal({backdrop: 'static'});
        $("#txtDelete1").val();
        $('#btnDelete').click(function(){
            if($("#txtDelete1").val()== ''){
                alert('Description Cannot Empty!');
                $("#txtDelete1").focus();
                return false;
            }
            $("#txtDelete").val($("#txtDelete1").val());
        });
    }
    function hitdk(tcounter){
        var idebit = parseInt($('#idebit').val());
        var tdebebit = parseInt($('#txtDebet_'+tcounter).val());
        $("#idebit").val(idebit+tdebebit);
        alert($('#idebit').val());
    }
    function akun(tcounter) {
        $.post("function/ajax_function.php",{ fungsi: "ambilakun" },function(data)
        {
            for(var i=0; i<274; ++i) {
                var x = document.getElementById("txtKodeRekening_"+tcounter);
                var option = document.createElement("option");
                option.text = data[i].text;
                option.value = data[i].val;
                x.add(option);
            }
            
        },"json"); 
    }
  
function addJurnal(){    
    tcounter = $("#jumAddJurnal").val();
    akun(tcounter);
    var currentdate = new Date(); 
    var tgl = currentdate.getDate() 
    + (currentdate.getMonth()+1)  
    + currentdate.getFullYear()
    + currentdate.getHours() 
    + currentdate.getMinutes()  
    + currentdate.getSeconds();
    $("#jumAddJurnal").val(parseInt($("#jumAddJurnal").val())+1);

    var ttable = document.getElementById("kendali");
    var trow = document.createElement("TR");

    //Kolom 1 Checkbox
    var td = document.createElement("TD");
    td.setAttribute("align","center");
    td.style.verticalAlign = 'top';
    td.innerHTML+='<div class="form-group"><input type="checkbox" class="minimal" name="chkAddJurnal_'+tcounter+'" id="chkAddJurnal_'+tcounter+'" value="1" checked /></div>';
    trow.appendChild(td);

    //Kolom 2 Kode Rekening

    var td = document.createElement("TD");
    td.setAttribute("align","left");
    td.style.verticalAlign = 'top';
    td.innerHTML+='<div class="form-group"><select class="form-control select2" name="txtKodeRekening_'+tcounter+'" id="txtKodeRekening_'+tcounter+'"></div></select></div>';
    trow.appendChild(td);

    //Kolom 5 Keterangan
    var td = document.createElement("TD");
    td.setAttribute("align","left");
    td.style.verticalAlign = 'top';
    td.innerHTML+='<div class="form-group"><input name="txtKeterangan_'+tcounter+'" id="txtKeterangan_'+tcounter+'" class="form-control" " placeholder="Wajib Isi"></div>';
    trow.appendChild(td);

    //Kolom 6 Debet
    var td = document.createElement("TD");
    td.setAttribute("align","left");
    td.style.verticalAlign = 'top';
    td.innerHTML+='<div class="form-group"><input name="txtDebet_'+tcounter+'" id="txtDebet_'+tcounter+'" class="form-control" " onkeydown="return numbersonly(this, event);"  value="0" style="text-align:right"></div>';
    trow.appendChild(td);

    //Kolom 7 Kredit
    var td = document.createElement("TD");
    td.setAttribute("align","left");
    td.style.verticalAlign = 'top';
    td.innerHTML+='<div class="form-group"><input name="txtKredit_'+tcounter+'" id="txtKredit_'+tcounter+'" class="form-control" " onkeydown="return numbersonly(this, event);" value="0" style="text-align:right"><input name="txtNoTrans'+tcounter+'" id="txtNoTrans'+tcounter+'" class="form-control" " type="hidden"  value="'+tgl+'" style="text-align:right"></div>';
    trow.appendChild(td);

    ttable.appendChild(trow);
    
    $(".select2").select2();
}

function validasiForm(form)
{
    var tmax=($("#jumAddJurnal").val());
    var idebit = ($('#idebit').val());
    var ikredit = ($('#ikredit').val());
    var tdebit = 0;
    var tkredit = 0;
    for (i=0;i<tmax;i++){
        var d =($('#txtDebet_'+i).val()).replace(/\./g,'');
        var k =($('#txtKredit_'+i).val()).replace(/\./g,'');
        tdebit += parseInt(d);
        tkredit += parseInt(k);
        if ($('#txtKeterangan_'+i).val()=='') {
            alert("Keterangan harus diisi!");
            $('#txtKeterangan_'+i).focus();
            return false;
        }
    }
    $("#idebit").val((tdebit));
    $("#ikredit").val((tkredit));
    if(form.txtTglTransaksi.value=='' )
    {
        alert("Tanggal Transaksi harus diisi!");
        form.txtTglTransaksi.focus();
        return false;
    }
    /*if ($("#idebit").val()!=$("#ikredit").val()) {
        alert("Debit dan Kredit Harus Berjumlah sama!");
        form.txtDebet_0.focus();
        return false;
    }*/

return true;
}
</SCRIPT>

<section class="content-header">
    <h1>
        ADJUSTMENT ENTRIES
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Input</li>
        <li class="active">Adjustment Entries</li>
    </ol>
</section>

<form action="index2.php?page=view/jurnalPenyesuaian_detail" method="post" name="frmSiswaDetail" onSubmit="return validasiForm(this);" autocomplete="off">
    <section class="content">
        <!-- Main row -->
        <div class="row">
            <section class="col-lg-6">
                <div class="box box-primary">
                    <div class="box-header">
                        <i class="ion ion-clipboard"></i>
                        <?php
                        if ($_GET["mode"] == "edit") {
                            echo '<h3 class="box-title">UBAH JURNAL PENYESUAIAN </h3>';
                            echo "<input type='hidden' name='txtMode' value='Edit'>";
                            //Secure parameter from SQL injection
                            if (isset($_GET["kode"])){
                                $kode = secureParam($_GET["kode"], $dbLink);
                            }else{
                                $kode = "";
                            }
                            $q = "SELECT j.nomor_jurnal, j.kode_transaksi, j.tanggal_selesai, t.tanggal_transaksi ";
                            $q.= "FROM aki_jurnal_umum j INNER JOIN aki_tabel_transaksi t ON j.kode_transaksi=t.kode_transaksi ";
                            $q.= "WHERE md5(j.kode_transaksi)='".$kode."'";

                            $rsTemp = mysql_query($q, $dbLink);

                            if ($dataJurnal = mysql_fetch_array($rsTemp)) {
                                echo "<input type='hidden' name='kodeTransaksi' value='" . $dataJurnal["kode_transaksi"] . "'>";
                            } else {
                                ?>
                                <script language="javascript">
                                    alert("Invalid Code! ");
                                    history.go(-1);
                                </script>
                                <?php
                            }
                        } else {
                            echo '<h3 class="box-title">TAMBAH JURNAL PENYESUAIAN </h3>';
                            echo "<input type='hidden' name='txtMode'  value='Add'>";
                        }
                        ?>
                    </div>
                    <div class="box-body">
                        <div class="form-group ">
                            <label class="control-label" for="txtKodeTransaksi">Transaction Code</label>
                            <input name="txtKodeTransaksi" id="txtKodeTransaksi" maxlength="30" class="form-control" 
                            readonly value="<?php if($_GET["mode"]=='edit'){ echo $dataJurnal["kode_transaksi"]; }?>" placeholder="Generating Code . . . . " onKeyPress="return handleEnter(this, event)">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtTglTransaksi">Transaction Date</label>
                            <input name="txtTglTransaksi" id="txtTglTransaksi" maxlength="30" class="form-control" 
                            value="<?php if ($_GET["mode"]=='edit'){ echo tgl_ind($dataJurnal["tanggal_transaksi"]); } ?>" placeholder="Empty" onKeyPress="return handleEnter(this, event)">
                        </div>
                    </div>
                </div>    
            </section>
            <section class="col-lg-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <i class="ion ion-clipboard"></i>
                        <h3 class="box-title">DETAILS</h3>
                        <span id="msgbox"> </span>
                    </div>
                    <div class="box-body">

                        <table class="table table-bordered table-striped table-hover"  >
                            <thead>
                                <tr>
                                    <th style="width: 2%"><i class='fa fa-edit'></i></th>
                                    <th style="width: ">Account</th>
                                    <th style="width: 30%">Description</th>
                                    <th style="width: 15%">Debit</th>
                                    <th style="width: 15%">Credit</th>
                                    <?php
                                    if ($_GET['mode']=='edit'){
                                        echo '<th colspan="2" width="2%"><i class="fa fa-trash"></i></th>';
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody id="kendali">
                                <?php
                                if ($_GET['mode']=='edit'){
                                    $q = "SELECT t.id_transaksi, t.kode_transaksi, t.kode_rekening, t.tanggal_transaksi, t.keterangan_transaksi, t.debet, t.kredit, m.nama_rekening FROM aki_tabel_transaksi t INNER JOIN aki_tabel_master m ON t.kode_rekening=m.kode_rekening WHERE MD5(kode_transaksi)='" . $kode . "' ORDER BY id_transaksi ";
                                    $rsDetilJurnal = mysql_query($q, $dbLink);
                                    $iJurnal = 0;
                                    while ($DetilJurnal = mysql_fetch_array($rsDetilJurnal)) {
                                        echo '<tr>';
                                        echo '<td align="center" valign="top"><div class="form-group">
                                        <input type="checkbox" class="minimal"  name="chkEdit_' . $iJurnal . '" id="chkEdit_' . $iJurnal . '" value="' . $DetilJurnal["id_transaksi"] . '" /></div></td>';

                                        echo '<td align="center" valign="top" width=><div class="form-group"><select class="form-control select2" name="txtKodeRekening_' . $iJurnal . '" id="txtKodeRekening_' . $iJurnal . '">
                                       <option value="'.$DetilJurnal['kode_rekening'].'">'.$DetilJurnal['kode_rekening'].' - '.$DetilJurnal['nama_rekening'].'</option></select></div></td>';

                                        echo '<td align="center" valign="top" width=><div class="form-group">
                                        <input type="text" class="form-control"  name="txtKeterangan_' . $iJurnal . '" id="txtKeterangan_' . $iJurnal . '" value="' . $DetilJurnal["keterangan_transaksi"] . '" /></div></td>';

                                        echo '<td align="center" valign="top" width=><div class="form-group">
                                        <input type="text" onkeydown="return numbersonly(this, event);"  class="form-control"  name="txtDebet_' . $iJurnal . '" id="txtDebet_' . $iJurnal . '" value="' . number_format($DetilJurnal["debet"], 0, ",", ".") . '" style="text-align:right" /></div></td>';

                                        echo '<td align="center" valign="top" width=><div class="form-group">
                                        <input type="text" onkeydown="return numbersonly(this, event);"  class="form-control" name="txtKredit_' . $iJurnal . '" id="txtKredit_' . $iJurnal . '" value="' . number_format($DetilJurnal["kredit"], 0, ",", ".") . '" style="text-align:right" /></div></td>';

                                        echo '<td align="center" valign="top"><div class="form-group">
                                        <input type="checkbox" class="minimal"  name="chkDel_' . $iJurnal . '" id="chkDel_' . $iJurnal . '" value="' . $DetilJurnal["id_transaksi"] . '" /></div></td></tr>';
                                        $iJurnal++;
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                        <input type="hidden" value="<?php if ($_GET['mode']=='edit'){echo $iJurnal;}else{echo 0;} ?>" id="jumAddJurnal" name="jumAddJurnal"/>
                        <input type="hidden" value="0" id="idebit" name="idebit"/>
                        <input type="hidden" value="0" id="ikredit" name="ikredit"/>
                        <center><button type="button" class="btn btn-success" onclick="javascript:addJurnal()">Add General Entries</button></center>
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
                                    <textarea class="form-control" id="txtDelete1"></textarea>
                                </div>
                                <div class="modal-footer">
                                    <input type="submit" class="btn btn-primary" value="Save"  id="btnDelete">
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


                        <a href="index.php?page=html/jurnalpenyesuaian_list">
                            <button type="button" class="btn btn-default pull-right">&nbsp;&nbsp;Cancel&nbsp;&nbsp;</button>    
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </section>
</form>
