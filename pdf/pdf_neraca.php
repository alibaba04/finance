<?php
    require_once('../function/fpdf/html_table.php');
    require_once ("../function/fungsi_formatdate.php");
    require_once ("../function/fungsi_convertNumberToWord.php");
    $pdf=new FPDF();
    $pdf->AddPage('');
    $pdf->SetMargins(12, 20, 10, true);
    
    $filter = "";
    $html = "";
    $talancar=$tatetap=$tkewajiban=$tekuitas=0;
   
    $pdf->SetFont('Helvetica', '', 14);
    $pdf->Cell(0, 7, "DATA TRANSAKSI NERACA", 0, 1, 'C');
    //ISI
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', '', 11); 
    $pdf->Cell(28,6,'Kode Akun',1,0,'C',0);
    $pdf->Cell(75,6,'Nama Akun',1,0,'C',0);
    $pdf->Cell(30,6,'Normal',1,0,'C',0);
    $pdf->Cell(56,6,'Saldo (Rp)',1,1,'C',0);
   	
    $filter = "";
    if (($_GET["bulan"])!=''){
        $filter = $filter . " AND month(t.tanggal_transaksi)= '" . $_GET["bulan"] . "' AND year(t.tanggal_transaksi)= '" . $_GET["tahun"] ."'";
    }else{
        $filter = "";
    }
    //database
    $q = "SELECT m.kode_rekening, m.nama_rekening, m.awal_debet, m.awal_kredit,IFNULL((b.debet),0) as debet,IFNULL((b.kredit),0) as kredit,IFNULL((c.debet),0) as pdebet,IFNULL((c.kredit),0) as pkredit,b.ref,m.normal  FROM `aki_tabel_master` m";
    $q.= " left join (SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet,  sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1";
    $q.=$filter." and ket_hitungrlneraca!='-' and keterangan_posting='Post' and t.ref='-' GROUP by m.kode_rekening) as b ";
    $q.="on m.kode_rekening=b.kode_rekening left join";
    $q.="(SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet, sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1 ";
    $q.=$filter." and ket_hitungrlneraca!='-' and keterangan_posting='Post' and t.ref!='-' GROUP by m.kode_rekening) as c on m.kode_rekening=c.kode_rekening";
    $q.=" where m.kode_rekening BETWEEN '1110.000' and '1140.003' or m.kode_rekening BETWEEN '1300.000' and '1453.000' GROUP by m.kode_rekening ORDER BY m.kode_rekening asc" ;
    
    $result=mysqli_query($dbLink,$q);
    $totADebet=$totAKredit=0;
    $nsdebet=0;
    $nskredit=0;
    $nspenyesuaianD=0;
    $nspenyesuaianK=0;
    $totslado = 0;
    while ($query_data = mysqli_fetch_array($result)) {
        if ($query_data["normal"] == 'Debit') {
            $nsdebet = $query_data["awal_debet"]+$query_data["debet"]-$query_data["awal_kredit"]-$query_data["kredit"];
            $nspenyesuaianD = $nsdebet+$query_data["pdebet"]-$nskredit-$query_data["pkredit"];
        }else{
            $nskredit = $query_data["awal_kredit"]+$query_data["kredit"]-$query_data["awal_debet"]-$query_data["debet"];
            $nspenyesuaianD = $nskredit+$query_data["pkredit"]-$nsdebet-$query_data["pdebet"];
        }
        if ($nspenyesuaianD!=0){
            $pdf->SetFont('helvetica', '', 11); 
            $pdf->Cell(28,6,$query_data["kode_rekening"],1,0,'C',0);
            $pdf->Cell(75,6, $query_data["nama_rekening"],1,0,'C',0);
            $pdf->Cell(30,6,$query_data["normal"],1,0,'C',0);
            $pdf->Cell(56,6,number_format( $nspenyesuaianD, 2),1,1,'R',0);
        }   
        $talancar+=$nspenyesuaianD;
    }
    $pdf->SetFont('helvetica', 'B', 11); 
    $pdf->Cell(133,6,'Total aktiva Lancar',1,0,'C',0);
    $pdf->Cell(56,6,number_format( $talancar, 2),1,1,'R',0);
    $pdf->Ln(5);
    $filter = "";
    if (($_GET["bulan"])!=''){
        $filter = $filter . " AND month(t.tanggal_transaksi)= '" . $_GET["bulan"] . "' AND year(t.tanggal_transaksi)= '" . $_GET["tahun"] ."'";
    }else{
        $filter = "";
    }
    //database
    $q = "SELECT m.kode_rekening, m.nama_rekening, m.awal_debet, m.awal_kredit,IFNULL((b.debet),0) as debet,IFNULL((b.kredit),0) as kredit,IFNULL((c.debet),0) as pdebet,IFNULL((c.kredit),0) as pkredit,b.ref,m.normal  FROM `aki_tabel_master` m";
    $q.= " left join (SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet,  sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1";
    $q.=$filter." and ket_hitungrlneraca!='-' and keterangan_posting='Post' and t.ref='-' GROUP by m.kode_rekening) as b ";
    $q.="on m.kode_rekening=b.kode_rekening left join";
    $q.="(SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet, sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1 ";
    $q.=$filter." and ket_hitungrlneraca!='-' and keterangan_posting='Post' and t.ref!='-' GROUP by m.kode_rekening) as c on m.kode_rekening=c.kode_rekening";
    $q.=" where m.kode_rekening BETWEEN '1140.004' and '1270.000' or m.kode_rekening BETWEEN '1500.000' and '1790.000' GROUP by m.kode_rekening ORDER BY m.kode_rekening asc" ;
    $rs = mysql_query($q, $dbLink);
    
    $result=mysqli_query($dbLink,$q);
    $totADebet=$totAKredit=0;
    $nsdebet=0;
    $nskredit=0;
    $nspenyesuaianD=0;
    $nspenyesuaianK=0;
    $totslado = 0;
    while ($query_data = mysqli_fetch_array($result)) {
        if ($query_data["normal"] == 'Debit') {
            $nsdebet = $query_data["awal_debet"]+$query_data["debet"]-$query_data["awal_kredit"]-$query_data["kredit"];
            $nspenyesuaianD = $nsdebet+$query_data["pdebet"]-$nskredit-$query_data["pkredit"];
        }else{
            $nskredit = $query_data["awal_kredit"]+$query_data["kredit"]-$query_data["awal_debet"]-$query_data["debet"];
            $nspenyesuaianD = $nskredit+$query_data["pkredit"]-$nsdebet-$query_data["pdebet"];
        }
        if ($nspenyesuaianD!=0){
            $pdf->SetFont('helvetica', '', 11); 
            $pdf->Cell(28,6,$query_data["kode_rekening"],1,0,'C',0);
            $pdf->Cell(75,6, $query_data["nama_rekening"],1,0,'C',0);
            $pdf->Cell(30,6,$query_data["normal"],1,0,'C',0);
            $pdf->Cell(56,6,number_format( $nspenyesuaianD, 2),1,1,'R',0);
        }   
        $tatetap+=$nspenyesuaianD;
    }
    $pdf->SetFont('helvetica', 'B', 11); 
    $pdf->Cell(133,6,'Total aktiva Tetap',1,0,'C',0);
    $pdf->Cell(56,6,number_format( $tatetap, 2),1,1,'R',0);
    $pdf->Cell(133,6,'Total aktiva',1,0,'C',0);
    $pdf->Cell(56,6,number_format( $talancar+$tatetap, 2),1,1,'R',0);
    $pdf->Ln(5);
    $filter = "";
    if (($_GET["bulan"])!=''){
        $filter = $filter . " AND month(t.tanggal_transaksi)= '" . $_GET["bulan"] . "' AND year(t.tanggal_transaksi)= '" . $_GET["tahun"] ."'";
    }else{
        $filter = "";
    }
    //database
    $q = "SELECT m.kode_rekening, m.nama_rekening, m.awal_debet, m.awal_kredit,IFNULL((b.debet),0) as debet,IFNULL((b.kredit),0) as kredit,IFNULL((c.debet),0) as pdebet,IFNULL((c.kredit),0) as pkredit,b.ref,m.normal  FROM `aki_tabel_master` m";
    $q.= " left join (SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet,  sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1";
    $q.=$filter." and ket_hitungrlneraca!='-' and keterangan_posting='Post' and t.ref='-' GROUP by m.kode_rekening) as b ";
    $q.="on m.kode_rekening=b.kode_rekening left join";
    $q.="(SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet, sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1 ";
    $q.=$filter." and ket_hitungrlneraca!='-' and keterangan_posting='Post' and t.ref!='-' GROUP by m.kode_rekening) as c on m.kode_rekening=c.kode_rekening";
    $q.=" where m.kode_rekening BETWEEN '2110.000' and '2310.000' GROUP by m.kode_rekening ORDER BY m.kode_rekening asc" ;
    $rs = mysql_query($q, $dbLink);
    
    $result=mysqli_query($dbLink,$q);
    $totADebet=$totAKredit=0;
    $nsdebet=0;
    $nskredit=0;
    $nspenyesuaianD=0;
    $nspenyesuaianK=0;
    $totslado = 0;
    while ($query_data = mysqli_fetch_array($result)) {
        if ($query_data["normal"] == 'Debit') {
            $nsdebet = $query_data["awal_debet"]+$query_data["debet"]-$query_data["awal_kredit"]-$query_data["kredit"];
            $nspenyesuaianD = $nsdebet+$query_data["pdebet"]-$nskredit-$query_data["pkredit"];
        }else{
            $nskredit = $query_data["awal_kredit"]+$query_data["kredit"]-$query_data["awal_debet"]-$query_data["debet"];
            $nspenyesuaianD = $nskredit+$query_data["pkredit"]-$nsdebet-$query_data["pdebet"];
        }
        if ($nspenyesuaianD!=0){
            $pdf->SetFont('helvetica', '', 11); 
            $pdf->Cell(28,6,$query_data["kode_rekening"],1,0,'C',0);
            $pdf->Cell(75,6, $query_data["nama_rekening"],1,0,'C',0);
            $pdf->Cell(30,6,$query_data["normal"],1,0,'C',0);
            $pdf->Cell(56,6,number_format( $nspenyesuaianD, 2),1,1,'R',0);
        }   
        $tatetap+=$nspenyesuaianD;
    }
    $pdf->SetFont('helvetica', 'B', 11); 
    $pdf->Cell(133,6,'Total Kewajiban',1,0,'C',0);
    $pdf->Cell(56,6,number_format( $tatetap, 2),1,1,'R',0);
    $pdf->Ln(5);
    $filter = "";
    if (($_GET["bulan"])!=''){
        $filter = $filter . " AND month(t.tanggal_transaksi)= '" . $_GET["bulan"] . "' AND year(t.tanggal_transaksi)= '" . $_GET["tahun"] ."'";
    }else{
        $filter = "";
    }
    //database
    $q = "SELECT m.kode_rekening, m.nama_rekening, m.awal_debet, m.awal_kredit,IFNULL((b.debet),0) as debet,IFNULL((b.kredit),0) as kredit,IFNULL((c.debet),0) as pdebet,IFNULL((c.kredit),0) as pkredit,b.ref,m.normal  FROM `aki_tabel_master` m";
    $q.= " left join (SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet,  sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1";
    $q.=$filter." and ket_hitungrlneraca!='-' and keterangan_posting='Post' and t.ref='-' GROUP by m.kode_rekening) as b ";
    $q.="on m.kode_rekening=b.kode_rekening left join";
    $q.="(SELECT m.kode_rekening, m.nama_rekening,m.awal_debet, m.awal_kredit, sum(t.debet) as debet, sum(t.kredit)as kredit,m.normal,t.ref FROM aki_tabel_master m INNER JOIN aki_tabel_transaksi t ON t.kode_rekening=m.kode_rekening WHERE 1=1 ";
    $q.=$filter." and ket_hitungrlneraca!='-' and keterangan_posting='Post' and t.ref!='-' GROUP by m.kode_rekening) as c on m.kode_rekening=c.kode_rekening";
    $q.=" where m.kode_rekening BETWEEN '3000.000' and '3390.000' GROUP by m.kode_rekening ORDER BY m.kode_rekening asc" ;
    $rs = mysql_query($q, $dbLink);
    
    $result=mysqli_query($dbLink,$q);
    $totADebet=$totAKredit=0;
    $nsdebet=0;
    $nskredit=0;
    $nspenyesuaianD=0;
    $nspenyesuaianK=0;
    $totslado = 0;
    while ($query_data = mysqli_fetch_array($result)) {
        if ($query_data["normal"] == 'Debit') {
            $nsdebet = $query_data["awal_debet"]+$query_data["debet"]-$query_data["awal_kredit"]-$query_data["kredit"];
            $nspenyesuaianD = $nsdebet+$query_data["pdebet"]-$nskredit-$query_data["pkredit"];
        }else{
            $nskredit = $query_data["awal_kredit"]+$query_data["kredit"]-$query_data["awal_debet"]-$query_data["debet"];
            $nspenyesuaianD = $nskredit+$query_data["pkredit"]-$nsdebet-$query_data["pdebet"];
        }
        if ($nspenyesuaianD!=0){
            $pdf->SetFont('helvetica', '', 11); 
            $pdf->Cell(28,6,$query_data["kode_rekening"],1,0,'C',0);
            $pdf->Cell(75,6, $query_data["nama_rekening"],1,0,'C',0);
            $pdf->Cell(30,6,$query_data["normal"],1,0,'C',0);
            $pdf->Cell(56,6,number_format( $nspenyesuaianD, 2),1,1,'R',0);
        }   
        $tatetap+=$nspenyesuaianD;
    }
    $pdf->SetFont('helvetica', 'B', 11); 
    $pdf->Cell(133,6,'Total Ekuitas',1,0,'C',0);
    $pdf->Cell(56,6,number_format( $tatetap, 2),1,1,'R',0);

    //output file PDF
    $pdf->Output('BukuJurnal.pdf', 'I'); //download file pdf
?>