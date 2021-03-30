<?php
/*==================================================
//=======  : Alibaba
====================================================*/
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 
error_reporting( error_reporting() & ~E_NOTICE );
class c_hitungrlneraca
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
	
	function hitungRL($ptgl1, $ptgl2) 
	{
		global $dbLink;
		require_once './function/fungsi_formatdate.php';
		$this->tgl1 = $ptgl1;
		$this->tgl2 = $ptgl2;

		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}

			//Ambil data tabel master
			$filter =  " AND b.tanggal_transaksi BETWEEN '" . tgl_mysql($this->tgl1) . "' AND '" . tgl_mysql($this->tgl2) . "'";
		
			$q = 'SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, (t.debet) as debet, (t.kredit)as kredit,m.normal,t.ref,t.ket_hitungrlneraca FROM `aki_tabel_transaksi`t left join aki_tabel_master m on t.kode_rekening=m.kode_rekening where aktif=1';
			if (!$query_hitung_sisa=mysql_query($q, $dbLink))
				throw new Exception('Gagal Hitung Rugi Laba & Neraca, data master tidak dapat diproses.');
			$hasilrs = mysql_num_rows($query_hitung_sisa);
			if ($hasilrs>0){
				$nsdebet = $nskredit = $nspenyesuaianD = $nspenyesuaianK = 0;
				while($row_hit=mysql_fetch_array($query_hitung_sisa)){
					$kode_rekening=$row_hit['kode_rekening'];
					$qPeriode="UPDATE `aki_tabel_transaksi` SET `ket_hitungrlneraca`='y' where aktif=1 and kode_rekening='$kode_rekening'";
					if(!mysql_query($qPeriode, $dbLink))
						throw new Exception($qPeriode.'Gagal Hitung Rugi Laba, tidak bisa update data rugi laba.');
				}
			}else{
				throw new Exception('Transaksi tanggal "'.tgl_mysql($this->tgl1) . "' AND '" . tgl_mysql($this->tgl2) .'" Tidak ada/Sudah terposting.');

			}

			date_default_timezone_set("Asia/Jakarta");
			$tgl = date("Y-m-d h:i:sa");
			$ket = "desc : Hitung Rugi Laba dan Neraca tanggal_transaksi BETWEEN " . tgl_mysql($this->tgl1) . " AND " . tgl_mysql($this->tgl2) .", datetime: ".$tgl;
			$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
			$q4.= "('".$pembuat."','".$tgl."','".$ket."');";
			if (!mysql_query( $q4, $dbLink))
				throw new Exception('Gagal insert report!');
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
