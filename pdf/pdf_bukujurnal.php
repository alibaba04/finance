<?php
    //require_once('../function/fpdf/html_table.php');
    require_once('../function/fpdf/mc_table.php');
    require_once ("../function/fungsi_formatdate.php");
    require_once ("../function/fungsi_convertNumberToWord.php");
    $pdf=new PDF_MC_Table();
    $pdf->AddPage();
    $pdf->SetFont('Arial','',14);

    $tglJurnal1 = $_GET['tglJurnal1'];
    $tglJurnal2 = $_GET['tglJurnal2'];
    
    $filter = "";
    $html = "";
    if ($tglJurnal1 && $tglJurnal2)
        $filter = $filter . " AND t.tanggal_transaksi BETWEEN '" . tgl_mysql($tglJurnal1) . "' AND '" . tgl_mysql($tglJurnal2) . "' ";

    $pdf->SetFont('Arial', '', 14);
    $pdf->Cell(0, 7, "LAPORAN TRANSAKSI JURNAL", 0, 1, 'C');

    if ($filter==""){
        $pdf->Cell(0, 5, "Periode Sampai Tanggal : ".date('d-m-Y',time()), 0, 1, 'C');
    }else{
        if ($tglJurnal1 == $tglJurnal2) {
            $pdf->Cell(0, 5, "Periode Tanggal : ".$tglJurnal1, 0, 1, 'C');
        }else{
            $pdf->Cell(0, 5, "Periode Tanggal : ".$tglJurnal1." s/d ".$tglJurnal2, 0, 1, 'C');
        }
    }
    $date = date_create($tglJurnal1);
    //ISI
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'b', 12); 
    $pdf->Cell(0, 5, "*Laporan Keuangan, ".(strftime('%A', strtotime($tglJurnal1)))." ".$tglJurnal1."*", 0, 1, 'L');
    $pdf->SetFont('Arial', '', 12);  
    $qsum= "SELECT sum(debet) as nominal FROM `aki_tabel_transaksi` WHERE tanggal_transaksi<='".date_format($date,"Y-m-d")."'";
    $resultsum=mysqli_query($dbLink,$qsum);
    if ($lap = mysqli_fetch_array($resultsum)) {
        $pdf->Cell(0, 5, chr(187).chr(187).' Rp. '.number_format($lap["nominal"],0), 0, 1, 'L'); 
        $pdf->Cell(0, 5, chr(187).chr(187).' USD    '.number_format($lap["nominal"]*0.000070,0), 0, 1, 'L'); 
        $pdf->Cell(0, 5, chr(187).chr(187).' Philippines Peso '.number_format($lap["nominal"]*0.0034), 0, 1, 'L');
    }
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'b', 12);
    $pdf->Cell(0, 5, "*Pemasukan*", 0, 1, 'L');
    $pdf->SetFont('Arial', '', 12); 
    $qin= "SELECT keterangan_transaksi,(debet) as nominal FROM `aki_tabel_transaksi` WHERE keterangan_transaksi like '%payin%' and debet>1000000  and tanggal_transaksi='".date_format($date,"Y-m-d")."'";
    $resultin=mysqli_query($dbLink,$qin);
        $noin=1;$noout=1;$nopay=1;
    while ($lap = mysqli_fetch_array($resultin)) {
        $pdf->Cell(0, 5, $noin.'. '.$lap["keterangan_transaksi"].' '.number_format($lap["nominal"],0), 0, 1, 'L'); 
        $noin++;
    }
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'b', 12);
    $pdf->Cell(0, 5, "*Pengeluaran*", 0, 1, 'L');
    $pdf->SetFont('Arial', '', 12); 
    $qout= "SELECT keterangan_transaksi,(debet) as nominal FROM `aki_tabel_transaksi` WHERE keterangan_transaksi like '%payout%' and debet>1000000 and tanggal_transaksi='".date_format($date,"Y-m-d")."'";
    $resultout=mysqli_query($dbLink,$qout);
    while ($lap = mysqli_fetch_array($resultout)) {
        $pdf->Cell(0, 5, $noout.'. '.$lap["keterangan_transaksi"].' '.number_format($lap["nominal"],0), 0, 1, 'L');
        $noout++; 
    }
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'b', 12);
    $pdf->Cell(0, 5, "*Rekap Pembayaran, *".(strftime('%A', strtotime($tglJurnal1)))." ".$tglJurnal1."*", 0, 1, 'L');
    $pdf->SetFont('Arial', '', 12); 
    $qpay= "SELECT keterangan_transaksi,(debet) as nominal FROM `aki_tabel_transaksi` WHERE (keterangan_transaksi like '%pembayaran%' or keterangan_transaksi like '%dp%') and debet>1000000 and tanggal_transaksi='".date_format($date,"Y-m-d")."'";
    $resultpay=mysqli_query($dbLink,$qpay);
    while ($lap = mysqli_fetch_array($resultpay)) {
        $pdf->Cell(0, 5, $nopay.'. '.$lap["keterangan_transaksi"].' '.number_format($lap["nominal"],0), 0, 1, 'L');
        $nopay++; 
    }
    $pdf->Ln(3);  
    $pdf->SetFont('Arial', 'b', 12);
    $pdf->Cell(0, 5, "*Detail Transaksi*", 0, 1, 'L');
    $pdf->Ln(3);  
    $pdf->SetFont('Arial', '', 10); 
    $pdf->SetFillColor(255,0,0);
    $pdf->Cell(19,6,'Kode Akun',1,0,'C',0);
    $pdf->Cell(45,6,'Nama Akun',1,0,'C',0);
    $pdf->Cell(66,6,'Keterangan',1,0,'C',0);
    $pdf->Cell(30,6,'Debet (Rp)',1,0,'C',0);
    $pdf->Cell(30,6,'Kredit (Rp)',1,1,'C',0);
    $pdf->SetWidths(array(19,45,66,30,30));
    $pdf->SetAligns(array('C','L','L','R','R'));
    //database
    $q = "SELECT t.tanggal_transaksi, t.kode_transaksi, t.kode_rekening, m.nama_rekening, m.nama_rekening, t.keterangan_transaksi, t.debet, t.kredit ";
    $q.= "FROM aki_tabel_transaksi t left join aki_tabel_master m on t.kode_rekening=m.kode_rekening ";
    $q.= "WHERE 1=1 ".$filter;
    $q.= " ORDER BY t.tanggal_transaksi, t.id_transaksi ";
    $result=mysqli_query($dbLink,$q);
    // $no = 1;
    $totDebet = 0;
    $totKredit = 0;
    //$rsLap = mysql_query($q, $dbLink);
    while ($lap = mysqli_fetch_array($result)) {
        $pdf->Row(array($lap["kode_rekening"],$lap["nama_rekening"],$lap["keterangan_transaksi"],number_format($lap["debet"],0),number_format($lap["kredit"],0)));
         $totDebet += $lap["debet"];
         $totKredit += $lap["kredit"];
    }
    $pdf->Cell(130,7,'Total Transaksi',1,0,'R',0);
    $pdf->Cell(30,7,number_format($totDebet,0),'LTB',0,'R',0);
    $pdf->Cell(30,7,number_format($totKredit,0),1,1,'R',0);

    //output file PDF
    $pdf->Output('BukuJurnal_'.$tglJurnal1.'.pdf', 'I'); //download file pdf
?>