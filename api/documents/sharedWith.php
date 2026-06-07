<?php
header("Access-Control-Allow-Origin: https://google-docs-pearl-ten.vercel.app");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit(0); }

include "../config/database.php";

$document_id = isset($_GET['document_id']) ? intval($_GET['document_id']) : 0;

if (!$document_id) {
    http_response_code(400);
    echo json_encode(["error" => "document_id is required"]);
    exit;
}

$db   = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("
    SELECT u.id, u.name, u.email
    FROM document_shares ds
    JOIN users u ON u.id = ds.user_id
    WHERE ds.document_id = :doc_id
");
$stmt->bindParam(":doc_id", $document_id, PDO::PARAM_INT);
$stmt->execute();
$shared = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["shared_with" => $shared]);
?>
