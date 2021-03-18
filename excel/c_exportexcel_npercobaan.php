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
	
	$filter = "";
	$koneksi = mysqli_connect("localhost","u5514609_can",",S1s6h8+Mrc)","u5514609_dbaki");
	if (($_GET["bulan"])!=''){
		$filter = $filter . "AND month(t.tanggal_transaksi)= '" . $_GET["bulan"] . "' AND year(t.tanggal_transaksi)= '" . $_GET["tahun"] ."'";
		header("Content-Disposition: attachment; filename=neracaPercobaan_".namaBulan_id($_GET["bulan"]).$_GET["tahun"].".xls");
	}else{
		$filter = "";
		header("Content-Disposition: attachment; filename=neracaPercobaan_.xls");
	}
	?>

	<center>
		<h1>Export Data Neraca Percobaan<br/></h1>
		<h3><?php if (($_GET["bulan"])!=''){ echo 'Periode Bulan '.namaBulan_id($_GET["bulan"]).' '.$_GET["tahun"]; }else {}?></h3>
	</center>

	<table border="1">
		<thead>
			<th style="width: 5%"rowspan="2">Kode</th>
			<th style="width: 15%"rowspan="2">Nama Akun</th>
			<th style="width: 10%"colspan="2">Saldo Awal</th>
			<th style="width: 10%"rowspan="2">Debet</th>
			<th style="width: 10%"rowspan="2">Kredit</th>
			<th style="width: 10%"colspan="2">Neraca Saldo</th>
			<th style="width: 10%"colspan="2">Penyesuaian</th>
			<th style="width: 10%"colspan="2">NS Setelah Penyesuaian</th>
			<th style="width: 10%"colspan="2">Rugi Laba</th>
			<th style="width: 10%"colspan="2">Neraca</th>
			<tr>
				<th style="width: 5%">Debet</th>
				<th style="width: 5%">Kredit</th>
				<th style="width: 5%">Debet</th>
				<th style="width: 5%">Kredit</th>
				<th style="width: 5%">Debet</th>
				<th style="width: 5%">Kredit</th>
				<th style="width: 5%">Debet</th>
				<th style="width: 5%">Kredit</th>
				<th style="width: 5%">Debet</th>
				<th style="width: 5%">Kredit</th>
				<th style="width: 5%">Debet</th>
				<th style="width: 5%">Kredit</th>
			</tr>
		</thead>
		<tbody>
		<?php 
