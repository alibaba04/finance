<?php
/*==================================================
//=======  : Alibaba
====================================================*/
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 

class c_perkiraan
{
	var $strResults="";
	
	function validate(&$params) 
	{
		$temp=TRUE;
                
        if($params["txtKodePerkiraan"]=='' )
		{
			$this->strResults.="Kode Perkiraan harus diisi!<br/>";
			$temp=FALSE;
		}
		if($params["txtNamaPerkiraan"]=='' )
		{
			$this->strResults.="Nama Perkiraan harus diisi!<br/>";
			$temp=FALSE;
		}
        if($params["cboNormal"]=='0' )
		{
			$this->strResults.="Normal Balance harus dipilih!<br/>";
			$temp=FALSE;
		}
		if($params["cboPosisi"]=='0' )
		{
			$this->strResults.="Posisi harus dipilih!<br/>";
			$temp=FALSE;
		}
        if($params["txtAwalDebet"]=='' )
		{
			$this->strResults.="Saldo Awal Debet harus diisi!<br/>";
			$temp=FALSE;
		}
        if($params["txtAwalKredit"]=='' )
		{
			$this->strResults.="Saldo Awal Kredit harus diisi!<br/>";
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

		//cari kode rekening di tabel master
		$rsTemp=mysql_query("SELECT kode_rekening FROM aki_tabel_master WHERE md5(kode_rekening) = '".$kode."'", $dbLink);
        $rows = mysql_num_rows($rsTemp);
        if($rows==0)
		{
			$temp=TRUE;
		} 
		else
        {
        	$this->strResults.="Data Siswa masih terpakai dalam Salah satu tabel SMS Gateway ini!<br />";
            $temp=FALSE;
        }
		
		return $temp;
	}
	
	function add(&$params) 
	{
		global $dbLink;
		
		//Jika input tidak valid, langsung kembalikan pesan Error update ke user ($this->strResults)
		if(!$this->validate($params))
		{	//Pesan Error update harus diawali kata "Gagal"
			$this->strResults="Gagal Tambah Data Perkiraan - ".$this->strResults;
			return $this->strResults;
		}

		$kodeRekening = secureParam($params["txtKodePerkiraan"],$dbLink);
        $namaRekening = secureParam($params["txtNamaPerkiraan"],$dbLink);
        $normal = secureParam($params["cboNormal"],$dbLink);
        $posisi = secureParam($params["cboPosisi"],$dbLink);
        $awalDebet = secureParam($params["txtAwalDebet"],$dbLink);
        $awalDebet = str_replace(".", "", $awalDebet);
        $awalKredit = secureParam($params["txtAwalKredit"],$dbLink);
        $awalKredit = str_replace(".", "", $awalKredit);
        
        $pembuat = $_SESSION["my"]->id;

		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
			$tanggalAwal = date('Y-m-d',time());
			$result = mysql_query("SELECT kode_rekening FROM aki_tabel_master WHERE kode_rekening='".$kodeRekening."' ");
			if(mysql_num_rows($result))
				throw new Exception('Data Kode Perkiraan yang akan ditambahkan sudah pernah terdaftar dalam database.');
			
			$q = "INSERT INTO aki_tabel_master(kode_rekening, nama_rekening, tanggal_awal,  awal_debet, awal_kredit, normal, posisi) ";
			$q.= "VALUES('".$kodeRekening."',  '".$namaRekening."',  '".$tanggalAwal."',  '".$awalDebet."', '".$awalKredit."', '".$normal."',  '".$posisi."');";
			
			if (!mysql_query( $q, $dbLink))
				throw new Exception('Gagal masukkan data dalam database.');
				
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Tambah Data Perkiraan ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Tambah Data Perkiraan - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
			  return $this->strResults;
		}
		return $this->strResults;
	}
	
	function edit(&$params) 
	{
		global $dbLink;
		
		//Jika input tidak valid, langsung kembalikan pesan Error update ke user ($this->strResults)
		if(!$this->validate($params))
		{	//Pesan Error update harus diawali kata "Gagal"
			$this->strResults="Gagal Ubah Data Perkiraan - ".$this->strResults;
			return $this->strResults;
		}
		
		$kodeRekening = secureParam($params["txtKodePerkiraan"],$dbLink);
        $namaRekening = secureParam($params["txtNamaPerkiraan"],$dbLink);
        $normal = secureParam($params["cboNormal"],$dbLink);
        $posisi = secureParam($params["cboPosisi"],$dbLink);
        $awalDebet = secureParam($params["txtAwalDebet"],$dbLink);
        $awalDebet = str_replace(".", "", $awalDebet);
        $awalKredit = secureParam($params["txtAwalKredit"],$dbLink);
        $awalKredit = str_replace(".", "", $awalKredit);
        
        $pembuat = $_SESSION["my"]->id;
		
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
            $q = "UPDATE aki_tabel_master SET nama_rekening='".$namaRekening."', awal_debet='".$awalDebet."', awal_kredit='".$awalKredit."', normal='".$normal."', posisi='".$posisi."' ";
                        
			$q.= "WHERE kode_rekening='".$kodeRekening."' ";
			
			//report
			$rsTemp=mysql_query("SELECT * FROM `aki_tabel_master` WHERE kode_rekening='".$kodeRekening."' ", $dbLink);
			$temp = mysql_fetch_array($rsTemp);
			$tempName  = $temp['nama_rekening'];
			$tempD  = $temp['awal_debet'];
			$tempK  = $temp['awal_kredit'];
			$tempNormal  = $temp['normal'];
			$tempPos  = $temp['posisi'];
			$desc = secureParam($params["txtUpdate"], $dbLink);

			date_default_timezone_set("Asia/Jakarta");
			$tgl = date("Y-m-d h:i:sa");
			$ket = "desc : ".$desc." `nomer`=".$kodeRekening."  -has change, ket : ".$tempName.", ".$tempD.", ".$tempK.", ".$tempNormal.", ".$tempPos.", datetime: ".$tgl;
			$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
			$q4.= "('".$pembuat."','".$tgl."','".$ket."');";
			if (!mysql_query( $q4, $dbLink))
				throw new Exception('Error update database.');
			if (!mysql_query( $q, $dbLink))
				throw new Exception('Error update database.');
			
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Ubah Data Perkiraan ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Ubah Data Perkiraan - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
			  return $this->strResults;
		}
		return $this->strResults;
	}
	
	function delete($kodePerkiraan)
	{
		global $dbLink;

		//Jika input tidak valid, langsung kembalikan pesan Error update ke user ($this->strResults)
		if(!$this->validateDelete($kodeSiswa))
		{	//Pesan Error update harus diawali kata "Gagal"
			$this->strResults="Gagal Hapus Data Perkiraan - ".$this->strResults;
			return $this->strResults;
		}
		
                $kodeRekening = secureParam($kodePerkiraan,$dbLink);
                $pembatal = $_SESSION["my"]->id;
                		
		$q = "DELETE FROM aki_tabel_master ";
		$q.= "WHERE md5(kode_rekening)='".$kodeRekening."';";
                
		if (mysql_query( $q, $dbLink))
		{	
			$this->strResults="Sukses Hapus Data Perkiraan";
		}
		else
		{	//Pesan Error update harus diawali kata "Gagal"
			$this->strResults="Gagal Hapus Data Perkiraan - ".mysql_error();
		}
		return $this->strResults;
	}
}
?>
