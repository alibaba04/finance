<?php
    require_once("../function/fpdf/html_table.php");
    require_once ("../function/fungsi_formatdate.php");
    require_once ("../function/fungsi_convertNumberToWord.php");
    $pdf=new PDF();
    $pdf->AddPage('L');
    $pdf->SetMargins(12, 20, 10, true);
    $pdf->image('../dist/img/cop-aki.jpg',12,12,275,30);
    $pdf->SetFont('helvetica', '', 14); 
    $pdf->Cell(0, 7, "NERACA PERCOBAAN ", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', '', 11); 
$tbl = '<thead>
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
    </thead>';
$pdf->WriteHTML($tbl);
    $filter = "";
    if (isset($_GET["bulan"])){
        $filter = $filter . "AND month(t.tanggal_transaksi)= '" . $_GET["bulan"] . "' AND year(t.tanggal_transaksi)= '" . $_GET["tahun"] ."'";
    }else{
        $filter = "";
    }

    $q = "SELECT m.kode_rekening, m.nama_rekening, m.awal_debet, m.awal_kredit,IFNULL((b.debet),0) as debet,IFNULL((b.kredit),0) as kredit,IFNULL((c.debet),0) as pdebet,IFNULL((c.kredit),0) as pkredit,b.ref,m.normal,m.posisi  FROM `aki_tabel_master` m";
    $q.= " left join (SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet,  sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1 ";
    $q.=$filter." and keterangan_posting='Post' and t.ref='-' GROUP by m.kode_rekening) as b ";
    $q.="on m.kode_rekening=b.kode_rekening left join";
    $q.="(SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet, sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1 ";
    $q.=$filter." and keterangan_posting='Post' and t.ref!='-' GROUP by m.kode_rekening) as c on m.kode_rekening=c.kode_rekening where 1=1 ";
    $q.=" GROUP by m.kode_rekening ORDER BY m.kode_rekening asc" ;
    $totDebet = $totKredit = $totmDebet = $totmKredit = $totsDebet = $totsKredit = 0;
    $rsLap = mysql_query($q, $dbLink);
    while ($lap = mysql_fetch_array($rsLap)) {
/*
        $pdf->SetMargins(12, 20, 10, true);
        $pdf->image('../dist/img/cop-aki.jpg',12,12,275,30);
        $pdf->Cell(25,6,$lap['kode_rekening'],1,0,'C',0);
        $pdf->Cell(90,6,$lap["nama_rekening"],1,0,'C',0);
        $pdf->Cell(40,6,number_format($lap["awal_debet"], 2),1,0,'R',0);
        $pdf->Cell(40,6,number_format($lap["awal_kredit"], 2),1,0,'R',0);
        $pdf->Cell(40,6,number_format($lap["awal_kredit"], 2),1,0,'R',0);
        $pdf->Cell(40,6,number_format($lap["awal_debet"], 2),1,1,'R',0);
            */
    }
   /* $pdf->Cell(25,7,'','LTB',0,'C',0);
    $pdf->Cell(90,7,'Jumlah','TRB',0,'R',0);
    $pdf->Cell(40,7,number_format($totDebet, 2),'LTB',0,'R',0);
    $pdf->Cell(40,7,number_format($totKredit, 2),1,0,'R',0);
    $pdf->Cell(40,7,number_format($totmDebet, 2),1,0,'R',0);
    $pdf->Cell(40,7,number_format($totmKredit, 2),1,1,'R',0);*/


    //output file PDF
    $pdf->Output('BukuJurnal.pdf', 'I'); //download file pdf
?>