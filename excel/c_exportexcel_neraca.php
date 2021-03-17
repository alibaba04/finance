<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<style type="text/css">
		body{
			font-family: sans-serif;
		}
		table{
			margin: 20px auto;
			border-collapse: collapse;
		}
		table th,
		table td{
			border: 1px solid #3c3c3c;
			padding: 3px 8px;

		}
		a{
			background: blue;
			color: #fff;
			padding: 8px 10px;
			text-decoration: none;
			border-radius: 2px;
		}
	</style>

	<?php
	require_once('../function/fungsi_formatdate.php');
	$tanggal = date("Y-m-d h", time());
	header("Content-type: application/vnd-ms-excel");
	header("Content-Disposition: attachment; filename=neraca_".$tanggal.".xls");
	$koneksi = mysqli_connect("localhost","u5514609_can",",S1s6h8+Mrc)","u5514609_dbaki");
	$TATetap = $TALancar = $TKewajiban = $TEkuitas = 0;
	?>

	<center>
		<h1>Export Data Neraca<br/></h1>
		<h3>Periode Bulan <?php echo namaBulan_id($_GET["bulan"]).' '.$_GET["tahun"]; ?></h3>
	</center>

	<table border="1">
		<thead>
			<tr>
				<th style="width: 10%">Kode</th>
				<th style="width: 20%">Nama Akun</th>
				<th style="width: 10%">Normal</th>
				<th style="width: 30%"colspan='2'>Saldo</th>
			</tr>
		</thead>
		<?php 
// koneksi database

		$filter = "";
		$bulan = $_GET['bulan'];
		$tahun = $_GET['tahun'];
		$totADebet=$totAKredit=0;
		$nsdebet=0;
		$nskredit=0;
		$nspenyesuaianD=0;
		$nspenyesuaianK=0;
		if (($bulan)!=''){
			$filter = $filter . " AND month(t.tanggal_transaksi)= '" . $bulan . "' AND year(t.tanggal_transaksi)= '" . $tahun ."'";
		}else{
			$filter = "";
		}
//database
		$q = "SELECT m.kode_rekening, m.nama_rekening, m.awal_debet, m.awal_kredit,IFNULL((b.debet),0) as debet,IFNULL((b.kredit),0) as kredit,IFNULL((c.debet),0) as pdebet,IFNULL((c.kredit),0) as pkredit,b.ref,m.normal  FROM `aki_tabel_master` m";
		$q.= " left join (SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet,  sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1";
		$q.=$filter." and ket_hitungrlneraca!='-' and keterangan_posting='Post' and t.ref='-' GROUP by m.kode_rekening) as b ";
		$q.="on m.kode_rekening=b.kode_rekening left join";
		$q.="(SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet, sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1 ";
		$q.=$filter." and ket_hitungrlneraca!='-' and keterangan_posting='Post' and t.ref!='-' GROUP by m.kode_rekening) as c on m.kode_rekening=c.kode_rekening";
		$q.=" where m.kode_rekening BETWEEN '1110.000' and '1140.003' or m.kode_rekening BETWEEN '1300.000' and '1453.000' GROUP by m.kode_rekening ORDER BY m.kode_rekening asc" ;