// koneksi database
		
                //database
		$q = "SELECT m.kode_rekening, m.nama_rekening, m.awal_debet, m.awal_kredit,IFNULL((b.debet),0) as debet,IFNULL((b.kredit),0) as kredit,IFNULL((c.debet),0) as pdebet,IFNULL((c.kredit),0) as pkredit,b.ref,m.normal,m.posisi  FROM `aki_tabel_master` m";
		$q.= " left join (SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet,  sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1 ";
		$q.=$filter." and keterangan_posting='Post' and t.ref='-' GROUP by m.kode_rekening) as b ";
		$q.="on m.kode_rekening=b.kode_rekening left join";
		$q.="(SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet, sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1 ";
		$q.=$filter." and keterangan_posting='Post' and t.ref!='-' GROUP by m.kode_rekening) as c on m.kode_rekening=c.kode_rekening where 1=1 ";
		$q.=" GROUP by m.kode_rekening ORDER BY m.kode_rekening asc" ;
		$rs = mysqli_query($koneksi,$q);
		$hasilrs = mysqli_num_rows($rs);
		$rowCounter = 1; $totADebet=$totAKredit=$totMutDebet=$totMutKredit=$totNDebet=$totNKredit=0;
		$totPDebet=$totPKredit=$totNsDebet=$totNsKredit=$totRlDebet=$totRlKredit=$totNeDebet=$totNeKredit=0;
		$nsdebet=0;
		$nskredit=0;
		$nspenyesuaianD=0;
		$nspenyesuaianK=0;
		if ($hasilrs>0){
			while ($query_data = mysqli_fetch_array($rs)) {
				echo "<tr>";
				echo "<td>'" . $query_data["kode_rekening"] . "</td>";
				echo "<td>" . $query_data["nama_rekening"] . "</td>";
				echo "<td align='right'>" . number_format($query_data["awal_debet"], 2) ."</td>";
				echo "<td align='right'>" . number_format($query_data["awal_kredit"], 2) . "</td>";
				echo "<td align='right'>" . number_format($query_data["debet"], 2) . "</td>";
				echo "<td align='right'>" . number_format($query_data["kredit"], 2) . "</td>";

				if ($query_data["normal"] == 'Debit') {
					$nsdebet = $query_data["awal_debet"]+$query_data["debet"]-$query_data["awal_kredit"]-$query_data["kredit"];
					$nspenyesuaianD = $nsdebet+$query_data["pdebet"]-$nskredit-$query_data["pkredit"];
					echo "<td align='right'>" . number_format($nsdebet, 2) . "</td>";
				}else{
					echo "<td align='right'>" . number_format(0, 2) . "</td>";
				}
				if($query_data["normal"] == 'Kredit'){
					$nskredit = $query_data["awal_kredit"]+$query_data["kredit"]-$query_data["awal_debet"]-$query_data["debet"];
					$nspenyesuaianK = $nskredit+$query_data["pkredit"]-$nsdebet-$query_data["pdebet"];
					echo "<td align='right'>" . number_format($nskredit, 2) . "</td>";
				}else{
					echo "<td align='right'>" . number_format(0, 2) . "</td>";
				}

				echo "<td align='right'>" . number_format($query_data["pdebet"], 2) . "</td>";
				echo "<td align='right'>" . number_format($query_data["pkredit"], 2) . "</td>";

				echo "<td align='right'>" . number_format($nspenyesuaianD, 2) . "</td>";
				echo "<td align='right'>" . number_format($nspenyesuaianK, 2) . "</td>";
				if ($query_data["posisi"] == 'LR') {
					$totRlDebet += $nspenyesuaianD;
					echo "<td align='right'>" . number_format($nspenyesuaianD, 2) . "</td>";
				}else{
					echo "<td align='right'>" . number_format(0, 2) . "</td>";
				}
				if($query_data["posisi"] == 'LR'){
					$totRlKredit += $nspenyesuaianK;
					echo "<td align='right'>" . number_format($nspenyesuaianK, 2) . "</td>";
				}else{
					echo "<td align='right'>" . number_format(0, 2) . "</td>";
				}
				if ($query_data["posisi"] == 'NRC') {
					$totNeDebet += $nspenyesuaianD;
					echo "<td align='right'>" . number_format($nspenyesuaianD, 2) . "</td>";
				}else{
					echo "<td align='right'>" . number_format(0, 2) . "</td>";
				}
				if($query_data["posisi"] == 'NRC'){
					$totNeKredit += $nspenyesuaianK;
					echo "<td align='right'>" . number_format($nspenyesuaianK, 2) . "</td>";
				}else{
					echo "<td align='right'>" . number_format(0, 2) . "</td>";
				}
				echo("</tr>");
				$totADebet += $query_data["awal_debet"];
				$totAKredit += $query_data["awal_kredit"]; 
				$totMutDebet += $query_data["debet"];
				$totMutKredit += $query_data["kredit"];
				$totNDebet += $nsdebet;
				$totNKredit += $nskredit; 
				$totPDebet += $query_data["pdebet"];
				$totPKredit += $query_data["pkredit"];
				$totNsDebet += $nspenyesuaianD;
				$totNsKredit += $nspenyesuaianK; 
			}
			echo "</tr>";
			echo "<tr>";
			echo "<td colspan='2'></td>";
			echo "<td align='right'>" . number_format($totADebet, 2) ."</td>";
			echo "<td align='right'>" . number_format($totAKredit, 2) . "</td>";
			echo "<td align='right'>" . number_format($totMutDebet, 2) ."</td>";
			echo "<td align='right'>" . number_format($totMutKredit, 2) . "</td>";
			echo "<td align='right'>" . number_format($totNDebet, 2) ."</td>";
			echo "<td align='right'>" . number_format($totNKredit, 2) . "</td>";
			echo "<td align='right'>" . number_format($totPDebet, 2) . "</td>";
			echo "<td align='right'>" . number_format($totPKredit, 2) . "</td>";
			echo "<td align='right'>" . number_format($totNsDebet, 2) . "</td>";
			echo "<td align='right'>" . number_format($totNsKredit, 2) . "</td>";
			echo "<td align='right'>" . number_format($totRlDebet, 2) . "</td>";
			echo "<td align='right'>" . number_format($totRlKredit, 2) . "</td>";
			echo "<td align='right'>" . number_format($totNeDebet, 2) . "</td>";
			echo "<td align='right'>" . number_format($totNeKredit, 2) . "</td></tr>";
			echo "<tr>";
			echo "<td colspan='11'></td>";

			$totDneraca = $totKneraca = 0;

			if ($totRlDebet>$totRlKredit) {
				$totDneraca = $totRlDebet-$totRlKredit;
				echo "<td align='right'><font color='red'><b>Rugi</td>";
				echo "<td align='right'>" . number_format($totDneraca, 2) . "</td>";
			}else{
				echo "<td align='right'>" . number_format(0, 2) . "</td>";
			}
			if ($totRlDebet<$totRlKredit) {
				$totKneraca = $totRlKredit-$totRlDebet;
				echo "<td align='right'><font color='blue'><b>Laba</td>";
				echo "<td align='right'>" . number_format($totKneraca, 2) . "</td>";
			}else{
				echo "<td align='right'>" . number_format(0, 2) . "</td>";
			}
			echo "<td align='right'>" . number_format($totDneraca, 2) . "</td>";
			echo "<td align='right'>" . number_format($totKneraca, 2) . "</td></tr>";
			echo "<tr>";
			echo "<td colspan='14'></td>";
			echo "<td align='right'>" . number_format($totNeDebet+$totDneraca, 2) . "</td>";
			echo "<td align='right'>" . number_format($totNeKredit+$totKneraca, 2) . "</td></tr>";

		} else {
			echo("<tr class='even'>");
			echo ("<td colspan='8' align='center'>No data Found!</td>");
			echo("</tr>");
		}
		?>
	</tbody>
</table>
</body>
</html>




