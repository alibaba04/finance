<?php
require_once( 'config.php' );
global $dbLink;
$id_kota = $_GET['id_kota'];
$q = "SELECT m.kode_rekening, m.nama_rekening, m.saldo_akhir, m.awal_debet, m.awal_kredit, m.posisi, m.normal ";
$q.= "FROM aki_tabel_master m ";
$q.= "WHERE 1=1 " ;
$q.= " ORDER BY m.kode_rekening asc ";
$query = mysql_query($q,$dbLink);
$data = array();
while($row =mysql_fetch_assoc($query)){
$data[] = array("kode_rekening" => $row['kode_rekening'], "nama_rekening" => $row['nama_rekening']);
}
echo json_encode($data);?>