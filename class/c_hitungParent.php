<?php
/* ==================================================
  //=======  : Alibaba
  ==================================================== */
// Hitung Parent ///
class c_hitungParent{
function totalParent()
{
	error_reporting( error_reporting() & ~E_NOTICE );
	global $dbLink;
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
		$q_parent = "select * from aki_tabel_master";
			if (!$query_hitung_parent=mysql_query($q_parent, $dbLink))
				throw new Exception('Gagal Posting Jurnal, tidak bisa ambil data.');
			while($row_hit_parent=mysql_fetch_array($query_hitung_parent)){
				$kode_rekening=$row_hit_parent['kode_rekening'];
				$awal_debet=$row_hit_parent['awal_debet'];
				$awal_kredit=$row_hit_parent['awal_kredit'];
				// KAS //
				if ($kode_rekening > '1.1.10.100' AND $kode_rekening < '1.1.10.200'){
					
					$kasDebet += $awal_debet;
					$kasKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$kasDebet', awal_kredit='$kasKredit' where kode_rekening='1.1.10.100'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				//GIRO DAN TABUNGAN	//
				if ($kode_rekening > '1.1.10.200' AND $kode_rekening < '1.1.10.300'){
					
					$gntsDebet += $awal_debet;
					$gntsKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$gntsDebet', awal_kredit='$gntsKredit' where kode_rekening='1.1.10.200'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				//DEPOSITO //
				if ($kode_rekening > '1.1.10.300' AND $kode_rekening < '1.1.10.400'){
					
					$depDebet += $awal_debet;
					$depKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$depDebet', awal_kredit='$depKredit' where kode_rekening='1.1.10.300'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}

				// KAS DAN SETORAN KAS aset lancar//
				$knsKasDebet = $kasDebet+$gntsDebet+$depDebet;
				$knsKasKredit = $kasKredit+$gntsKredit+$depKredit;
				$qupdate_parent="update aki_tabel_master set awal_debet='$knsKasDebet', awal_kredit='$knsKasKredit' where kode_rekening='1.1.10.000' OR kode_rekening='1.1.00.000'";
				//$qupdateNeraca = "update aki_tabel_neraca set awal_debet='$knsKasDebet', awal_kredit='0' where kode_rekening='A2' OR kode_rekening='A1'";
						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
						//if (!$update_parent=mysql_query($qupdateNeraca, $dbLink))
							//throw new Exception('Gagal Posting Jurnal, tidak bisa update data total ke neraca.');
				//INVESTASI JANGKA PENDEK//
				if ($kode_rekening > '1.2.00.000' AND $kode_rekening < '1.3.00.000'){
					$ijkDebet += $awal_debet;
					$ijkKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$ijkDebet', awal_kredit='$ijkKredit' where kode_rekening='1.2.00.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				//PIUTANG PINJAMAN ANGGOTA	//
				if ($kode_rekening > '1.3.10.100' AND $kode_rekening< '1.3.10.200'){
					$ppaDebet += $awal_debet;
					$ppaKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$ppaDebet', awal_kredit='$ppaKredit' where kode_rekening='1.3.10.100'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				//PIUTANG UNIT USAHA	//
				if ($kode_rekening > '1.3.10.200' AND $kode_rekening< '1.3.10.300'){
					$puuDebet += $awal_debet;
					$puuKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$puuDebet', awal_kredit='$puuKredit' where kode_rekening='1.3.10.200'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				//PIUTANG USAHA //
					$puDebet = $ppaDebet+$puuDebet;
					$puKredit = $ppaKredit+$puuKredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$puDebet', awal_kredit='$puKredit' where kode_rekening='1.3.10.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				
				//PIUTANG LAIN-LAIN	//
				if ($kode_rekening > '1.3.20.000' AND $kode_rekening< '1.3.30.000'){
					$pllDebet += $awal_debet;
					$pllKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$pllDebet', awal_kredit='$pllKredit' where kode_rekening='1.3.20.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				//PIUTANG	//
					$piuDebet = $puDebet+$pllDebet;
					$piuKredit = $puKredit+$pllKredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$piuDebet', awal_kredit='$piuKredit' where kode_rekening='1.3.00.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				//PERSEDIAAN DALAM PROSES PEMBANGUNAN//
				if ($kode_rekening > '1.4.10.000' AND $kode_rekening< '1.4.20.000'){
					$pdppDebet += $awal_debet;
					$pdppKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$pdppDebet', awal_kredit='$pdppKredit' where kode_rekening='1.4.10.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				//PERSEDIAAN SIAP UNTUK DIJUAL	//
				if ($kode_rekening > '1.4.20.000' AND $kode_rekening< '1.4.30.000'){
					$psudDebet += $awal_debet;
					$psudKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$psudDebet', awal_kredit='$psudKredit' where kode_rekening='1.4.20.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				//PERSEDIAAN BARANG DAGANGAN//
				if ($kode_rekening > '1.4.30.000' AND $kode_rekening< '1.4.40.000'){
					$pbdDebet += $awal_debet;
					$pbdKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$pbdDebet', awal_kredit='$pbdKredit' where kode_rekening='1.4.30.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				//PERSEDIAAN BARANG	//
					$pbDebet = $pdppDebet+$psudDebet+$pbdDebet;
					$pbKredit = $pdppKredit+$psudKredit+$pbdKredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$pbDebet', awal_kredit='$pbKredit' where kode_rekening='1.4.00.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				
				//TANAH	//
				if ($kode_rekening > '1.5.10.000' AND $kode_rekening< '1.5.20.000'){
					$tDebet += $awal_debet;
					$tKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$tDebet', awal_kredit='$tKredit' where kode_rekening='1.5.10.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				//BANGUNAN	//
				if ($kode_rekening > '1.5.20.000' AND $kode_rekening< '1.5.30.000'){
					$bDebet += $awal_debet;
					$bKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$bDebet', awal_kredit='$bKredit' where kode_rekening='1.5.20.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				//KENDARAAN	//
				if ($kode_rekening > '1.5.30.000' AND $kode_rekening< '1.5.40.000'){
					$kDebet += $awal_debet;
					$kKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$kDebet', awal_kredit='$kKredit' where kode_rekening='1.5.30.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				//INVENTARIS KANTOR	//
				if ($kode_rekening > '1.5.40.000' AND $kode_rekening< '1.5.50.000'){
					$ikDebet += $awal_debet;
					$ikKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$ikDebet', awal_kredit='$ikKredit' where kode_rekening='1.5.40.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				//TAMAN DAN PEKERANGAN	//
				if ($kode_rekening > '1.5.50.000' AND $kode_rekening< '1.5.60.000'){
					$tnpDebet += $awal_debet;
					$tnpKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$tnpDebet', awal_kredit='$tnpKredit' where kode_rekening='1.5.50.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				//ASET TETAP//
					$atDebet = $tDebet+$bDebet+$kDebet+$ikDebet+$tnpDebet;
					$atKredit = $tKredit+$bKredit+$kKredit+$ikKredit+$tnpKredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$atDebet', awal_kredit='$atKredit' where kode_rekening='1.5.00.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');

				if ($kode_rekening > '1.6.10.000' AND $kode_rekening< '1.6.20.000'){
					$bdpDebet += $awal_debet;
					$bdpKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$bdpDebet', awal_kredit='$bdpKredit' where kode_rekening='1.6.10.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				if ($kode_rekening > '1.6.20.000' AND $kode_rekening< '1.6.30.000'){
					$bddDebet += $awal_debet;
					$bddKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$bddDebet', awal_kredit='$bddKredit' where kode_rekening='1.6.20.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				if ($kode_rekening > '1.4.40.000' AND $kode_rekening< '1.4.50.000'){
					$pmDebet += $awal_debet;
					$pmKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$pmDebet', awal_kredit='$pmKredit' where kode_rekening='1.4.40.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				if ($kode_rekening > '1.6.50.000' AND $kode_rekening< '1.6.60.000'){
					$pyadDebet += $awal_debet;
					$pyadKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$pyadDebet', awal_kredit='$pyadKredit' where kode_rekening='1.6.50.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				//ASET LAINNYA	//
					$alDebet = $bdpDebet+$bddDebet+$pmDebet+$pyadDebet;
					$alKredit = $bdpKredit+$bddKredit+$pmKredit+$pyadKredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$totsDebet', awal_kredit='$totsKredit' where kode_rekening='1.6.00.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				//ASET//
					$asetDebet = $knsKasDebet+$ijkDebet+$piuDebet+$pbDebet+$atDebet+$alDebet;
					$asetKredit = $knsKasKredit+$ijkKredit+$piuKredit+$pbKredit+$atKredit+$alKredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$asetDebet', awal_kredit='$asetKredit' where kode_rekening='1.0.00.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				
				if ($kode_rekening > '2.1.10.000' AND $kode_rekening < '2.1.20.000'){
					$kuDebet += $awal_debet;
					$kuKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$kuDebet', awal_kredit='$kuKredit' where kode_rekening='2.1.10.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
					$llanDebet = $kuDebet;
					$llanKredit = $kuKredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$llanDebet', awal_kredit='$llanKredit' where kode_rekening='2.1.00.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				
				if ($kode_rekening == '2.2.10.000' ){
					$kbjpeDebet = $awal_debet;
					$kbjpeKredit = $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$kbjpeDebet', awal_kredit='$kbjpeKredit' where kode_rekening='2.2.10.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}

				if ($kode_rekening > '2.2.20.000' AND $kode_rekening < '2.2.30.000'){
					$kbjpDebet += $awal_debet;
					$kbjpKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$kbjpDebet', awal_kredit='$kbjpKredit' where kode_rekening='2.2.20.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
					$lbDebet = $kbjpeDebet+$kbjpDebet;
					$lbKredit = $kbjpeKredit+$kbjpKredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$lbDebet', awal_kredit='$lbKredit' where kode_rekening='2.2.00.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				if ($kode_rekening > '2.3.00.000' AND $kode_rekening < '2.4.00.000'){
					$lpDebet += $awal_debet;
					$lpKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$lpDebet', awal_kredit='$lpKredit' where kode_rekening='2.3.00.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				if ($kode_rekening > '2.4.00.000' AND $kode_rekening < '2.5.00.000'){
					$llDebet += $awal_debet;
					$llKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$llDebet', awal_kredit='$llKredit' where kode_rekening='2.4.00.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
					$liabilitasDebet = $llanDebet+$lbDebet+$lpDebet+$llDebet;
					$liabilitasKredit = $llanKredit+$lbKredit+$lpKredit+$llKredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$liabilitasDebet', awal_kredit='$liabilitasKredit' where kode_rekening='2.0.00.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');

				if ($kode_rekening > '3.1.00.000' AND $kode_rekening < '3.2.00.000'){
					$msDebet += $awal_debet;
					$msKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='0', awal_kredit='$msKredit' where kode_rekening='3.1.00.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				if ($kode_rekening > '3.2.00.000' AND $kode_rekening < '3.3.00.000'){
					$shuDebet1 += $awal_debet;
					$shuKredit1 += $awal_kredit;
					$shuKredit = $shuKredit1-$shuDebet1;
					$qupdate_parent="update aki_tabel_master set awal_debet='0', awal_kredit='$shuKredit' where kode_rekening='3.2.00.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
					$asetnetoDebet = $msDebet+$shuDebet;
					$asetnetoKredit = $msKredit+$shuKredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='0', awal_kredit='$asetnetoKredit' where kode_rekening='3.0.00.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				if ($kode_rekening > '4.1.00.000' AND $kode_rekening < '4.2.00.000'){
					$pbpaDebet += $awal_debet;
					$pbpaKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$pbpaDebet', awal_kredit='$pbpaKredit' where kode_rekening='4.1.00.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				if ($kode_rekening > '4.2.00.000' AND $kode_rekening < '4.3.00.000'){
					$puuDebet += $awal_debet;
					$puuKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$puuDebet', awal_kredit='$puuKredit' where kode_rekening='4.2.00.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				if ($kode_rekening > '4.3.00.000' AND $kode_rekening < '4.4.00.000'){
					$plaDebet += $awal_debet;
					$plaKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$plaDebet', awal_kredit='$plaKredit' where kode_rekening='4.3.00.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				if ($kode_rekening > '4.4.00.000' AND $kode_rekening < '4.5.00.000'){
					$pDebet += $awal_debet;
					$pKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$pDebet', awal_kredit='$pKredit' where kode_rekening='4.4.00.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				
					$penerimaanDebet = $pbpaDebet+$puuDebet+$plaDebet+$pDebet;
					$penerimaanKredit = $pbpaKredit+$puuKredit+$plaKredit+$pKredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$penerimaanDebet', awal_kredit='$penerimaanKredit' where kode_rekening='4.0.00.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');

				if ($kode_rekening > '5.2.10.000' AND $kode_rekening < '5.2.20.000'){
					$bpDebet += $awal_debet;
					$bpKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$bpDebet', awal_kredit='$bpKredit' where kode_rekening='5.2.10.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				if ($kode_rekening > '5.2.20.000' AND $kode_rekening < '5.2.30.000'){
					$bbDebet += $awal_debet;
					$bbKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$bbDebet', awal_kredit='$bbKredit' where kode_rekening='5.2.20.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				if ($kode_rekening > '5.2.30.000' AND $kode_rekening < '5.2.40.000'){
					$angDebet += $awal_debet;
					$angKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$angDebet', awal_kredit='angKredit' where kode_rekening='5.2.30.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}

					$pBankDebet = $bpDebet+$bbDebet+$angDebet;
					$pBankKredit = $bpKredit+$bbKredit+$angKredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$pBankDebet', awal_kredit='$pBankKredit' where kode_rekening='5.2.00.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				
				if ($kode_rekening > '5.1.10.000' AND $kode_rekening < '5.1.20.000'){
					$hppenDebet += $awal_debet;
					$hppenKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$hppenDebet', awal_kredit='$hppenKredit' where kode_rekening='5.1.00.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}

				if ($kode_rekening > '5.1.00.000' AND $kode_rekening < '5.1.10.000'){
					$pusaDebet += $awal_debet;
					$pusaKredit += $awal_kredit;
					$tpusaD = $pusaDebet+$hppenDebet;
					$tpusaK = $pusaKredit+$hppenKredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$tpusaD', awal_kredit='$pusaKredit' where kode_rekening='5.1.00.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				
					
				if ($kode_rekening > '5.3.00.000' AND $kode_rekening < '5.4.00.000'){
					$bpatDebet += $awal_debet;
					$bpatKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$bpatDebet', awal_kredit='$bpatKredit' where kode_rekening='5.3.00.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				if ($kode_rekening > '5.4.00.000' AND $kode_rekening < '5.5.00.000'){
					$ppengDebet += $awal_debet;
					$ppengKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$ppengDebet', awal_kredit='$ppengKredit' where kode_rekening='5.4.00.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				if ($kode_rekening > '5.5.00.000' AND $kode_rekening < '5.6.00.000'){
					$ppajDebet += $awal_debet;
					$pajKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$ppajDebet', awal_kredit='$ppajKredit' where kode_rekening='5.5.00.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				if ($kode_rekening > '5.6.00.000' AND $kode_rekening < '5.7.00.000'){
					$plainDebet += $awal_debet;
					$plainKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$plainDebet', awal_kredit='$plainKredit' where kode_rekening='5.6.00.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
				if ($kode_rekening > '5.7.00.000' AND $kode_rekening < '5.8.00.000'){
					$pshuDebet += $awal_debet;
					$pshuKredit += $awal_kredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$pshuDebet', awal_kredit='$pshuKredit' where kode_rekening='5.7.00.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				}
					$pengeluaranDebet = $tpusaD+$pBankDebet+$bpatDebet+$ppengDebet+$ppajDebet+$plainDebet+$pshuDebet;
					$pengeluaranKredit = $tpusaK+$pBankKredit+$bpatKredit+$ppengKredit+$ppajKredit+$plainKredit+$pshuKredit;
					$qupdate_parent="update aki_tabel_master set awal_debet='$pengeluaranDebet', awal_kredit='$pengeluaranKredit' where kode_rekening='5.0.00.000'";

						if (!$update_parent=mysql_query($qupdate_parent, $dbLink))
							throw new Exception('Gagal Posting Jurnal, tidak bisa update data total parent.');
				
			}
	}
}

?>