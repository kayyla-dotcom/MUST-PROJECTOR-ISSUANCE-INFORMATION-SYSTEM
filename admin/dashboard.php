<?php

$conn = mysqli_connect('localhost', 'root','' , 'projector_system');
require_once '../includes/auth.php';
requireAdmin();
require_once '../db.php';

$pageTitle = "Admin Dashboard";


/* how many projectors and transactions we have */

/* Total  */
$Projector_stat = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_projectors");
$row = mysqli_fetch_assoc($Projector_stat);
$totalProjectors = $row['total'];

/* Available projectors */
$Projector_stat = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_projectors WHERE status = 'available'");
$row = mysqli_fetch_assoc($Projector_stat);
$totalAvailable = $row['total'];

/* Issued projectors */
$Projector_stat = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_projectors WHERE status = 'issued'");
$row = mysqli_fetch_assoc($Projector_stat);
$totalIssued = $row['total'];

/* Projectors in maintenance */
$Projector_stat = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_projectors WHERE status = 'maintenance'");
$row = mysqli_fetch_assoc($Projector_stat);
$totalMaintenance = $row['total'];

/* Total transactions */
$Projector_stat = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_transactions");
$row = mysqli_fetch_assoc($Projector_stat);
$totalTransactions = $row['total'];

/* Overdue transactions */
$Projector_stat = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_transactions WHERE status = 'overdue'");
$row = mysqli_fetch_assoc($Projector_stat);
$totalOverdue = $row['total'];


/* Which projector has been borrowed the most times? */
$Projector_stat = mysqli_query($conn,
    "SELECT p.serial_number, p.brand, p.model, COUNT(t.id) AS times_borrowed
     FROM tbl_projectors p
     LEFT JOIN tbl_transactions t ON p.id = t.projector_id
     GROUP BY p.id
     ORDER BY times_borrowed DESC
     LIMIT 1"
);
$mostUsed = mysqli_fetch_assoc($Projector_stat);


/* Which projector has been borrowed the fewest times? */
$Projector_stat = mysqli_query($conn,
    "SELECT p.serial_number, p.brand, p.model, COUNT(t.id) AS times_borrowed
     FROM tbl_projectors p
     LEFT JOIN tbl_transactions t ON p.id = t.projector_id
     GROUP BY p.id
     ORDER BY times_borrowed ASC
     LIMIT 1"
);
$leastUsed = mysqli_fetch_assoc($Projector_stat);


/* Which person has borrowed a projector the most times? */
$Projector_stat = mysqli_query($conn,
    "SELECT b.full_name, b.department, COUNT(t.id) AS times_borrowed
     FROM tbl_borrowers b
     LEFT JOIN tbl_transactions t ON b.id = t.borrower_id
     GROUP BY b.id
     ORDER BY times_borrowed DESC
     LIMIT 1"
);
$topBorrower = mysqli_fetch_assoc($Projector_stat);


/*
   Show all projectors and how many times each was borrowed */
$usageResult = mysqli_query($conn,
    "SELECT p.serial_number, p.brand, p.model, p.status, COUNT(t.id) AS times_borrowed
     FROM tbl_projectors p
     LEFT JOIN tbl_transactions t ON p.id = t.projector_id
     GROUP BY p.id
     ORDER BY times_borrowed DESC"
);


/* PART 3 - RECENT TRANSACTIONS */
$recentResult = mysqli_query($conn,
    "SELECT
        t.id,
        t.date_issued,
        t.expected_return,
        t.status,
        p.model         AS projector_model,
        p.serial_number AS serial_number,
        b.full_name     AS borrower_name,
        u.username      AS issued_by
     FROM tbl_transactions t
     JOIN tbl_projectors p ON t.projector_id = p.id
     JOIN tbl_borrowers  b ON t.borrower_id  = b.id
     JOIN tbl_users      u ON t.issued_by    = u.id
     ORDER BY t.created_at DESC
     LIMIT 10"
);


require_once '../includes/header.php';
?>



<div class="stats-row">

    <div class="stat-card">
        <div>
            <div class="stat-label">Total Projectors</div>
            <div class="stat-number"><?php echo $totalProjectors; ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-label">Available</div>
            <div class="stat-number"><?php echo $totalAvailable; ?></div>
        </div>
    </div>

    <div class="stat-card orange">
        <div>
            <div class="stat-label">Issued Out</div>
            <div class="stat-number"><?php echo $totalIssued; ?></div>
        </div>
    </div>

    <div class="stat-card red">
        <div>
            <div class="stat-label">Overdue</div>
            <div class="stat-number"><?php echo $totalOverdue; ?></div>
        </div>
    </div>

    <div class="stat-card purple">
        <div>
            <div class="stat-label">Maintenance</div>
            <div class="stat-number"><?php echo $totalMaintenance; ?></div>
        </div>
    </div>

    <div class="stat-card blue">
        <div>
            <div class="stat-label">Total Transactions</div>
            <div class="stat-number"><?php echo $totalTransactions; ?></div>
        </div>
    </div>

