<?php
    require_once('../function/fpdf/html_table.php');
    require_once ("../function/fungsi_formatdate.php");
    require_once ("../function/fungsi_convertNumberToWord.php");
    $pdf=new FPDF();
    $pdf->AddPage('');
    $pdf->SetMargins(12, 20, 10, true);
    
    $filter = "";
    $html = "";
    $tpend=$thpp=$tboper=$tpendlain=$tblain=0;
   
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
    $q.=" where m.kode_rekening BETWEEN '4000.000' and '4300.000' GROUP by m.kode_rekening ORDER BY m.kode_rekening asc" ;
    
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
        $tpend+=$nspenyesuaianD;
    }
    $pdf->SetFont('helvetica', 'B', 11); 
    $pdf->Cell(133,6,'Total Pendapatan',1,0,'C',0);
    $pdf->Cell(56,6,number_format( $tpend, 2),1,1,'R',0);
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
    $q.=" where m.kode_rekening BETWEEN '5000.000' and '5490.000' GROUP by m.kode_rekening ORDER BY m.kode_rekening asc" ;
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
        $thpp+=$nspenyesuaianD;
    }
    $pdf->SetFont('helvetica', 'B', 11); 
    $pdf->Cell(133,6,'Total Biaya Atas Pendapatan / HPP ',1,0,'C',0);
    $pdf->Cell(56,6,number_format( $thpp, 2),1,1,'R',0);
    $pdf->Cell(133,6,'Laba Kotor',1,0,'C',0);
    $pdf->Cell(56,6,number_format( $tpend+$thpp, 2),1,1,'R',0);
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
    $q.=" where m.kode_rekening BETWEEN '6000.000' and '6990.001' GROUP by m.kode_rekening ORDER BY m.kode_rekening asc" ;
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
        $tboper+=$nspenyesuaianD;
    }
    $pdf->SetFont('helvetica', 'B', 11); 
    $pdf->Cell(133,6,'Total Biaya Operasional',1,0,'C',0);
    $pdf->Cell(56,6,number_format( $tboper, 2),1,1,'R',0);
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
    $q.=" where m.kode_rekening BETWEEN '7000.000' and '7170.000' GROUP by m.kode_rekening ORDER BY m.kode_rekening asc" ;
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
        $tpendlain+=$nspenyesuaianD;
    }
    $pdf->SetFont('helvetica', 'B', 11); 
    $pdf->Cell(133,6,'Total Pendapatan Lainnya',1,0,'C',0);
    $pdf->Cell(56,6,number_format( $tpendlain, 2),1,1,'R',0);
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
    $q.=" where m.kode_rekening BETWEEN '8000.000' and '8190.000' GROUP by m.kode_rekening ORDER BY m.kode_rekening asc" ;
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
        $tblain+=$nspenyesuaianD;
    }
    $rl = ($tpend+$thpp)-$tboper+$tpendlain-$tblain;
    $trl = '';
    if ($rl<0) {
       $trl = 'Rugi';
    }else{
        $trl = 'Laba';
    }
    $pdf->SetFont('helvetica', 'B', 11); 
    $pdf->Cell(133,6,'Total Biaya Lainnya',1,0,'C',0);
    $pdf->Cell(56,6,number_format( $tblain, 2),1,1,'R',0);
    $pdf->Cell(133,6,$trl,1,0,'C',0);
    $pdf->Cell(56,6,number_format( ($tpend+$thpp)-$tboper+$tpendlain-$tblain, 2),1,1,'R',0);

    //output file PDF
    $pdf->Output('BukuJurnal.pdf', 'I'); //download file pdf
?>