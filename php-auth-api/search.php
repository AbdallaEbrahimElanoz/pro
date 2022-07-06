<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] !== 'GET') :
    http_response_code(405);
    echo json_encode([
        'success' => 0,
        'message' => 'Invalid Request Method. HTTP method should be GET',
    ]);
    exit;
endif;

require 'classes/Database.php';
$database = new Database();
$conn = $database->dbConnection();
$name= null;
// $query = "SELECT * FROM posts WHERE title LIKE 'p%' ";
// $stmt = $conn->prepare($query);

// $stmt->execute();
// $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
// var_dump($data);
// exit;

if (isset($_GET['title'])) {
    $name= filter_var($_GET['title'], FILTER_SANITIZE_STRING, [
        'options' => [
            'default' => 'all_posts',
            'min_range' => 1
        ]
    ]);
}

try {

    $sql =is_string($name) ? "SELECT * FROM posts WHERE title LIKE '%" .$name. "%'" : "SELECT * FROM posts";

    $stmt = $conn->prepare($sql);

    $stmt->execute();
//     $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
// var_dump($data);
// exit;

  //error >0
    if ($stmt->rowCount()) :

        $data = null;
        if (is_string($name)) {
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode([
            'success' => 1,
            'data' => $data,
        ]);

    else :
        echo json_encode([
            'success' => 0,
            'message' => 'No Result Found!',
        ]);
    endif;
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => 0,
        'message' => $e->getMessage()
    ]);
    exit;
}