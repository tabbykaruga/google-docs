<?php
header("Access-Control-Allow-Origin: https://google-docs-pearl-ten.vercel.app");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
include "../config/database.php";

$data = json_decode(file_get_contents("php://input"));

$db = new Database();
$conn = $db->getConnection();

$query = "UPDATE documents 
          SET title = :title, content = :content 
          WHERE id = :id";

$stmt = $conn->prepare($query);

$stmt->bindParam(":title", $data->title);
$stmt->bindParam(":content", $data->content);
$stmt->bindParam(":id", $data->id);

$stmt->execute();

echo json_encode(["message" => "Document updated"]);
?>