<?php
include 'db.php';
include 'header.php';

function getScanHistoriesByKeyword($conn, $keyword) {
    $sql = "SELECT url, status, scans.created_at, results 
            FROM scans 
            JOIN users ON scans.user_id = users.id 
            WHERE url LIKE ? OR status LIKE ? OR results LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = '%' . $keyword . '%';
    $stmt->bind_param('sss', $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $scanHistories = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $scanHistories[] = $row;
        }
    }

    return $scanHistories;
}

$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$scanHistories = getScanHistoriesByKeyword($conn, $keyword);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Public Scan Results</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Public Scan Results</h2>
        <!-- Search Form -->
        <form method="get" action="">
            <div class="form-group">
                <input type="text" name="keyword" class="form-control" placeholder="Search by keyword" value="<?php echo htmlspecialchars($keyword); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>URL</th>
                    <th>Status</th>
                    <th>Date Posted</th>
                    <th>Full Report</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($scanHistories) > 0) { ?>
                    <?php foreach ($scanHistories as $history) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($history['url']); ?></td>
                            <td><?php echo htmlspecialchars($history['status']); ?></td>
                            <td><?php echo htmlspecialchars($history['created_at']); ?></td>
                            <td>
                                <a href="full_report.php?url=<?php echo urlencode($history['url']); ?>" class="btn btn-info">View Full Report</a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="4">No results found</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <!-- Back Button -->
        <form action="index.php" method="get">
            <button type="submit" class="btn btn-secondary">Back</button>
        </form>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php include 'footer.php'; ?>
