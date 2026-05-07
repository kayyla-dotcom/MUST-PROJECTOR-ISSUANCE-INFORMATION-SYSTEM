<?php
/*

  reports/all_transactions.php
  All Transactions Report
*/
$conn = mysqli_connect('localhost', 'root','' , 'projector_system');
require_once '../includes/auth.php';
requireAdmin();
require_once '../db.php';

//GET ALL TRANSACTIONS 
$result = mysqli_query($conn,
    "SELECT
        t.id,
        t.date_issued,
        t.expected_return,
        t.date_returned,
        t.status,
        t.purpose,
        p.serial_number,
        p.model    AS projector_model,
        p.brand,
        b.full_name AS borrower_name,
        b.department,
        u.username  AS issued_by
     FROM tbl_transactions t
     JOIN tbl_projectors p ON t.projector_id = p.id
     JOIN tbl_borrowers  b ON t.borrower_id  = b.id
     JOIN tbl_users      u ON t.issued_by    = u.id
     ORDER BY t.date_issued DESC"
);

//COUNT TOTALS FOR SUMMARY 
$totalAll      = mysqli_num_rows($result);
$totalIssued   = 0;
$totalReturned = 0;
$totalOverdue  = 0;


$rows = array();
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
    if ($row['status'] == 'issued')   $totalIssued++;
    if ($row['status'] == 'returned') $totalReturned++;
    if ($row['status'] == 'overdue')  $totalOverdue++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Transactions Report</title>
    <style>
        /*  PAGE STYLES  */
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: black;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }

        /* REPORT PAPER */
        .report-page {
            background: white;
            max-width: 1100px;
            margin: 0 auto;
            padding: 30px 35px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        /*  HEADER BAR  */
        .report-header {
            background: #c0392b;
            color: white;
            padding: 16px 20px;
            margin: -30px -35px 25px -35px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .report-header h1 {
            font-size: 18px;
            margin: 0;
        }

        .report-header .date {
            font-size: 12px;
            opacity: 0.85;
        }

        /* SUMMARY BOXES */
        .summary-row {
            display: flex;
            gap: 12px;
            margin-bottom: 22px;
        }

        .summary-box {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 12px 15px;
            text-align: center;
        }

        .summary-box .label {
            font-size: 11px;
            color: black;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .summary-box .value {
            font-size: 22px;
            font-weight: 700;
            margin-top: 4px;
        }

        .summary-box.green .value  { color: #1a7a4a; }
        .summary-box.orange .value { color: #c05c00; }
        .summary-box.blue .value   { color: #1d4ed8; }
        .summary-box.red .value    { color: #b91c1c; }

        /* ---- TABLE ---- */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        thead th {
            background: #c0392b;
            color: white;
            padding: 8px 10px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        tbody td {
            padding: 7px 10px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }

        tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        /* ---- STATUS BADGES ---- */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
        }

        .badge-available  { background: #d1fae5; color: #065f46; }
        .badge-issued     { background: #fef3c7; color: #92400e; }
        .badge-returned   { background: #dbeafe; color: #1e40af; }
        .badge-overdue    { background: #fee2e2; color: #991b1b; }

        /* PRINT BUTTON  */
        .print-btn {
            background: #c0392b;
            color: white;
            border: none;
            padding: 10px 24px;
            font-size: 14px;
            border-radius: 6px;
            cursor: pointer;
            margin-bottom: 18px;
            font-family: Arial, sans-serif;
        }

        .back-btn {
            background: #c0392b;
            color: white;
            border: none;
            padding: 10px 18px;
            font-size: 14px;
            border-radius: 6px;
            cursor: pointer;
            margin-bottom: 18px;
            margin-right: 8px;
            font-family: Arial, sans-serif;
            text-decoration: none;
            display: inline-block;
        }

        /* HIDE BUTTONS WHEN PRINTING */
        /* @media print means: only apply this CSS when printing */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white;
                padding: 0;
                color: black;
            }
            .report-page {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            .report-header {
                margin: 0 0 20px 0;
            }
        }
    </style>
</head>
<body>

<!-- BUTTONS (hidden when printing) -->
<div class="no-print" style="max-width:1100px; margin:0 auto 12px auto;">
    <a href="../admin/transactions.php" class="back-btn"> Back</a>
    <button class="print-btn" onclick="window.print()">
     Save as PDF
    </button>
</div>

<!-- REPORT PAGE -->
<div class="report-page">

    <!-- Header -->
    <div class="report-header">
        <h1>All Transactions Report</h1>
        <div class="date">
    
            Projector Issuance Information System
        </div>
    </div>

    <!-- Summary boxes -->
    <div class="summary-row">
        <div class="summary-box green">
            <div class="label">Total Transactions</div>
            <div class="value"><?php echo $totalAll; ?></div>
        </div>
        <div class="summary-box blue">
            <div class="label">Returned</div>
            <div class="value"><?php echo $totalReturned; ?></div>
        </div>
        <div class="summary-box orange">
            <div class="label">Still Issued</div>
            <div class="value"><?php echo $totalIssued; ?></div>
        </div>
        <div class="summary-box red">
            <div class="label">Overdue</div>
            <div class="value"><?php echo $totalOverdue; ?></div>
        </div>
    </div>

    <!-- Transactions Table -->
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Serial No.</th>
                <th>Projector</th>
                <th>Borrower</th>
                <th>Department</th>
                <th>Purpose</th>
                <th>Issued By</th>
                <th>Date Issued</th>
                <th>Expected Return</th>
                <th>Returned On</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>

        <?php if (count($rows) == 0): ?>
            <tr>
                <td colspan="11" style="text-align:center; padding:20px; color:#666;">
                    No transactions found.
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($rows as $t): ?>
            <tr>
                <td><?php echo $t['id']; ?></td>
                <td><?php echo $t['serial_number']; ?></td>
                <td><?php echo $t['brand'] . ' ' . $t['projector_model']; ?></td>
                <td><?php echo $t['borrower_name']; ?></td>
                <td><?php echo $t['department']; ?></td>
                <td><?php echo $t['purpose'] ? $t['purpose'] : '—'; ?></td>
                <td><?php echo $t['issued_by']; ?></td>
                <td><?php echo $t['date_issued']; ?></td>
                <td><?php echo $t['expected_return']; ?></td>
                <td><?php echo $t['date_returned'] ? $t['date_returned'] : '—'; ?></td>
                <td>
                    <span class="badge badge-<?php echo $t['status']; ?>">
                        <?php echo $t['status']; ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>

        </tbody>
    </table>

    <!-- Footer -->
    <div style="margin-top:20px; padding-top:12px; border-top:1px solid #ddd; font-size:11px; color:black; text-align:center;">
        MUST Projector Issuance Information System &nbsp;|&nbsp;
        Total Records: <?php echo $totalAll; ?> &nbsp;|&nbsp;
        Printed: <?php echo date('d M Y H:i'); ?>
    </div>

</div>

</body>
</html>