// menampilkan data pegawai
		$data = mysqli_query($koneksi,$q);
		while($query_data = mysqli_fetch_array($data)){
			if ($query_data["awal_debet"] != 0 || $query_data["awal_kredit"]!= 0 || $query_data["debet"] != 0 || $query_data["kredit"]!= 0 || $query_data["pdebet"] != 0 || $query_data["pkredit"]!= 0) {
				echo "<tr>";
				echo "<td align='center'style='width: 10%'>'" . $query_data["kode_rekening"] . "</td>";
				echo "<td align='left'style='width: 20%'>" . $query_data["nama_rekening"] . "</td>";
				echo "<td align='center'style='width: 10%'>" . $query_data["normal"] ."</td>";
				if ($query_data["normal"] == 'Debit') {
					$nsdebet = $query_data["awal_debet"]+$query_data["debet"]-$query_data["awal_kredit"]-$query_data["kredit"];
					$nspenyesuaianD = $nsdebet+$query_data["pdebet"]-$nskredit-$query_data["pkredit"];
				}else{
					$nskredit = $query_data["awal_kredit"]+$query_data["kredit"]-$query_data["awal_debet"]-$query_data["debet"];
					$nspenyesuaianD = $nskredit+$query_data["pkredit"]-$nsdebet-$query_data["pdebet"];
				}

				echo "<td align='right'style='width: 15%'>" . number_format( $nspenyesuaianD, 2). "</td>";
				echo "<td align='right' style='width: 15%'> </td>";

				$totADebet += $nspenyesuaianD;
				$totAKredit += $nspenyesuaianK; 
			}
		}
		$TALancar = $totADebet+$totAKredit;
		echo "<tfooter><tr>";
		echo "<td align='right' style='width: 50%' colspan='3' ><b>Total Aktiva Lancar</td>";
		echo "<td align='center' style='width: 50%' colspan='2' ><b>".number_format( $totADebet+$totAKredit, 2)."</td>";
		echo "</tr></tfooter>";
		?>
	</table>

	<table border="1">
		
		<?php 
		// koneksi database
		
		$filter = "";
		$bulan = $_GET['bulan'];
		$tahun = $_GET['tahun'];
		$totADebet=$totAKredit=0;
		$nsdebet=0;
		$nskredit=0;
		$nspenyesuaianD=0;
		$nspenyesuaianK=0;
		if (($bulan)!=''){
			$filter = $filter . " AND month(t.tanggal_transaksi)= '" . $bulan . "' AND year(t.tanggal_transaksi)= '" . $tahun ."'";
		}else{
			$filter = "";
		}
    //database
		$q = "SELECT m.kode_rekening, m.nama_rekening, m.awal_debet, m.awal_kredit,IFNULL((b.debet),0) as debet,IFNULL((b.kredit),0) as kredit,IFNULL((c.debet),0) as pdebet,IFNULL((c.kredit),0) as pkredit,b.ref,m.normal  FROM `aki_tabel_master` m";
		$q.= " left join (SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet,  sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1";
		$q.=$filter." and ket_hitungrlneraca!='-' and keterangan_posting='Post' and t.ref='-' GROUP by m.kode_rekening) as b ";
		$q.="on m.kode_rekening=b.kode_rekening left join";
		$q.="(SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet, sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1 ";
		$q.=$filter." and ket_hitungrlneraca!='-' and keterangan_posting='Post' and t.ref!='-' GROUP by m.kode_rekening) as c on m.kode_rekening=c.kode_rekening";
		$q.=" where m.kode_rekening BETWEEN '1140.004' and '1270.000' or m.kode_rekening BETWEEN '1500.000' and '1790.000' GROUP by m.kode_rekening ORDER BY m.kode_rekening asc" ;
		// menampilkan data pegawai
		$data = mysqli_query($koneksi,$q);
		while($query_data = mysqli_fetch_array($data)){
			if ($query_data["awal_debet"] != 0 || $query_data["awal_kredit"]!= 0 || $query_data["debet"] != 0 || $query_data["kredit"]!= 0 || $query_data["pdebet"] != 0 || $query_data["pkredit"]!= 0) {
				echo "<tr>";
				echo "<td align='center'style='width: 10%'>" . $query_data["kode_rekening"] . "</td>";
				echo "<td align='left'style='width: 20%'>" . $query_data["nama_rekening"] . "</td>";
				echo "<td align='center'style='width: 10%'>" . $query_data["normal"] ."</td>";
				if ($query_data["normal"] == 'Debit') {
					$nsdebet = $query_data["awal_debet"]+$query_data["debet"]-$query_data["awal_kredit"]-$query_data["kredit"];
					$nspenyesuaianD = $nsdebet+$query_data["pdebet"]-$nskredit-$query_data["pkredit"];
				}else{
					$nskredit = $query_data["awal_kredit"]+$query_data["kredit"]-$query_data["awal_debet"]-$query_data["debet"];
					$nspenyesuaianD = $nskredit+$query_data["pkredit"]-$nsdebet-$query_data["pdebet"];
				}

				echo "<td align='right'style='width: 15%'>" . number_format( $nspenyesuaianD, 2). "</td>";
				echo "<td align='right' style='width: 15%'> </td>";

				$totADebet += $nspenyesuaianD;
				$totAKredit += $nspenyesuaianK; 
			}
		}
		$TATetap = $totADebet+$totAKredit;
		echo "<tfooter><tr>";
		echo "<td align='right' style='width: 50%' colspan='3' ><b>Total Aktiva Tetap</td>";
		echo "<td align='center' style='width: 50%' colspan='2' ><b>".number_format( $totADebet+$totAKredit, 2)."</td>";
		echo "</tr><tr>";
		echo "<td align='right' style='width: 50%'colspan='3'  ><b>TOTAL AKTIVA</td>";
		echo "<td align='center' style='width: 50%' colspan='2' ><b>".number_format( $TALancar+$TATetap, 2)."</td>";
		echo "</tr></tfooter>";
		?>

	</table>
	<table border="1">
		
		<?php 
		// koneksi database
		
		$filter = "";
		$bulan = $_GET['bulan'];
		$tahun = $_GET['tahun'];
		$totADebet=$totAKredit=0;
		$nsdebet=0;
		$nskredit=0;
		$nspenyesuaianD=0;
		$nspenyesuaianK=0;
		if (($bulan)!=''){
			$filter = $filter . " AND month(t.tanggal_transaksi)= '" . $bulan . "' AND year(t.tanggal_transaksi)= '" . $tahun ."'";
		}else{
			$filter = "";
		}
    //database
		$q = "SELECT m.kode_rekening, m.nama_rekening, m.awal_debet, m.awal_kredit,IFNULL((b.debet),0) as debet,IFNULL((b.kredit),0) as kredit,IFNULL((c.debet),0) as pdebet,IFNULL((c.kredit),0) as pkredit,b.ref,m.normal  FROM `aki_tabel_master` m";
		$q.= " left join (SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet,  sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1";
		$q.=$filter." and ket_hitungrlneraca!='-' and keterangan_posting='Post' and t.ref='-' GROUP by m.kode_rekening) as b ";
		$q.="on m.kode_rekening=b.kode_rekening left join";
		$q.="(SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet, sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1 ";
		$q.=$filter." and ket_hitungrlneraca!='-' and keterangan_posting='Post' and t.ref!='-' GROUP by m.kode_rekening) as c on m.kode_rekening=c.kode_rekening";
		$q.=" where m.kode_rekening BETWEEN '2110.000' and '2310.000' GROUP by m.kode_rekening ORDER BY m.kode_rekening asc" ;
		$data = mysqli_query($koneksi,$q);
		// menampilkan data pegawai
		while($query_data = mysqli_fetch_array($data)){
			if ($query_data["awal_debet"] != 0 || $query_data["awal_kredit"]!= 0 || $query_data["debet"] != 0 || $query_data["kredit"]!= 0 || $query_data["pdebet"] != 0 || $query_data["pkredit"]!= 0) {
				echo "<tr>";
				echo "<td align='center'style='width: 10%'>" . $query_data["kode_rekening"] . "</td>";
				echo "<td align='left'style='width: 20%'>" . $query_data["nama_rekening"] . "</td>";
				echo "<td align='center'style='width: 10%'>" . $query_data["normal"] ."</td>";
				if ($query_data["normal"] == 'Debit') {
					$nsdebet = $query_data["awal_debet"]+$query_data["debet"]-$query_data["awal_kredit"]-$query_data["kredit"];
					$nspenyesuaianD = $nsdebet+$query_data["pdebet"]-$nskredit-$query_data["pkredit"];
				}else{
					$nskredit = $query_data["awal_kredit"]+$query_data["kredit"]-$query_data["awal_debet"]-$query_data["debet"];
					$nspenyesuaianD = $nskredit+$query_data["pkredit"]-$nsdebet-$query_data["pdebet"];
				}

				echo "<td align='right'style='width: 15%'>" . number_format( $nspenyesuaianD, 2). "</td>";
				echo "<td align='right' style='width: 15%'> </td>";

				$totADebet += $nspenyesuaianD;
				$totAKredit += $nspenyesuaianK; 
			}
		}
		$TKewajiban = $totADebet+$totAKredit;
		echo "<tfooter><tr>";
		echo "<td align='right' style='width: 50%' colspan='3' ><b>Total Kewajiban</td>";
		echo "<td align='center' style='width: 50%' colspan='2' ><b>".number_format( $totADebet+$totAKredit, 2)."</td>";
		echo "</tr></tfooter>";
		?>

	</table>
	<table border="1">
		
		<?php 
		// koneksi database
		
		$filter = "";
		$bulan = $_GET['bulan'];
		$tahun = $_GET['tahun'];
		$totADebet=$totAKredit=0;
		$nsdebet=0;
		$nskredit=0;
		$nspenyesuaianD=0;
		$nspenyesuaianK=0;
		if (($bulan)!=''){
			$filter = $filter . " AND month(t.tanggal_transaksi)= '" . $bulan . "' AND year(t.tanggal_transaksi)= '" . $tahun ."'";
		}else{
			$filter = "";
		}
    //database
		$q = "SELECT m.kode_rekening, m.nama_rekening, m.awal_debet, m.awal_kredit,IFNULL((b.debet),0) as debet,IFNULL((b.kredit),0) as kredit,IFNULL((c.debet),0) as pdebet,IFNULL((c.kredit),0) as pkredit,b.ref,m.normal  FROM `aki_tabel_master` m";
		$q.= " left join (SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet,  sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1";
		$q.=$filter." and ket_hitungrlneraca!='-' and keterangan_posting='Post' and t.ref='-' GROUP by m.kode_rekening) as b ";
		$q.="on m.kode_rekening=b.kode_rekening left join";
		$q.="(SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet, sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1 ";
		$q.=$filter." and ket_hitungrlneraca!='-' and keterangan_posting='Post' and t.ref!='-' GROUP by m.kode_rekening) as c on m.kode_rekening=c.kode_rekening";
		$q.=" where m.kode_rekening BETWEEN '3000.000' and '3390.000' GROUP by m.kode_rekening ORDER BY m.kode_rekening asc" ;
		$data = mysqli_query($koneksi,$q);
		while($query_data = mysqli_fetch_array($data)){
			if ($query_data["awal_debet"] != 0 || $query_data["awal_kredit"]!= 0 || $query_data["debet"] != 0 || $query_data["kredit"]!= 0 || $query_data["pdebet"] != 0 || $query_data["pkredit"]!= 0) {
				echo "<tr>";
				echo "<td align='center'style='width: 10%'>" . $query_data["kode_rekening"] . "</td>";
				echo "<td align='left'style='width: 20%'>" . $query_data["nama_rekening"] . "</td>";
				echo "<td align='center'style='width: 10%'>" . $query_data["normal"] ."</td>";
				if ($query_data["normal"] == 'Debit') {
					$nsdebet = $query_data["awal_debet"]+$query_data["debet"]-$query_data["awal_kredit"]-$query_data["kredit"];
					$nspenyesuaianD = $nsdebet+$query_data["pdebet"]-$nskredit-$query_data["pkredit"];
				}else{
					$nskredit = $query_data["awal_kredit"]+$query_data["kredit"]-$query_data["awal_debet"]-$query_data["debet"];
					$nspenyesuaianD = $nskredit+$query_data["pkredit"]-$nsdebet-$query_data["pdebet"];
				}

				echo "<td align='right'style='width: 15%'>" . number_format( $nspenyesuaianD, 2). "</td>";
				echo "<td align='right' style='width: 15%'> </td>";

				$totADebet += $nspenyesuaianD;
				$totAKredit += $nspenyesuaianK; 
			}
		}
		$TEkuitas = $totADebet+$totAKredit;
		echo "<tfooter><tr>";
		echo "<td align='right' style='width: 50%' colspan='3' ><b>Total Ekuitas</td>";
		echo "<td align='center' style='width: 50%' colspan='2' ><b>".number_format( $totADebet+$totAKredit, 2)."</td>";
		echo "</tr></tfooter>";
		echo "</tr><tr>";
        echo "<td align='right' style='width: 50%' colspan='3' ><b>TOTAL KEWAJIBAN DAN EKUITAS</td>";
        echo "<td align='center' style='width: 50%' colspan='2' ><b>".number_format( $TKewajiban+$TEkuitas, 2)."</td>";
        echo "</tr></tfooter>";
		?>
	</table>
</body>
</html>