</div>


<!-- QUICK BUTTONS -->
<div class="action-row">
    <a href="projectors.php"                  class="btn btn-green">Add Projector</a>
    <a href="borrowers.php"                   class="btn btn-green">Add Borrower</a>
    <a href="users.php"                       class="btn btn-grey" >Add Staff</a>
    <a href="../reports/all_transactions.php" class="btn btn-gold" >PDF Report</a>
</div>


<!-- PART 2 - PROJECTOR STATISTICS-->

<h3 style="color:#c0392b; margin-bottom:14px;">Projector Statistics</h3>

<div class="stats-row" style="margin-bottom:25px;">

    <!-- Most Used Projector -->
    <div class="stat-info-box">
        <div class="stat-info-label">Most Used Projector</div>
        <div class="stat-info-value">
            <?php echo $mostUsed['brand']; ?> <?php echo $mostUsed['model']; ?>
        </div>
        <div class="stat-info-sub">
            Serial: <?php echo $mostUsed['serial_number']; ?>
        </div>
        <div class="stat-info-count">
            Borrowed <?php echo $mostUsed['times_borrowed']; ?> times
        </div>
    </div>

    <!-- Least Used Projector -->
    <div class="stat-info-box">
        <div class="stat-info-label">Least Used Projector</div>
        <div class="stat-info-value">
            <?php echo $leastUsed['brand']; ?> <?php echo $leastUsed['model']; ?>
        </div>
        <div class="stat-info-sub">
            Serial: <?php echo $leastUsed['serial_number']; ?>
        </div>
        <div class="stat-info-count">
            Borrowed <?php echo $leastUsed['times_borrowed']; ?> times
        </div>
    </div>

    <!-- Top Borrower -->
    <div class="stat-info-box">
        <div class="stat-info-label">Top Borrower</div>
        <div class="stat-info-value">
            <?php echo $topBorrower['full_name']; ?>
        </div>
        <div class="stat-info-sub">
            <?php echo $topBorrower['department']; ?>
        </div>
        <div class="stat-info-count">
            Borrowed <?php echo $topBorrower['times_borrowed']; ?> times
        </div>
    </div>

</div>


<!-- PROJECTOR USAGE TABLE -->
<div class="card" style="margin-bottom:25px;">
    <div class="card-header">
        <h3>How Many Times Each Projector Was Borrowed</h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Serial Number</th>
                <th>Brand &amp; Model</th>
                <th>Current Status</th>
                <th>Times Borrowed</th>
            </tr>
        </thead>
        <tbody>

        <?php
        $number = 1; // row number starting at 1
        while ($p = mysqli_fetch_assoc($usageResult)):
        ?>
            <tr>
                <td><?php echo $number; ?></td>
                <td><strong><?php echo $p['serial_number']; ?></strong></td>
                <td><?php echo $p['brand']; ?> <?php echo $p['model']; ?></td>
                <td>
                    <span class="badge badge-<?php echo $p['status']; ?>">
                        <?php echo $p['status']; ?>
                    </span>
                </td>
                <td>
                    <!-- Show the count in green if > 0, grey if never borrowed -->
                    <?php if ($p['times_borrowed'] > 0): ?>
                        <strong style="color:#1a7a4a;">
                            <?php echo $p['times_borrowed']; ?> times
                        </strong>
                    <?php else: ?>
                        <span class="text-grey">Never borrowed</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php
            $number++;
        endwhile;
        ?>

        </tbody>
    </table>
</div>


<!--PART 3 - RECENT TRANSACTIONS TABLE -->
<div class="card">
    <div class="card-header">
        <h3>Recent Transactions</h3>
        <a href="transactions.php" class="btn btn-grey btn-small">View All</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Projector</th>
                <th>Borrower</th>
                <th>Issued By</th>
                <th>Date Issued</th>
                <th>Expected Return</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>

        <?php if (mysqli_num_rows($recentResult) == 0): ?>
            <tr>
                <td colspan="7" class="text-center text-grey" style="padding:20px;">
                    No transactions yet.
                </td>
            </tr>
        <?php else: ?>

            <?php while ($tx = mysqli_fetch_assoc($recentResult)): ?>
            <tr>
                <td><?php echo $tx['id']; ?></td>
                <td>
                    <?php echo $tx['projector_model']; ?><br>
                    <small class="text-grey"><?php echo $tx['serial_number']; ?></small>
                </td>
                <td><?php echo $tx['borrower_name']; ?></td>
                <td><?php echo $tx['issued_by']; ?></td>
                <td><?php echo $tx['date_issued']; ?></td>
                <td><?php echo $tx['expected_return']; ?></td>
                <td>
                    <span class="badge badge-<?php echo $tx['status']; ?>">
                        <?php echo $tx['status']; ?>
                    </span>
                </td>
            </tr>
            <?php endwhile; ?>

        <?php endif; ?>

        </tbody>
    </table>
</div>



<?php require_once '../includes/footer.php'; ?>
