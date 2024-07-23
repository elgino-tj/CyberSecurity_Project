<?php
include 'db.php';
include 'header.php';

$url = $_GET['url'];

// Fetch scan result from the database
$sql = "SELECT * FROM scans WHERE url='$url' ORDER BY created_at DESC LIMIT 1";
$result = $conn->query($sql);
$scan = $result->fetch_assoc();

if ($scan) {
    $results = json_decode($scan['results'], true);
} else {
    $results = null;
}

$vulnerabilities = [
    'SQL Injection' => 'SQL injection allows attackers to execute arbitrary SQL code on a database, typically through a vulnerable input field.',
    'XSS' => 'Cross-site scripting (XSS) allows attackers to inject malicious scripts into web pages viewed by other users.',
    'Security Headers' => 'Security headers provide protection against common vulnerabilities by enforcing security policies on the client side.'
];
?>

<h2><?php echo htmlspecialchars($url); ?></h2>

<?php if ($results !== null && is_array($results)): ?>
    <?php foreach ($results as $vulnerability => $details): ?>
        <h3><?php echo $vulnerability; ?></h3>
        <p><?php echo $vulnerabilities[$vulnerability]; ?></p>
        <p>Status: <?php echo !empty($details) ? 'Vulnerability detected' : 'No vulnerability detected'; ?></p>
        <?php if (!empty($details)): ?>
            <ul>
                <?php foreach ($details as $detail): ?>
                    <li><?php echo htmlspecialchars($detail); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php endforeach; ?>

    <h3>Risk Scale</h3>
<p>
    The risk scale is based on the presence of vulnerabilities:
    <ul>
        <li><strong>Low:</strong> No or very few minor vulnerabilities detected.</li>
        <li><strong>Medium:</strong> Some vulnerabilities detected.</li>
        <li><strong>High:</strong> Multiple or critical vulnerabilities detected.</li>
    </ul>
</p>
<?php
// Sample data structure for $results array
$results = [
    ['type' => 'SQL Injection'],
    ['type' => 'Missing X-XSS-Protection'],
    ['type' => 'XSS'],
    // Add more risks here
];

// Initialize risk weight
$riskWeight = 0;

// Define weight values for each risk type
$riskTypes = [
    'SQL Injection' => 3,
    'XSS' => 2,
    'Missing X-XSS-Protection' => 1,
    'Missing Referrer-Policy' => 1,
    'Missing X-Frame-Options' => 1,
    'Missing Content-Security-Policy' => 1,
    'Missing Strict-Transport-Security' => 1,
    'Missing X-Content-Type-Options' => 1,
];

// Calculate total risk weight
foreach ($results as $risk) {
    if (isset($riskTypes[$risk['type']])) {
        $riskWeight += $riskTypes[$risk['type']];
    }
}

// Determine overall risk
if (empty($results)) {
    $overallRisk = 'No Risk';
} elseif ($riskWeight <= 2) {
    $overallRisk = 'Low';
} elseif ($riskWeight <= 5) {
    $overallRisk = 'Medium';
} else {
    $overallRisk = 'High';
}
?>
<p>Overall Risk: <?php echo $overallRisk; ?></p>



<!--  
<p>Overall Risk: 
        <?php
        if (empty($results)) {
            echo 'No Risk';
        } elseif (count($results) == 1) {
            echo 'Low';
        } elseif (count($results) == 2) {
            echo 'Medium';
        } else {
            echo 'High';
        }
        ?>
    </p>
-->
<?php else: ?>
    <p>No scan results found for this URL.</p>
<?php endif; ?>

<button onclick="window.location.href='index.php';" class="btn btn-primary">Back to Index</button>

<?php include 'footer.php'; ?>
