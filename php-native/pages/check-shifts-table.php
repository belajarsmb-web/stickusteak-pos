<?php
require_once __DIR__ . '/../config/database.php';

$pdo = getDbConnection();

echo "<h2>Shifts Table Structure</h2>";

$stmt = $pdo->query("DESCRIBE shifts");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
foreach ($columns as $col) {
    echo "<tr>";
    echo "<td>{$col['Field']}</td>";
    echo "<td>{$col['Type']}</td>";
    echo "<td>{$col['Null']}</td>";
    echo "<td>{$col['Key']}</td>";
    echo "<td>" . ($col['Default'] ?? 'NULL') . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h3>Sample Data</h3>";
$stmt = $pdo->query("SELECT * FROM shifts LIMIT 5");
$shifts = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($shifts) > 0) {
    echo "<pre>";
    print_r($shifts[0]);
    echo "</pre>";
} else {
    echo "<p>No shifts found</p>";
}
?>
