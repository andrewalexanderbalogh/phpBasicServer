<?php
require_once __DIR__ . '/../vendor/autoload.php';

/**Load our .env settings using the phpdotenv library
 * https://packagist.org/packages/vlucas/phpdotenv
 */
$env = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$env->load();


/** Local Access logging with monolog library
 * https://packagist.org/packages/monolog/monolog
 */
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
$log = new Logger('logchannel');
$log->pushHandler(new StreamHandler(__DIR__ . '/../access.log', Logger::INFO));


$rawRequest = (object)parse_url($_SERVER['REQUEST_URI']);
$requestPath = $rawRequest->path;
$requestMethod = $_SERVER['REQUEST_METHOD'];
$log->info("$requestMethod $requestPath");

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


header('Access-Control-Allow-Origin: ' . ($_SERVER['HTTP_ORIGIN'] ?? '*'));
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, Content-Type, X-Portal-User, X-Portal-API-User, X-Portal-API-UUID');



/* Provide Response according to Request Method */
switch ($requestMethod){
case 'GET':
    /* GET Endpoints */
    if (!$requestPath || $requestPath === '/'){
        http_response_code(200);
        require __DIR__ . '/../views/welcome.php';
        break;
    }
    else if ($requestPath === '/departments') {
        # /departments
        $statement = $pdo->prepare("
            SELECT *
            FROM departments;
        ");

        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_OBJ);
        http_response_code(200);
        echo json_encode($results);
    }
    else if (preg_match('/\/employee\/(\d+)/', $requestPath, $matches)){
        # /employee/<EMP_NO>
        $emp_no = $matches[1];

        $statement = $pdo->prepare("
            SELECT *
            FROM employees
            WHERE employees.emp_no = '$emp_no';
        ");

        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_OBJ);
        http_response_code(200);
        echo json_encode($results);
    }
    else if (preg_match('/\/employees/', $requestPath)){
        # /employees?gender=<GENDER>&hire_date=<HIRE_DATE>
        $gender = $_GET['gender'] ?? '';
        $hire_date = $_GET['hire_date'] ?? '';

        $statement = $pdo->prepare("
            SELECT *
            FROM employees
            WHERE employees.gender = '$gender'
                AND employees.hire_date >= '$hire_date';
        ");

        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_OBJ);
        http_response_code(200);
        echo json_encode($results);
    }
    else {
        http_response_code(404);
        require __DIR__ . '/../views/404.php';
    }

    break;




case 'POST':
    /* POST Endpoints */
    if ($requestPath === '/employees') {
        # /employees
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
        catch (PDOException $e) {
            http_response_code(502);
            echo 'Could not perform query: ' . $e->getMessage();
        }

    }
    else {
        http_response_code(404);
        require __DIR__ . '/../views/404.php';
    }




    break;


case 'OPTIONS':
    /* Pre-Flight Requests typically */
    http_response_code(200);
    break;


default:
    http_response_code(501);
    echo 'Not Implemented';
    break;
}




