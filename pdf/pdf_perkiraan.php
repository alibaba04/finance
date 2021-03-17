<?php
    require_once('../function/fpdf/html_table.php');
    require_once ("../function/fungsi_formatdate.php");
    require_once ("../function/fungsi_convertNumberToWord.php");
    $pdf=new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 14); 
    $pdf->Cell(0,7,'COA (Chart Of Account)',0,1,'C',0);
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', '', 11); 
    $pdf->Cell(25,6,'Kode Akun',1,0,'C',0);
    $pdf->Cell(112,6,'Nama Akun',1,0,'C',0);
    $pdf->Cell(53,6,'Posisi',1,1,'C',0);
    
    //database
    $q = "SELECT m.kode_rekening, m.nama_rekening, m.awal_debet, m.awal_kredit, m.posisi, m.normal ";
    $q.= "FROM aki_tabel_master m ";
    $q.= "WHERE 1=1 " ;
    $q.= " ORDER BY m.kode_rekening asc ";
    $totDebet = $totKredit = 0;
    $rsLap = mysql_query($q, $dbLink);
    while ($lap = mysql_fetch_array($rsLap)) {
        $pdf->Cell(25,6,$lap['kode_rekening'],1,0,'C',0);
        $pdf->Cell(112,6,$lap["nama_rekening"],1,0,'C',0);
        $pdf->Cell(53,6,$lap["posisi"],1,1,'C',0);
        /*$totDebet += $lap["debet"];
        $totKredit += $lap["kredit"];
        if (($query_data["kode_rekening"]) == '1.0.00.000' OR ($query_data["kode_rekening"]) == '2.0.00.000' OR ($query_data["kode_rekening"]) == '3.0.00.000' OR ($query_data["kode_rekening"]) == '4.0.00.000' OR ($query_data["kode_rekening"]) == '5.0.00.000') {
            $totDebet1 += $query_data["awal_debet"];
            $totKredi1 += $query_data["awal_kredit"];
        }
        //SISA HASIL USAHA
        if (($query_data["kode_rekening"]) == '3.2.00.000' ) {
            $totDebet3 = $query_data["awal_debet"];
        }
        $totDebet = $totDebet1-$totDebet3;
        $totKredit = $totKredi1-$totDebet3;*/
    }
    // $pdf->Cell(25,6,'','LTB',0,'C',0);
    // $pdf->Cell(112,6,'JUMLAH',1,0,'C',0);
    // $pdf->Cell(53,6,$lap["posisi"],1,1,'C',0);
    //output file PDF
    $pdf->Output('Perkiraan.pdf', 'I'); //download file pdf
?>