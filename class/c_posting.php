<?php
/*==================================================
//=======  : Alibaba
====================================================*/
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 

class c_posting
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
	
	function posting($ptgl1, $ptgl2) 
	{
		$this->tgl1 = $ptgl1;
		$this->tgl2 = $ptgl2;
		global $dbLink;
		require_once './function/fungsi_formatdate.php';

        $tanggal = date('Y-m-d',time());

        //insert ke tabel jurnal kas keluar dulu
        //field tanggal selesai akan diupdate dengan tanggal posting
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}

			///////////////////////// HITUNG MUTASI /////////////////////
			$filter =  " AND tanggal_transaksi BETWEEN '" . tgl_mysql($this->tgl1) . "' AND '" . tgl_mysql($this->tgl2) . "'";
			$q_transaksi = "select id_transaksi, kode_rekening from aki_tabel_transaksi where keterangan_posting=''".$filter." order by kode_rekening";
			if (!$query_hitung_mutasi=mysql_query($q_transaksi, $dbLink))
				throw new Exception('Gagal Posting Jurnal, tidak bisa ambil data kode rekening transaksi. ');

			////////////////////////// UBAH STATUS POSTING //////////////////////////////
			$selesai_tr="update aki_tabel_transaksi set tanggal_posting='$tanggal', keterangan_posting='Post' where keterangan_posting=''".$filter;
			if (!$update_tr=mysql_query($selesai_tr, $dbLink))
				throw new Exception('Gagal Posting Jurnal, tidak bisa update data tanggal selesai di transaksi.');
			
			$selesai_ju="update aki_jurnal_umum set tanggal_selesai='$tanggal' where tanggal_selesai='0000-00-00'";
			if (!$update_ju=mysql_query($selesai_ju, $dbLink))
				throw new Exception('Gagal Posting Jurnal, tidak bisa update data tanggal selesai di jurnal umum.');
			
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Posting Jurnal Dari Tanggal $this->tgl1 Sampai $this->tgl2.";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Posting Jurnal - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
			  return $this->strResults;
		}
		return $this->strResults;
	}

}
?>
