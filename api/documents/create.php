<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

include "../config/database.php";

$data = json_decode(file_get_contents("php://input"));

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("INSERT INTO documents (title, content, owner_id) VALUES (:title, :content, :owner_id)");

$stmt->bindParam(":title", $data->title);
$stmt->bindParam(":content", $data->content);
$stmt->bindParam(":owner_id", $data->owner_id);

$stmt->execute();

echo json_encode([
    "id" => $conn->lastInsertId(),
    "message" => "Document created"
]);
?>