<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Credentials: true");

require __DIR__ . '/classes/JwtHandler.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET' &&$_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'PUT' ) :
    http_response_code(405);
    echo json_encode([
        'success' => 0,
        'message' => 'Invalid Request Method. HTTP method should be GET',
    ]);
    exit;
endif;

if($_SERVER['REQUEST_METHOD'] == 'GET') :
  header("Access-Control-Allow-Methods: GET");    
  header("Content-Type: application/json; charset=UTF-8");
  
  
elseif($_SERVER['REQUEST_METHOD'] == 'POST') :
    header("Access-Control-Allow-Methods: POST");    
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
// require 'classes/Database.php';
// $database = new Database();
// $conn = $database->dbConnection();


class AuthZ extends JwtHandler
{
        protected $db;
        protected $headers;
        protected $token;

        public function __construct($db, $headers)
        {
            parent::__construct();
            $this->db = $db;
            $this->headers = $headers;
        }
        public function getUserIDInsert() { 
    if (array_key_exists('Authorization', $this->headers) && preg_match('/Bearer\s(\S+)/', $this->headers['Authorization'], $matches)) {

        $data = $this->jwtDecodeData($matches[1]);

        if (
            isset($data['data']->user_id) &&
            $user = $this->fetchPostsInsert($data['data']->user_id)
        ) :
            return [
                "success" => 1,
                "user" => $user
            ];
        else :
            return [
                "success" => 0,
                "message" => $data['message'],
            ];
        endif;
    } else {
        return [
            "success" => 0,
            "message" => "Token not found in request"
        ];
    }

}  
    protected function fetchPostsInsert($user_id)
    {

        $data = json_decode(file_get_contents("php://input"));
         if (!isset($data->title) || !isset($data->body) || !isset($data->author)) :

                echo json_encode([
                    'success' => 0,
                    'message' => 'Please fill all the fields | title, body, author.',
                ]);
                exit;

            elseif (empty(trim($data->title)) || empty(trim($data->body)) || empty(trim($data->author))) :

                echo json_encode([
                    'success' => 0,
                    'message' => 'Oops! empty field detected. Please fill all the fields.',
                ]);
                exit;

            endif;

            try {

                $title = htmlspecialchars(trim($data->title));
                $body = htmlspecialchars(trim($data->body));
                $author = htmlspecialchars(trim($data->author));

                $query = "INSERT INTO posts(title,body,author) VALUES(:title,:body,:author)";

                $stmt = $conn->prepare($query);

                $stmt->bindValue(':title', $title, PDO::PARAM_STR);
                $stmt->bindValue(':body', $body, PDO::PARAM_STR);
                $stmt->bindValue(':author', $author, PDO::PARAM_STR);

                if ($stmt->execute()) {

                    http_response_code(201);
                    echo json_encode([
                        'success' => 1,
                        'message' => 'Data Inserted Successfully.'
                    ]);
                    exit;
                }
                
                echo json_encode([
                    'success' => 0,
                    'message' => 'Data not Inserted.'
                ]);
                exit;

            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode([
                    'success' => 0,
                    'message' => $e->getMessage()
                ]);
                exit;
            }

    }
}
    
elseif($_SERVER['REQUEST_METHOD'] == 'PUT') :
    header("Access-Control-Allow-Methods: PUT");    
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  
    elseif($_SERVER['REQUEST_METHOD'] == 'DELETE') :
        header("Access-Control-Allow-Methods: DELETE");    
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    exit;
endif;


require __DIR__.'/classes/Database.php';
//require __DIR__.'/AuthMiddleware.php';

$allHeaders = getallheaders();
$db_connection = new Database();
$conn = $db_connection->dbConnection();
$authz = new Authz($conn, $allHeaders);

//echo json_encode($auth->getUserID());
echo json_encode($authZ->getUserIDInsert());
