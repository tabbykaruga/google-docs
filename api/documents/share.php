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

// Prevent duplicate shares
$check = $conn->prepare("
    SELECT id FROM document_shares
    WHERE document_id = :doc_id AND user_id = :user_id
");
$check->bindParam(":doc_id",   $data->document_id, PDO::PARAM_INT);
$check->bindParam(":user_id",  $data->user_id,     PDO::PARAM_INT);
$check->execute();

if ($check->fetch()) {
    echo json_encode(["message" => "Already shared"]);
    exit;
}

$stmt = $conn->prepare("
    INSERT INTO document_shares (document_id, user_id)
    VALUES (:doc_id, :user_id)
");
$stmt->bindParam(":doc_id",  $data->document_id, PDO::PARAM_INT);
$stmt->bindParam(":user_id", $data->user_id,     PDO::PARAM_INT);
$stmt->execute();

echo json_encode(["message" => "Document shared successfully"]);
?>
