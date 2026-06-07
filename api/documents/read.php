<?php
header("Access-Control-Allow-Origin: https://google-docs-pearl-ten.vercel.app");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

include "../config/database.php";

$db = new Database();
$conn = $db->getConnection();

$id = $_GET['id'];

$query = "SELECT * FROM documents WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(":id", $id);
$stmt->execute();

$doc = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    "data" => $doc
]);
?>