<?php

$conn = mysqli_connect('localhost', 'root','' , 'projector_system');
require_once '../includes/auth.php';
requireAdmin();
require_once '../db.php';

/* AUTO MARK OVERDUE -/
mysqli_query($conn,
    "UPDATE tbl_transactions SET status = 'overdue'
     WHERE status = 'issued'
     AND expected_return < CURDATE()"
);

/* GET ALL OVERDUE TRANSACTIONS*/
$result = mysqli_query($conn,
    "SELECT
        t.id,
        t.date_issued,
        t.expected_return,
        t.purpose,
        p.serial_number,
        p.model    AS projector_model,
        p.brand,
        b.full_name AS borrower_name,
        b.department,
        b.contact,
        b.email,
        u.username  AS issued_by,
        DATEDIFF(CURDATE(), t.expected_return) AS days_overdue
     FROM tbl_transactions t
     JOIN tbl_projectors p ON t.projector_id = p.id
     JOIN tbl_borrowers  b ON t.borrower_id  = b.id
     JOIN tbl_users      u ON t.issued_by    = u.id
     WHERE t.status = 'overdue'
     ORDER BY t.expected_return ASC"
);

$rows = array();
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Overdue Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #111;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }

        .report-page {
            background: white;
            max-width: 1050px;
            margin: 0 auto;
            padding: 30px 35px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .report-header {
            background: #b91c1c;
            color: white;
            padding: 16px 20px;
            margin: -30px -35px 25px -35px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .report-header h1 { font-size: 18px; margin: 0; }
        .report-header .date { font-size: 12px; opacity: 0.85; }

        /* Warning box */
        .warning-box {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 20px;
            color: #991b1b;
            font-weight: 600;
            font-size: 13px;
        }

        /* All clear box */
        .clear-box {
            background: white;
            border: 1px solid #c0392b;
            border-radius: 6px;
            padding: 20px;
            text-align: center;
            color: #065f46;
            font-size: 15px;
            font-weight: 600;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        thead th {
            background: #b91c1c;
            color: white;
            padding: 8px 10px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }

        tbody td {
            padding: 7px 10px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }

        tbody tr:nth-child(even) { background: #fff5f5; }

        /* Days overdue in red bold */
        .days-late {
            color: #b91c1c;
            font-weight: 700;
        }

        .print-btn {
            background: #b91c1c;
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
            background: #6b7280;
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

        @media print {
            .no-print { display: none !important; }
            body { background: white; padding: 0; }
            .report-page { box-shadow: none; padding: 0; max-width: 100%; }
            .report-header { margin: 0 0 20px 0; }
        }
    </style>
</head>
<body>


<div class="no-print" style="max-width:1050px; margin:0 auto 12px auto;">
    <a href="../admin/transactions.php" class="back-btn">Back</a>
    <button class="print-btn" onclick="window.print()">
         Save as PDF
    </button>
</div>


<div class="report-page">

    <div class="report-header">
        <h1>Overdue Projectors Report</h1>
        <div class="date">
            
            Projector Issuance Information System
        </div>
    </div>

    <?php if (count($rows) == 0): ?>

       >
        <div class="clear-box">
            No overdue projectors at this time!<br>
            <span style="font-size:13px; font-weight:400; margin-top:6px; display:block;">
                All issued projectors are within their return deadline.
            </span>
        </div>

    <?php else: ?>

        
        <div class="warning-box">
            <?php echo count($rows); ?> projector(s) are overdue and require immediate follow-up.
            Please contact the borrowers listed below.
        </div>

    
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Serial No.</th>
                    <th>Projector</th>
                    <th>Borrower</th>
                    <th>Department</th>
                    <th>Contact</th>
                    <th>Date Issued</th>
                    <th>Due Date</th>
                    <th>Days Late</th>
                    <th>Issued By</th>
                </tr>
            </thead>
            <tbody>

            <?php foreach ($rows as $t): ?>
                <tr>
                    <td><?php echo $t['id']; ?></td>
                    <td><?php echo $t['serial_number']; ?></td>
                    <td><?php echo $t['brand'] . ' ' . $t['projector_model']; ?></td>
                    <td><?php echo $t['borrower_name']; ?></td>
                    <td><?php echo $t['department']; ?></td>
                    <td>
                        <?php echo $t['contact']; ?><br>
                        <small style="color:#666;"><?php echo $t['email']; ?></small>
                    </td>
                    <td><?php echo $t['date_issued']; ?></td>
                    <td><?php echo $t['expected_return']; ?></td>
                    <td class="days-late"><?php echo $t['days_overdue']; ?> days</td>
                    <td><?php echo $t['issued_by']; ?></td>
                </tr>
            <?php endforeach; ?>

            </tbody>
        </table>

        <!-- Action note -->
        <div style="margin-top:18px; padding:12px 15px; background:#fff3cd; border:1px solid #fcd34d; border-radius:6px; font-size:12px; color:#7c5800;">
            <strong>Action Required:</strong> Contact the borrowers above to arrange immediate return.
            If a borrower does not respond, escalate to their department head.
        </div>

    <?php endif; ?>

    <!-- Footer -->
    <div style="margin-top:20px; padding-top:12px; border-top:1px solid #ddd; font-size:11px; color:#999; text-align:center;">
        MUST Projector Issuance Information System &nbsp;|&nbsp;
        Overdue Count: <?php echo count($rows); ?> &nbsp;|&nbsp;
        Printed: <?php echo date('d M Y H:i'); ?>
    </div>

</div>

</body>
</html>
