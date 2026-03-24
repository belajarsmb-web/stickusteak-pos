<?php
/**
 * Test Shift APIs Directly
 */

echo "<h2>Testing Shift APIs</h2>";

$apis = [
    '/php-native/api/shifts/active.php',
    '/php-native/api/shifts/list.php',
    '/php-native/api/shifts/open.php'
];

foreach ($apis as $api) {
    echo "<h3>Testing: $api</h3>";
    
    $url = 'http://localhost' . $api;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    curl_close($ch);
    
    echo "<p><strong>HTTP Code:</strong> $httpCode</p>";
    
    if ($httpCode === 500) {
        echo "<p style='color: red;'><strong>Error Response:</strong></p>";
        echo "<pre style='background: #ffe0e0; padding: 10px; border-left: 3px solid red;'>$response</pre>";
    } else {
        echo "<p style='color: green;'><strong>Success!</strong></p>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
    
    echo "<hr>";
}
?>
<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    pre { background: #f0f0f0; padding: 10px; border-radius: 5px; overflow-x: auto; }
    h3 { margin-top: 30px; color: #333; }
</style>
