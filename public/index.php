<?php
require_once __DIR__ . '/../vendor/autoload.php';

/**Load our .env settings using the phpdotenv library
 * https://packagist.org/packages/vlucas/phpdotenv
 */
$env = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$env->load();


$requestPath = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];
/* Parse out and process POST data (JSON format expected) */
$rawJSON = file_get_contents('php://input');
$jsonData = json_decode($rawJSON, false);



/* Make initial Connection to the Database-Server */
try {
    $dbHost = $_ENV['DB_HOST'];
    $dbPort = $_ENV['DB_PORT'];
    $dbName = $_ENV['DB_NAME'];
    $dbUser = $_ENV['DB_USERNAME'];
    $dbPass = $_ENV['DB_PASSWORD'];
    $pdo = new PDO("mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   // We will make SQL errors throw exceptions
}
catch (PDOException $e){
    http_response_code(502);
    echo 'Database could not be reached';
    die();
}


/* Provide Response according to Request Method */
switch ($requestMethod){
case 'GET':
    /* GET Endpoints */
    switch ($requestPath){
    case '/departments':
        $statement = $pdo->prepare("
            SELECT *
            FROM departments;
        ");

        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_OBJ);
        http_response_code(200);
        echo json_encode($results);
        break;



    default:
        http_response_code(404);
        echo 'Not Found';
            break;
    }



    break;


case 'POST':
    /* POST Endpoints */
    switch ($requestPath){
    case '/employees':
        try {
            $pdo->exec("
                INSERT INTO employees (
                    emp_no, 
                    birth_date, 
                    first_name, 
                    last_name, 
                    gender, 
                    hire_date
                )
             
                VALUES (
                    '$jsonData->emp_no', 
                    '$jsonData->birth_date', 
                    '$jsonData->first_name', 
                    '$jsonData->last_name', 
                    '$jsonData->gender', 
                    '$jsonData->hire_date'       
                )
            
            ");

            http_response_code(200);
            echo 'Inserted new Employee record';
        }
        catch (PDOException $e){
            http_response_code(502);
            echo 'Could not perform query: ' . $e->getMessage();
        }

        break;



    default:
        http_response_code(404);
        echo 'Not Found';
        break;
    }
    break;


default:
    http_response_code(501);
    echo 'Not Implemented';
    break;
}




