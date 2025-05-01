<?php
require_once 'tcpdf/tcpdf.php';

class Export {
    public static function toCSV($data, $filename = 'export.csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Add header row
        fputcsv($output, array('ID', 'Name', 'Email', 'Phone', 'Profile Pic', 'Resume'));
        
        // Add data rows
        foreach($data as $row) {
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit;
    }
    
    public static function toPDF($data, $filename = 'export.pdf') {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Anoop Maurya');
        $pdf->SetTitle('Users Export');
        $pdf->SetSubject('Users Data');
        $pdf->SetKeywords('Users, PDF, Export');
        
        $pdf->AddPage();
        
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 15, 'Users List', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 10);
        
        $html = '<table border="1" cellpadding="5">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Profile Pic</th>
                <th>Resume</th>
            </tr>';
        
        foreach($data as $row) {
            $html .= '<tr>
                <td>' . $row['id'] . '</td>
                <td>' . $row['name'] . '</td>
                <td>' . $row['email'] . '</td>
                <td>' . $row['phone'] . '</td>
                <td>' . $row['profile_pic'] . '</td>
                <td>' . $row['resume'] . '</td>
            </tr>';
        }
        
        $html .= '</table>';
        
        $pdf->writeHTML($html, true, false, true, false, '');
        
        $pdf->Output($filename, 'D');
        exit;
    }
}
?>