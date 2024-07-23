<?php
include 'header.php';
$status = $_GET['status'];
$url = isset($_GET['url']) ? $_GET['url'] : '';
?>

<h2>Scan Result</h2>

<?php if ($status == 'invalid'): ?>
    <div class="alert alert-danger">
        <strong>X</strong> The URL is not valid or accessible.
    </div>
    <button onclick="window.location.href='scan.php';" class="btn btn-primary">Go Back</button>
<?php elseif ($status == 'success'): ?>
    <div class="alert alert-success">
        Scan for URL: <strong><?php echo htmlspecialchars($url); ?></strong> succeeded.
    </div>
    <button onclick="window.location.href='scan_history.php';" class="btn btn-secondary">View Scan History</button>
    <button onclick="window.location.href='index.php';" class="btn btn-primary">Back to Home</button>
    <button onclick="window.location.href='full_report.php?url=<?php echo urlencode($url); ?>';" class="btn btn-info">View Full Report</button>
<?php endif; ?>

<?php include 'footer.php'; ?>
