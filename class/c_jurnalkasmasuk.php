<?php
/*==================================================
//=======  : Alibaba
====================================================*/
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 

class c_jurnalkasmasuk
{
	var $strResults="";
	
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
	
	function add(&$params) 
	{
		global $dbLink;
		require_once './function/fungsi_formatdate.php';

		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validate($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Tambah Data Jurnal Kas Masuk - ".$this->strResults;
			return $this->strResults;
		}
		// $kodeTransaksi = secureParam($params["txtKodeTransaksi"],$dbLink);
        $tglTransaksi = secureParam($params["txtTglTransaksi"],$dbLink);
        $tglTransaksi = tgl_mysql($tglTransaksi);
        $ketKas = secureParam($params["txtKeteranganKas"],$dbLink);
        
        $pembuat = $_SESSION["my"]->id;

        //insert ke tabel jurnal kas umum dulu
        //field tanggal selesai akan diupdate dengan tanggal posting
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}

			$q_kode = mysql_query("SELECT MAX(kode_transaksi) AS kode_transaksi FROM aki_jurnal_masuk WHERE tgl_transaksi='".$tglTransaksi."' ", $dbLink);
			$kode = mysql_fetch_array($q_kode);

			$urut = substr($kode['kode_transaksi'], 2);
			$tglKodeTerakhir = substr($kode['kode_transaksi'], 2, 8);
			$tglTr = str_replace("-", "", $tglTransaksi);

			if ($tglTr == $tglKodeTerakhir){
				$kode = (int)$urut + 1;
				$kodeTransaksi = "KM".$kode;
			}else{
				$kodeTransaksi = "KM".$tglTr."001";
			}
			
			$q = "INSERT INTO aki_jurnal_masuk(nomor_jurnal, kode_transaksi, tanggal_selesai, tgl_transaksi) ";
			$q.= "VALUES ('NULL', '".$kodeTransaksi."', '0000-00-00', '".$tglTransaksi."');";
			
			if (!mysql_query($q, $dbLink))
				throw new Exception('Gagal masukkan data dalam database.');

			//insert ke tabel transaksi sebanyak jumAddJurnal
			//pada jurnal kas masuk, transaksi diinputkan pada sisi Kredit, sebagai lawannya (supaya balance)
			//diinputkan Akun Kas pada sisi Debet sejumlah total transaksi sisi Kredit 
			$jumData = $params["jumAddJurnal"];
			for ($j = 0; $j < $jumData ; $j++){
				if (!empty($params['chkAddJurnal_'.$j])){
					if ($params["txtKodeRekening_" . $j] == "")
                        throw new Exception("Kode Rekening Harus Diisi !");
                    if ($params["txtKeterangan_" . $j] == "")
                        throw new Exception("Keterangan Harus Diisi !");
                    // if ($params["txtDebet_" . $j] == "")
                    //     throw new Exception("Nominal Debet Harus Diisi, Minimal isikan angka 0 (Nol) !");
                    // if ($params["txtKredit_" . $j] == "")
                    //     throw new Exception("Nominal Kredit Harus Diisi, Minimal isikan angka 0 (Nol) !");

                    $kodeRekenening = secureParam($params["txtKodeRekening_" . $j], $dbLink);
                    $keterangan = secureParam($params["txtKeterangan_" . $j], $dbLink);
                    $debet = secureParam($params["txtDebet_" . $j], $dbLink);
                    $debet = str_replace(".", "", $debet);
                    $kredit = secureParam($params["txtKredit_" . $j], $dbLink);
                    $kredit = str_replace(".", "", $kredit);

                    $q2 = "INSERT INTO aki_tabel_transaksi(id_transaksi, kode_transaksi, kode_rekening, tanggal_transaksi, jenis_transaksi, keterangan_transaksi, debet, kredit, tanggal_posting, keterangan_posting, last_updater) ";
					$q2.= "VALUES ('NULL',  '".$kodeTransaksi."',  '".$kodeRekenening."', '".$tglTransaksi."', 'Kas Masuk', '".$keterangan."', '0', '".$kredit."', '0000-00-00', '', '".$pembuat."');";

					if (!mysql_query( $q2, $dbLink))
						throw new Exception('Gagal tambah data transaksi Jurnal Kas Masuk.');

					$totKredit += $kredit ; //semua nominal kredit diakumulasi nanti disimpan sebagai lawan transaksi jurnal kas masuk

				}
			}

			//ambil kode rekening kas
			$q_kodeKas =  "SELECT kode_rekening FROM aki_tabel_master WHERE nama_rekening='AKTIVA' ";
			if (!$rs_kodeKas=mysql_query( $q_kodeKas, $dbLink))
						throw new Exception('Gagal tambah data transaksi Jurnal Kas Masuk.');

			$kodeKas = mysql_fetch_array($rs_kodeKas);

			$q3 = "INSERT INTO aki_tabel_transaksi(id_transaksi, kode_transaksi, kode_rekening, tanggal_transaksi, jenis_transaksi, keterangan_transaksi, debet, kredit, tanggal_posting, keterangan_posting, last_updater) ";
			$q3.= "VALUES ('NULL',  '".$kodeTransaksi."',  '".$kodeKas['kode_rekening']."', '".$tglTransaksi."', 'Kas Masuk', '".$ketKas."', '".$totKredit."', '0', '0000-00-00', '', '".$pembuat."');";

