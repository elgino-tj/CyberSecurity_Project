<?php
include 'db.php';
include 'header.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['username'];
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';

// Fetch scan history for the logged-in user with keyword filtering
$sql = "SELECT * FROM scans WHERE user_id=(SELECT id FROM users WHERE username=?) AND (url LIKE ? OR status LIKE ?)";
$stmt = $conn->prepare($sql);
$searchTerm = '%' . $keyword . '%';
$stmt->bind_param('sss', $user, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Scan History</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Scan History</h2>
        <!-- Search Form -->
        <form method="get" action="">
            <div class="form-group">
                <input type="text" name="keyword" class="form-control" placeholder="Search by keyword" value="<?php echo htmlspecialchars($keyword); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>URL</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Full Report</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['url']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td>
                                <a href="full_report.php?url=<?php echo urlencode($row['url']); ?>" class="btn btn-info">View Full Report</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No results found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <button onclick="window.location.href='index.php';" class="btn btn-primary">Back to Index</button>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php include 'footer.php'; ?>
