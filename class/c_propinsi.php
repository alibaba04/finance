<?php
/*==================================================
//=======  : Alibaba
====================================================*/
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 

class c_propinsi
{
	var $strResults="";
	
	function validate(&$params) 
	{
		$temp=TRUE;
		
		//Jika mode Add, nama propinsi harus diisi
		if($params["txtnamaPropinsi"]=='' )
		{
			$this->strResults.="Nama Propinsi harus diisi!<br/>";
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

		//cari KodePropinsi di tabel kota
		$rsTemp=mysql_query("SELECT kodePropinsi FROM `Kota` WHERE md5(kodePropinsi) = '".$kode."'", $dbLink);
                $rows = mysql_num_rows($rsTemp);
                if($rows==0)
		{
			$temp=TRUE;
		} 
		else
                {
                        $this->strResults.="KodePropinsi masih terpakai dalam Salah satu tabel Kota!<br />";
                        $temp=FALSE;
                }
		
		return $temp;
	}
	
	function add(&$params) 
	{
		global $dbLink;
		
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validate($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Tambah Data Propinsi - ".$this->strResults;
			return $this->strResults;
		}
		$namaPropinsi = secureParam($params["txtnamaPropinsi"],$dbLink);

		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
				
			$result = mysql_query("SELECT nama FROM propinsi WHERE UPPER(nama)='".strtoupper($namaPropinsi)."'");
			if(mysql_num_rows($result))
				throw new Exception('Nama propinsi yang akan ditambahkan sudah pernah terdaftar dalam database.');
			
			$q = "INSERT INTO propinsi (nama) ";
			$q.= "VALUES ('".strtoupper($namaPropinsi)."');";
			
			if (!mysql_query( $q, $dbLink))
				throw new Exception('Gagal masukkan data dalam database.');
				
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Tambah Data Propinsi ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Tambah Data Propinsi - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
			  return $this->strResults;
		}
		return $this->strResults;
	}
	
	function edit(&$params) 
	{
		global $dbLink;
		
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validate($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Ubah Data Propinsi - ".$this->strResults;
			return $this->strResults;
		}
		
		$kodePropinsi = secureParam($params["kodePropinsi"],$dbLink);
		$namaPropinsi = secureParam($params["txtnamaPropinsi"],$dbLink);
		
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
			$result = mysql_query("SELECT nama FROM propinsi WHERE UPPER(nama)='".strtoupper($namaPropinsi)."';");
			if(mysql_num_rows($result))
				throw new Exception('Nama propinsi yang akan diubah sudah pernah terdaftar dalam database.'.$kodePropinsi);
				
			$q = "UPDATE propinsi SET nama='".strtoupper($namaPropinsi)."' ";
			$q.= "WHERE kodePropinsi='".$kodePropinsi."' ";
			
			if (!mysql_query( $q, $dbLink))
				throw new Exception('Gagal mengubah database.');
				
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Ubah Data Propinsi ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Ubah Data Propinsi - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
			  return $this->strResults;
		}
		return $this->strResults;
	}
	
	function delete($kodePropinsi)
	{
		global $dbLink;

		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validateDelete($kodePropinsi))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Hapus Data Propinsi - ".$this->strResults;
			return $this->strResults;
		}
		
                $kodePropinsi = secureParam($kodePropinsi,$dbLink);
                		
		$q = "DELETE FROM propinsi ";
		$q.= "WHERE md5(kodePropinsi)='".$kodePropinsi."';";
		
		if (mysql_query( $q, $dbLink))
		{	
			$this->strResults="Sukses Hapus Data Propinsi ";
		}
		else
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Hapus Data Propinsi - ".mysql_error();
		}
		return $this->strResults;
	}
}
?>
