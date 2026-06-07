<?php
header("Access-Control-Allow-Origin: https://google-docs-pearl-ten.vercel.app");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit(0); }

include "../config/database.php";

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->document_id) || !isset($data->user_id)) {
    http_response_code(400);
    echo json_encode(["error" => "document_id and user_id are required"]);
    exit;
}

$db   = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("
    DELETE FROM document_shares
    WHERE document_id = :doc_id AND user_id = :user_id
");
$stmt->bindParam(":doc_id",  $data->document_id, PDO::PARAM_INT);
$stmt->bindParam(":user_id", $data->user_id,     PDO::PARAM_INT);
$stmt->execute();

echo json_encode(["message" => "Access removed"]);
?>
