<?php
$conn = mysqli_connect('localhost', 'root','' , 'projector_system');
require_once '../includes/auth.php';
requireAdmin();
require_once '../db.php';

/* GET ALL PROJECTORS */
$result = mysqli_query($conn,
    "SELECT * FROM tbl_projectors ORDER BY status, brand, model"
);

$rows = array();
$countAvailable = 0;
$countIssued    = 0;
$countMaint     = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
    if ($row['status'] == 'available')   $countAvailable++;
    if ($row['status'] == 'issued')      $countIssued++;
    if ($row['status'] == 'maintenance') $countMaint++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Projector Status Report</title>
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
            max-width: 800px;
            margin: 0 auto;
            padding: 30px 35px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .report-header {
            background: #c0392b;
            color: white;
            padding: 16px 20px;
            margin: -30px -35px 25px -35px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .report-header h1 { font-size: 18px; margin: 0; }
        .report-header .date { font-size: 12px; opacity: 0.85; }

        /* Summary boxes */
        .summary-row {
            display: flex;
            gap: 12px;
            margin-bottom: 22px;
        }

        .summary-box {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 14px 15px;
            text-align: center;
        }

        .summary-box .label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
        }

        .summary-box .value {
            font-size: 28px;
            font-weight: 700;
            margin-top: 4px;
        }

        .summary-box.green  { border-color: #6ee7b7; }
        .summary-box.orange { border-color: #fcd34d; }
        .summary-box.purple { border-color: #c4b5fd; }

        .summary-box.green .value  { color: #1a7a4a; }
        .summary-box.orange .value { color: #c05c00; }
        .summary-box.purple .value { color: #6d28d9; }

        /* Table */
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
        }

        tbody td {
            padding: 7px 10px;
            border-bottom: 1px solid #eee;
        }

        tbody tr:nth-child(even) { background: #f9f9f9; }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
        }

        .badge-available   { background: #d1fae5; color: #065f46; }
        .badge-issued      { background: #fef3c7; color: #92400e; }
        .badge-maintenance { background: #ede9fe; color: #5b21b6; }

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


<div class="no-print" style="max-width:800px; margin:0 auto 12px auto;">
    <a href="../admin/projectors.php" class="back-btn">← Back</a>
    <button class="print-btn" onclick="window.print()">
        Save as PDF
    </button>
</div>


<div class="report-page">


    <div class="report-header">
        <h1> Projector Status Report</h1>
        <div class="date">
        
            Projector Issuance Information System
        </div>
    </div>

    <!-- Summary boxes -->
    <div class="summary-row">
        <div class="summary-box green">
            <div class="label">Available</div>
            <div class="value"><?php echo $countAvailable; ?></div>
        </div>
        <div class="summary-box orange">
            <div class="label">Issued Out</div>
            <div class="value"><?php echo $countIssued; ?></div>
        </div>
        <div class="summary-box purple">
            <div class="label">Maintenance</div>
            <div class="value"><?php echo $countMaint; ?></div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Serial Number</th>
                <th>Brand</th>
                <th>Model</th>
                <th>Status</th>
                <th>Condition Notes</th>
            </tr>
        </thead>
        <tbody>

        <?php $number = 1; ?>
        <?php foreach ($rows as $p): ?>
            <tr>
                <td><?php echo $number++; ?></td>
                <td><strong><?php echo $p['serial_number']; ?></strong></td>
                <td><?php echo $p['brand']; ?></td>
                <td><?php echo $p['model']; ?></td>
                <td>
                    <span class="badge badge-<?php echo $p['status']; ?>">
                        <?php echo $p['status']; ?>
                    </span>
                </td>
                <td><?php echo $p['condition_note']; ?></td>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>

    <!-- Footer -->
    <div style="margin-top:20px; padding-top:12px; border-top:1px solid #ddd; font-size:11px; color:#999; text-align:center;">
        MUST Projector Issuance Information System &nbsp;|&nbsp;
        Total Projectors: <?php echo count($rows); ?> &nbsp;|&nbsp;
        Printed: <?php echo date('d M Y H:i'); ?>
    </div>

</div>

</body>
</html>
