<?php
/*==================================================
//=======  : Alibaba
====================================================*/
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 
error_reporting( error_reporting() & ~E_NOTICE );
class c_tutupbuku
{
	var $strResults="";
	var $tgl1= "";
	var $tgl2= "";
	
	function validate(&$params) 
	{
		$temp=TRUE;

		if($params["txtTglTransaksi"]=='' )
		{
			$this->strResults.="Tanggal Transaksi harus diisi!<br/>";
			$temp=FALSE;
		}       
		return $temp;
	}

	function validateDelete($kode) 
	{
		global $dbLink;
		$temp=FALSE;
		if(empty($kode))
		{
			$this->strResults.="Kode tidak ditemukan!<br/>";
			$temp=FALSE;
		}

		//cari ID inisiasi di tabel penyusunan
		$rsTemp=mysql_query("SELECT id_transaksi, kode_rekening FROM aki_tabel_transaksi WHERE md5(id_transaksi) = '".$kode."'", $dbLink);
                $rows = mysql_num_rows($rsTemp);
                if($rows==0)
		{
			$temp=TRUE;
		} 
		else
        {
            $this->strResults.="Data Transaksi Jurnal masih terpakai dalam Salah satu tabel!<br />";
            $temp=FALSE;
        }
		
		return $temp;
	}
	
	function tutupbuku($ptgl1, $ptgl2) 
	{
		global $dbLink;
		require_once './function/fungsi_formatdate.php';
		$this->tgl1 = $ptgl1;
		$this->tgl2 = $ptgl2;

		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			$hrini = date('d-m-Y',time());
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}

			//Ambil data tabel master
			
			$filter =  " AND t.tanggal_transaksi BETWEEN '" . tgl_mysql($this->tgl1) . "' AND '" . tgl_mysql($this->tgl2) . "'";
			$q = "SELECT m.kode_rekening, m.nama_rekening, m.awal_debet, m.awal_kredit,IFNULL((b.debet),0) as debet,IFNULL((b.kredit),0) as kredit,IFNULL((c.debet),0) as pdebet,IFNULL((c.kredit),0) as pkredit,b.ref,m.normal,m.posisi  FROM `aki_tabel_master` m";
			$q.= " left join (SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet,  sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1 ";
			$q.=$filter." and keterangan_posting='Post' and t.ref='-' GROUP by m.kode_rekening) as b ";
			$q.="on m.kode_rekening=b.kode_rekening left join";
			$q.="(SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet, sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1 ";
			$q.=$filter." and keterangan_posting='Post' and t.ref!='-' GROUP by m.kode_rekening) as c on m.kode_rekening=c.kode_rekening where 1=1 ";
			$q.=" GROUP by m.kode_rekening ORDER BY m.kode_rekening asc" ;
			if (!$query_hitung_sisa=mysql_query($q, $dbLink))
				throw new Exception('Gagal Hitung Rugi Laba & Neraca, data master tidak dapat diproses.');
			echo $q;
			$hasilrs = mysql_num_rows($query_hitung_sisa);
			if ($hasilrs>0){
				$totADebet=$totAKredit=0;
				$nsdebet=0;
				$nskredit=0;
				$nspenyesuaianD=0;
				$nspenyesuaianK=0;
				while($row_hit=mysql_fetch_array($query_hitung_sisa)){

					if ($row_hit["normal"] == 'Debit') {
						$nsdebet = $row_hit["awal_debet"]+$row_hit["debet"]-$row_hit["awal_kredit"]-$row_hit["kredit"];
						$nspenyesuaianD = $nsdebet+$row_hit["pdebet"]-$nskredit-$row_hit["pkredit"];
					}else{
						$nskredit = $row_hit["awal_kredit"]+$row_hit["kredit"]-$row_hit["awal_debet"]-$row_hit["debet"];
						$nspenyesuaianK = $nskredit+$row_hit["pkredit"]-$nsdebet-$row_hit["pdebet"];
					}
					$totADebet += $nspenyesuaianD;
					$totAKredit += $nspenyesuaianK; 
					$nspenyesuaianD = trim($nspenyesuaianD,"-");
					$nspenyesuaianK = trim($nspenyesuaianK,"-");
					$kode_rekening=$row_hit['kode_rekening'];
					$q2="UPDATE `aki_tabel_master` SET `awal_debet`='$nspenyesuaianD', `awal_kredit`='$nspenyesuaianK' where kode_rekening='$kode_rekening'";
					if(!mysql_query($q2, $dbLink))
						throw new Exception($qPeriode.'Gagal Hitung Rugi Laba, tidak bisa update data rugi laba.');
				}
				
			}else{
				throw new Exception('Transaksi tanggal Tidak ada/Sudah terposting.');

			}

			
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Hitung Rugi Laba Neraca ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Hitung Rugi Laba Neraca - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
			  return $this->strResults;
		}
		return $this->strResults;
	}
}
?>
