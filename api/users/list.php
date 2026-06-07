<?php
header("Access-Control-Allow-Origin: https://google-docs-pearl-ten.vercel.app");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit(0); }

include "../config/database.php";

$db   = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT id, name, email FROM users ORDER BY id ASC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["users" => $users]);
?>
