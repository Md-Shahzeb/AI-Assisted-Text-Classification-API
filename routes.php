
<?php

require_once __DIR__ . '/controllers.php';

$requestMethod = $_SERVER['REQUEST_METHOD'];

// Read endpoint from query parameter
$endpoint = $_GET['route'] ?? '';

$controller = new ClassificationController();

if ($requestMethod === 'POST' && $endpoint === '/api/classify') {

    $controller->classify();

} else {

    header("Content-Type: application/json");
    http_response_code(404);

    echo json_encode([
        'success' => false,
        'message' => 'API endpoint not found'
    ]);
}
