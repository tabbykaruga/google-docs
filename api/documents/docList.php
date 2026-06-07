<?php
header("Access-Control-Allow-Origin: https://google-docs-pearl-ten.vercel.app");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

include "../config/database.php";

$data = json_decode(file_get_contents("php://input"));

$user_id = $data->user_id;

$db = new Database();
$conn = $db->getConnection();

// Owned documents
$owned = $conn->prepare("SELECT * FROM documents WHERE owner_id = :user_id");
$owned->bindParam(":user_id", $user_id);
$owned->execute();
$ownedDocs = $owned->fetchAll(PDO::FETCH_ASSOC);

// Shared documents
$shared = $conn->prepare("
    SELECT d.* 
    FROM documents d
    JOIN document_shares ds ON d.id = ds.document_id
    WHERE ds.user_id = :user_id
");
$shared->bindParam(":user_id", $user_id);
$shared->execute();
$sharedDocs = $shared->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "owned" => $ownedDocs,
    "shared" => $sharedDocs
]);
?>