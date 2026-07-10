<?php
require('../fpdf/fpdf.php');
include "../database/db.php";

$invoice_id = $_GET['id'];

// 1. Fetch general invoice, sale and customer details
$sql = "SELECT
        i.invoice_id,
        i.invoice_date,
        i.payment_status,
        i.total_amount,
        s.sale_id,
        c.name,
        c.phone,
        c.email
        FROM invoices i
        JOIN sales s ON i.sale_id = s.sale_id
        JOIN customers c ON s.customer_id = c.customer_id
        WHERE i.invoice_id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $invoice_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$invoice = mysqli_fetch_assoc($result);

if (!$invoice) {
    die("Invoice not found.");
}

$sale_id = $invoice['sale_id'];

// 2. Fetch all products associated with this sale
$items_sql = "SELECT
              p.product_name,
              si.quantity,
              si.price,
              si.subtotal
              FROM sale_items si
              JOIN products p ON si.product_id = p.product_id
              WHERE si.sale_id = ?";

$items_stmt = mysqli_prepare($conn, $items_sql);
mysqli_stmt_bind_param($items_stmt, "i", $sale_id);
mysqli_stmt_execute($items_stmt);
$items_result = mysqli_stmt_get_result($items_stmt);

// 3. Generate PDF
$pdf = new FPDF();
$pdf->AddPage();

// Set Courier font for the text receipt style
$pdf->SetFont('Courier', 'B', 12);

// Header Section
$pdf->Cell(190, 4, "--------------------------------------------------------", 0, 1, 'C');
$pdf->Cell(190, 6, "CRM SYSTEM", 0, 1, 'C');
$pdf->SetFont('Courier', '', 11);
$pdf->Cell(190, 5, "Sales * Billing * HR Management", 0, 1, 'C');
$pdf->SetFont('Courier', 'B', 12);
$pdf->Cell(190, 4, "--------------------------------------------------------", 0, 1, 'C');
$pdf->Ln(6);

// Invoice No & Date
$pdf->SetFont('Courier', '', 11);
$invoice_num = "INV-" . str_pad($invoice['invoice_id'], 3, '0', STR_PAD_LEFT);
$invoice_date = date('d-m-Y', strtotime($invoice['invoice_date']));
$pdf->Cell(95, 6, "Invoice No : " . $invoice_num, 0, 0);
$pdf->Cell(95, 6, "Date : " . $invoice_date, 0, 1, 'R');
$pdf->Ln(6);

// Bill To Section
$pdf->SetFont('Courier', 'B', 11);
$pdf->Cell(190, 6, "Bill To", 0, 1);
$pdf->SetFont('Courier', '', 11);
$pdf->Cell(190, 4, "------------------------------------", 0, 1);
$pdf->Cell(190, 6, "Customer : " . htmlspecialchars($invoice['name']), 0, 1);
$pdf->Cell(190, 6, "Phone    : " . htmlspecialchars($invoice['phone']), 0, 1);
$pdf->Cell(190, 6, "Email    : " . htmlspecialchars($invoice['email']), 0, 1);
$pdf->Ln(6);

// Items Table Header
$pdf->Cell(190, 4, "--------------------------------------------------------", 0, 1, 'C');
$pdf->SetFont('Courier', 'B', 11);
// Widths: Product (90), Qty (20), Price (40), Total (40)
$pdf->Cell(90, 6, "Product", 0, 0);
$pdf->Cell(20, 6, "Qty", 0, 0, 'C');
$pdf->Cell(40, 6, "Price", 0, 0, 'R');
$pdf->Cell(40, 6, "Total", 0, 1, 'R');
$pdf->Cell(190, 4, "--------------------------------------------------------", 0, 1, 'C');

// Items Content (Loop through all products in the sale)
$pdf->SetFont('Courier', '', 11);
while ($item = mysqli_fetch_assoc($items_result)) {
    $pdf->Cell(90, 6, htmlspecialchars($item['product_name']), 0, 0);
    $pdf->Cell(20, 6, $item['quantity'], 0, 0, 'C');
    $pdf->Cell(40, 6, "Rs. " . number_format($item['price'], 2), 0, 0, 'R');
    $pdf->Cell(40, 6, "Rs. " . number_format($item['subtotal'], 2), 0, 1, 'R');
}

// Grand Total
$pdf->Cell(190, 4, "--------------------------------------------------------", 0, 1, 'C');
$pdf->SetFont('Courier', 'B', 11);
$pdf->Cell(110, 6, "Grand Total", 0, 0);
$pdf->Cell(80, 6, "Rs. " . number_format($invoice['total_amount'], 2), 0, 1, 'R');
$pdf->Ln(6);

// Status and Footer Section
$pdf->SetFont('Courier', 'B', 11);
$pdf->Cell(190, 6, "Payment Status : " . strtoupper($invoice['payment_status']), 0, 1);
$pdf->Ln(6);

$pdf->SetFont('Courier', 'I', 11);
$pdf->Cell(190, 6, "Thank you for your business!", 0, 1);
$pdf->Ln(18);

$pdf->SetFont('Courier', '', 11);
$pdf->Cell(190, 6, "Authorized Signature", 0, 1, 'R');

$pdf->Output();
?>