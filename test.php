<?php
require_once __DIR__ . '/vendor/autoload.php'; // Include mPDF

use Mpdf\Mpdf;

try {
    // Initialize mPDF
    $mpdf = new Mpdf();

    // Start output buffering to capture the HTML
    ob_start();
    ?>
    <!-- Your HTML content goes here -->
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Report</title>
        <style>
            body { font-family: Arial, sans-serif; }
            h1 { text-align: center; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
            th { background-color: #f4f4f4; }
        </style>
    </head>
    <body>
        <h1>Kutipan Zakat Report</h1>
        <h2>Monthly Performance</h2>
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Year</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'dbconn.php'; // Database connection
                $query = "SELECT months, years, amount FROM kutipan_bulanan ORDER BY years, months";
                $result = $dbconn->query($query);
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['months']}</td>
                            <td>{$row['years']}</td>
                            <td>" . number_format($row['amount'], 2) . "</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </body>
    </html>
    <?php
    // Capture the HTML content
    $html = ob_get_clean();

    // Write HTML to PDF
    $mpdf->WriteHTML($html);

    // Output the PDF for download
    $mpdf->Output('Kutipan_Zakat_Report.pdf', 'D'); // 'D' forces download
} catch (\Mpdf\MpdfException $e) {
    echo "An error occurred: " . $e->getMessage();
}
