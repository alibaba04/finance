<?php
    require_once('../function/fpdf/html_table.php');
    require_once ("../function/fungsi_formatdate.php");
    require_once ("../function/fungsi_convertNumberToWord.php");
    $pdf=new FPDF();
    $pdf->AddPage('L');
    $pdf->SetMargins(12, 20, 10, true);
    $pdf->image('../dist/img/cop-aki.jpg',12,12,275,30);
    $tglJurnal1 = $_GET['tglJurnal1'];
    $tglJurnal2 = $_GET['tglJurnal2'];
    $no = $_GET['no'];
    
    $filter = "";

    $pdf->SetFont('Helvetica', '', 14);
    $pdf->Cell(0, 7, "DATA TRANSAKSI BUKU BESAR NO AKUN ".$no, 0, 1, 'C');

    if ($filter==""){
        $pdf->Cell(0, 5, "Sampai dengan periode : ".date('d-m-Y',time()), 0, 1, 'C');
    }else{
        $pdf->Cell(0, 5, "Periode : ".$tglJurnal1." s/d ".$tglJurnal2, 0, 1, 'C');
    }
    //ISI
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', '', 11); 
    $pdf->Cell(27,6,'Tanggal',1,0,'C',0);
    $pdf->Cell(40,6,'No Bukti',1,0,'C',0);
    $pdf->Cell(28,6,'Kode Akun',1,0,'C',0);
    $pdf->Cell(45,6,'Nama Akun',1,0,'C',0);
    $pdf->Cell(55,6,'Keterangan',1,0,'C',0);
    $pdf->Cell(40,6,'Debet (Rp)',1,0,'C',0);
    $pdf->Cell(40,6,'Kredit (Rp)',1,1,'C',0);
    if (isset($_GET["tglJurnal"])){
        $tglJurnal = secureParam($_GET["tglJurnal"], $dbLink);
        $tglJurnal = explode(" - ", $tglJurnal);
        $tglJurnal1 = $tglJurnal[0];
        $tglJurnal2 = $tglJurnal[1];
        $namarek=$_GET["txtKodeRekeningbb"];
    }else{
        $tglJurnal1 = "";
        $tglJurnal2 = "";
    }
    $filter = "";
    if ($tglJurnal1 && $tglJurnal2)
        $filter = $filter . " AND t.tanggal_transaksi BETWEEN '" . tgl_mysql($tglJurnal1) . "' 
    AND '" . tgl_mysql($tglJurnal2) . "'  ";

    $q = "SELECT t.tanggal_transaksi, t.kode_transaksi, t.kode_rekening, m.nama_rekening, t.keterangan_transaksi, t.debet, t.kredit ";
    $q.= "FROM aki_tabel_transaksi t INNER JOIN aki_tabel_master m ON t.kode_rekening=m.kode_rekening  AND t.kode_rekening= '". $no."'  ";
    $q.= "WHERE 1=1 and keterangan_posting='Post'" . $filter;
    $q.= " ORDER BY t.tanggal_transaksi, id_transaksi ";
    $result=mysqli_query($dbLink,$q);
    $totDebet = 0;
    $totKredit = 0;
    while ($lap = mysqli_fetch_array($result)) {
       $pdf->Cell(27,6,$lap['tanggal_transaksi'],1,0,'C',0);
        $pdf->Cell(40,6,$lap["kode_transaksi"],1,0,'C',0);
        $pdf->Cell(28,6,$lap["kode_rekening"],1,0,'C',0);
        $pdf->Cell(45,6,$lap["nama_rekening"],1,0,'L',0);
        $pdf->Cell(55,6,$lap["keterangan_transaksi"],1,0,'L',0);
        $pdf->Cell(40,6,number_format($lap["debet"], 2),1,0,'R',0);
        $pdf->Cell(40,6,number_format($lap["kredit"], 2),1,1,'R',0);
        // $no++; 
         $totDebet += $lap["debet"];
         $totKredit += $lap["kredit"];
    }
    $pdf->Cell(195,7,'Total Transaksi',1,0,'R',0);
    $pdf->Cell(40,7,number_format($totDebet, 2),'LTB',0,'R',0);
    $pdf->Cell(40,7,number_format($totKredit, 2),1,1,'R',0);
    $pdf->Cell(195,7,'Total Saldo',1,0,'R',0);
    $pdf->Cell(80,7,number_format($totDebet-$totKredit, 2),1,1,'C',0);

    //output file PDF
    $pdf->Output('BukuJurnal.pdf', 'I'); //download file pdf
?>