<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

include "../config/database.php";

$data = json_decode(file_get_contents("php://input"));

if (!$data || !isset($data->id)) {
    echo json_encode(["error" => "Invalid request"]);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$query = "DELETE FROM documents WHERE id = :id";
$stmt = $conn->prepare($query);

$stmt->bindParam(":id", $data->id);

$stmt->execute();

echo json_encode([
    "message" => "Document deleted successfully"
]);
?>