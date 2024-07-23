<?php
require 'vendor/autoload.php'; // Include your autoload file if needed
include 'db.php'; // Include your database connection file
include 'header.php'; // Include your header file

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

function performScan($url) {
    $results = [];

    // Example: SQL Injection check (replace with your logic)
    $sqlInjectionResult = checkSQLInjection($url);
    if ($sqlInjectionResult) {
        $results['SQL Injection'] = $sqlInjectionResult;
    }

    // Example: XSS check (replace with your logic)
    $xssResult = checkXSS($url);
    if ($xssResult) {
        $results['XSS'] = $xssResult;
    }

    // Example: Security Headers check (replace with your logic)
    $headersResult = checkSecurityHeaders($url);
    if ($headersResult) {
        $results['Security Headers'] = $headersResult;
    }

    return json_encode($results); // Encode results as JSON
}

function fetchURL($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function checkSQLInjection($url) {
    $payloads = [
        "' OR '1'='1",
        "' OR '1'='1' -- ",
        "1 OR 1=1",
        "' OR 'x'='x"
    ];

    $result = [];
    foreach ($payloads as $payload) {
        $response = fetchURL($url . "?input=" . urlencode($payload));
        if (strpos($response, 'error message') !== false) {
            $result[] = "Potential SQL Injection detected with payload: $payload";
        }
    }

    return $result;
}

function checkXSS($url) {
    $payloads = [
        "<script>alert('XSS')</script>",
        "<img src=x onerror=alert('XSS')>",
        "<svg/onload=alert('XSS')>"
    ];

    $result = [];
    foreach ($payloads as $payload) {
        $response = fetchURL($url . "?input=" . urlencode($payload));
        if (strpos($response, htmlentities($payload)) !== false) {
            $result[] = "Potential XSS detected with payload: $payload";
        }
    }

    return $result;
}

function checkSecurityHeaders($url) {
    // Ensure the URL starts with http:// or https://
    if (!preg_match('/^https?:\/\//', $url)) {
        $url = 'http://' . $url;
    }

    $response = @get_headers($url, 1); // Suppress warnings and handle errors manually
    if ($response === false) {
        return ["Error fetching headers from $url"];
    }

    // Normalize headers to lower case
    $normalizedHeaders = array_change_key_case($response, CASE_LOWER);

    $securityHeaders = [
        'x-frame-options',
        'content-security-policy',
        'strict-transport-security',
        'x-xss-protection',
        'x-content-type-options',
        'referrer-policy'
    ];

    $result = [];
    foreach ($securityHeaders as $header) {
        if (isset($normalizedHeaders[$header])) {
            $result[] = "Security header $header present";
        } else {
            $result[] = "Missing security header $header";
        }
    }

    return $result;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $url = $_POST['url'];
    $user = $_SESSION['username'];

    // Example: Validate URL (replace with your validation logic)
    if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
        header("Location: scan_result.php?status=invalid");
        exit();
    }

    // Example: Simulate user ID retrieval (replace with your logic)
    $user_id_sql = "SELECT id FROM users WHERE username='$user'";
    $user_id_result = $conn->query($user_id_sql);
    $user_id = $user_id_result->fetch_assoc()['id'];

    // Example: Perform scan and store results
    $result = performScan($url);
    $status = empty(json_decode($result, true)) ? "No vulnerabilities found" : "Vulnerabilities found";

    $sql = "INSERT INTO scans (url, status, user_id, results) VALUES ('$url', '$status', '$user_id', '$result')";

    if ($conn->query($sql) === TRUE) {
        header("Location: scan_result.php?status=success&url=" . urlencode($url));
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<h2>New Scan</h2>
<form method="POST" action="">
    <div class="form-group">
        <label for="url">URL to scan</label>
        <input type="text" class="form-control" id="url" name="url" required>
    </div>
    <button type="submit" class="btn btn-primary">Start Scan</button>
</form>

<?php include 'footer.php'; // Include your footer file ?>