			if (!mysql_query( $q3, $dbLink))
						throw new Exception('Gagal tambah data transaksi Jurnal Kas Masuk.');
				
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Tambah Data Jurnal Kas Masuk ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Tambah Data Jurnal Kas Masuk - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
			  return $this->strResults;
		}
		return $this->strResults;
	}
	
	function edit(&$params) 
	{
		global $dbLink;
		require_once './function/fungsi_formatdate.php';
		
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validate($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Ubah Data Jurnal Kas Masuk - ".$this->strResults;
			return $this->strResults;
		}
		
		$kodeTransaksi = secureParam($params["txtKodeTransaksi"],$dbLink);
        $tglTransaksi = secureParam($params["txtTglTransaksi"],$dbLink);
        $tglTransaksi = tgl_mysql($tglTransaksi);
        $ketKas = secureParam($params["txtKeteranganKas"],$dbLink);
        $pembuat = $_SESSION["my"]->id;
		
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}

			//update ke tabel transaksi sebanyak jumEditJurnal
			$jumData = $params["jumEditJurnal"];
			for ($j = 0; $j < $jumData ; $j++){
				if (!empty($params['chkEdit_'.$j])){
					if ($params["txtKodeRekening_" . $j] == "")
                        throw new Exception("Kode Rekening Harus Diisi !");
                    if ($params["txtKeterangan_" . $j] == "")
                        throw new Exception("Keterangan Harus Diisi !");
                    //if ($params["txtDebet_" . $j] == "")
                    //    throw new Exception("Nominal Debet Harus Diisi, Minimal isikan angka 0 (Nol) !");
                    if ($params["txtKredit_" . $j] == "")
                        throw new Exception("Nominal Kredit Harus Diisi, Minimal isikan angka 0 (Nol) !");

                    $idTransaksi = secureParam($params["chkEdit_" . $j], $dbLink);
                    $kodeRekenening = secureParam($params["txtKodeRekening_" . $j], $dbLink);
                    $keterangan = secureParam($params["txtKeterangan_" . $j], $dbLink);
                    $debet = secureParam($params["txtDebet_" . $j], $dbLink);
                    $debet = str_replace(".", "", $debet);
                    $kredit = secureParam($params["txtKredit_" . $j], $dbLink);
                    $kredit = str_replace(".", "", $kredit);

                    $q = "UPDATE aki_tabel_transaksi SET kode_rekening = '".$kodeRekenening."', tanggal_transaksi = '".$tglTransaksi."', keterangan_transaksi='".$keterangan."', debet='0', kredit= '".$kredit."', last_updater ='".$pembuat."' ";
					$q.= "WHERE id_transaksi='".$idTransaksi."' ;";

					if (!mysql_query( $q, $dbLink))
						throw new Exception('Gagal ubah data transaksi jurnal kas masuk.');

					$totKredit += $kredit ; //semua nominal kredit diakumulasi nanti disimpan sebagai lawan transaksi jurnal kas masuk

				}
			}

			//ambil kode rekening kas
			$q_kodeKas =  "SELECT kode_rekening FROM aki_tabel_master WHERE nama_rekening='Kas' ";
			if (!$rs_kodeKas=mysql_query($q_kodeKas, $dbLink))
				throw new Exception('Gagal ubah data transaksi Jurnal Kas Masuk. ');

			$kodeKas = mysql_fetch_array($rs_kodeKas);

			$q_debet = "SELECT sum(kredit) AS TotDebet FROM aki_tabel_transaksi WHERE kode_transaksi='".$kodeTransaksi."' ";
			if (!$rs_qdebet=mysql_query($q_debet, $dbLink))
				throw new Exception('Gagal ubah data transaksi Jurnal Kas Masuk. 3');

			$debet = mysql_fetch_array($rs_qdebet);

			$q3 = "UPDATE aki_tabel_transaksi SET tanggal_transaksi='".$tglTransaksi."', keterangan_transaksi='".$ketKas."', debet='".$debet['TotDebet']."', last_updater='".$pembuat."' ";
			$q3.= "WHERE kode_transaksi='".$kodeTransaksi."' AND kode_rekening='".$kodeKas['kode_rekening']."' ;";

			if (!mysql_query( $q3, $dbLink))
						throw new Exception('Gagal ubah data transaksi Jurnal Kas Masuk. ');
				
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Ubah Data Jurnal Kas Masuk ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Ubah Data Jurnal Kas Masuk - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
			  return $this->strResults;
		}
		return $this->strResults;
	}
	
	function delete($kode)
	{
		global $dbLink;

		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validateDelete($kode))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Hapus Data Jurnal Kas Masuk - ".$this->strResults;
			return $this->strResults;
		}

		$kodeTransaksi = secureParam($kode,$dbLink);
        $pembatal = $_SESSION["my"]->id;

		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}

			$q = "DELETE FROM aki_tabel_transaksi ";
			$q.= "WHERE md5(kode_transaksi)='".$kodeTransaksi."';";

			if (!mysql_query( $q, $dbLink))
				throw new Exception('Gagal hapus data transaksi Jurnal Kas Masuk.');

			$q1 = "DELETE FROM aki_jurnal_masuk ";
			$q1.= "WHERE md5(kode_transaksi)='".$kodeTransaksi."';";

			if (!mysql_query( $q, $dbLink))
				throw new Exception('Gagal hapus data Jurnal Kas Masuk.');

			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Hapus Data Jurnal Kas Masuk ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Hapus Data Jurnal Kas Masuk - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
			  return $this->strResults;
		}
		return $this->strResults;
		
	}
}
?>
