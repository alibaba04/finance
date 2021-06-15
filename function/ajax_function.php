<?php

require_once('../config.php' );
require_once('../function/secureParam.php');
//require_once('../function/mysql.php');

switch ($_POST['fungsi']) {
    case "checkKodeMenu":

        //echo "yes";

        $result = mysql_query("select kodeMenu FROM aki_menu WHERE kodeMenu ='" . secureParamAjax($_POST['kodeMenu'], $dbLink) . "'", $dbLink);

        if (mysql_num_rows($result)) {
             echo "yes";
        } else {
             echo "no";
        }
        break;

    case "checkKodeGroup":
        $result = mysql_query("select KodeGroup FROM aki_groups WHERE KodeGroup ='" . secureParamAjax($_POST['kodeGroup'], $dbLink) . "'", $dbLink);
        if (mysql_num_rows($result)) {
            echo "yes";
        } else {
            echo "no";
        }
        break;

    case "checkKodeUser":
        $result = mysql_query("select kodeUser FROM aki_user WHERE kodeUser ='" . secureParamAjax($_POST['kodeUser'], $dbLink) . "'", $dbLink);
        if (mysql_num_rows($result)) {
            echo "yes";
        } else {
            echo "no";
        }
        break;

    case "checkKodeKota":
        if ($_POST['kodeKota'] != "") {
            $result = mysql_query("select KodeKota from aki_kota where KodeKota ='" . strtoupper($_POST['kodeKota']) . "'");
            if (mysql_num_rows($result)) {
                echo "yes";
            } else {
                echo "no";
            }
        } else {
            echo "none";
        }
        break;

    case "cekpass":
        $kodeUser = secureParamAjax($_POST['kodeUser'], $dbLink);
        $pass = HASH('SHA512',$passSalt.secureParamAjax($_POST['pass'], $dbLink));
        $result = mysql_query("SELECT kodeUser, nama FROM aki_user WHERE kodeUser='".$kodeUser."' AND  password='".$pass."' AND aktif='Y'", $dbLink);
        if (mysql_num_rows($result)) {
            echo "yes";
        } else {
            echo "no";
        }
    break;

    case "ambilNamaRekening":
        if ($_POST['kodeRekening'] != "") {
            $result = mysql_query("SELECT nama_rekening,normal FROM aki_tabel_master WHERE kode_rekening ='" . $_POST['kodeRekening'] . "'", $dbLink);
            if (mysql_num_rows($result)>0) {
                $data = mysql_fetch_array($result);

                echo json_encode(array("hasil"=>"yes", "NamaRekening"=>$data['nama_rekening'],"normal"=>$data['normal']));
                break;
                
            } else {
                $namaRekening ="Kode Rekening Tidak Valid";
                echo json_encode(array("hasil"=>"no", "NamaRekening"=>$namaRekening));
                break;
            }
            echo json_encode(array("hasil"=>"no", "NamaRekening"=>$result));
        } 
    break;
    case "ambilKodeRekening1":
        if ($_POST['NamaRekening'] != "") {
            $result = mysql_query("SELECT kode_rekening,normal FROM aki_tabel_master WHERE nama_rekening ='" . $_POST['NamaRekening'] . "'", $dbLink);
            if (mysql_num_rows($result)>0) {
                $data = mysql_fetch_array($result);
                echo json_encode(array("hasil"=>"yes", "KodeRekening"=>$data['kode_rekening'],"normal"=>$data['normal']));
                break;
                
            } else {
                $KodeRekening ="Kode Rekening Tidak Valid";
                echo json_encode(array("hasil"=>"no", "KodeRekening"=>$KodeRekening));
                break;
            }
        } 
    break;
    case "ambilakun":
        $result = mysql_query("SELECT kode_rekening, nama_rekening FROM aki_tabel_master order by kode_rekening", $dbLink);
            if (mysql_num_rows($result)>0) {
                $idx = 0;
                while ( $data = mysql_fetch_assoc($result)) {

                    $output[$idx] = array("val"=>$data['kode_rekening'],"text"=>$data['kode_rekening'].' - '.$data['nama_rekening']);
                    $idx++;
                 } 
                echo json_encode($output);
                break;
            }
    break;
    case "checkNamaSetting":

        //echo "yes";

        $result = mysql_query("select namaSetting FROM aki_setting WHERE namaSetting ='" . secureParamAjax($_POST['namaSetting'], $dbLink) . "'", $dbLink);

        if (mysql_num_rows($result)) {
             echo "yes";
        } else {
             echo "no";
        }
        break;

    case "ambilKodeRekening":
            $result = mysql_query("SELECT kode_rekening, nama_rekening FROM aki_tabel_master ", $dbLink);
            if (mysql_num_rows($result)>0) {
                $idx = 0;
                while ( $data = mysql_fetch_assoc($result)) {

                    $output[$idx] = array($data['kode_rekening']);
                    $idx++;
                 } 
                echo json_encode($output);
                break;
                
        } 
    break;
    case "ambilNamaRekening1":
            $result = mysql_query("SELECT kode_rekening, nama_rekening FROM aki_tabel_master ", $dbLink);
            if (mysql_num_rows($result)>0) {
                $idx = 0;
                while ( $data = mysql_fetch_assoc($result)) {

                    $output[$idx] = array($data['nama_rekening']);
                    $idx++;
                 } 
                echo json_encode($output);
                break;
                
        } 
    break;
   
    case "ambilPendapatan":
        $filter = "";
        if (isset($_POST["bulan"])){
            $filter = $filter . " AND month(t.tanggal_transaksi)= '" . $_POST["bulan"] . "' AND year(t.tanggal_transaksi)= '" . $_POST["tahun"] ."'";
        }else{
            $filter = "";
        }
        $q = "SELECT m.kode_rekening, m.nama_rekening, m.awal_debet, m.awal_kredit,IFNULL((b.debet),0) as debet,IFNULL((b.kredit),0) as kredit,IFNULL((c.debet),0) as pdebet,IFNULL((c.kredit),0) as pkredit,b.ref,m.normal  FROM `aki_tabel_master` m";
        $q.= " left join (SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet,  sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1";
        $q.=$filter." and ket_hitungrlneraca!='-' and keterangan_posting='Post' and t.ref='-' GROUP by m.kode_rekening) as b ";
        $q.="on m.kode_rekening=b.kode_rekening left join";
        $q.="(SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet, sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1 ";
        $q.=$filter." and ket_hitungrlneraca!='-' and keterangan_posting='Post' and t.ref!='-' GROUP by m.kode_rekening) as c on m.kode_rekening=c.kode_rekening";
        $q.=" where m.kode_rekening BETWEEN '4000.000' and '4300.000' GROUP by m.kode_rekening ORDER BY m.kode_rekening asc" ;
        $result = mysql_query($q, $dbLink);
        if (mysql_num_rows($result)>0) {
            $idx = 0;
            while ( $data = mysql_fetch_assoc($result)) {

                $output[$idx] = array($data['nama_rekening']);
                $idx++;
             } 
            echo json_encode($output);
            break;
            
        } 
    break;
}
?>
