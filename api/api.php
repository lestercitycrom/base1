<?php

require '../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Slim\Psr7\Response;
use Illuminate\Database\Capsule\Manager as Capsule;
use App\Controllers\OrderController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as ResponseInterface;

$app = AppFactory::create();

// Установка базового пути для всех маршрутов
$app->setBasePath('/api');

// Middleware для обработки JSON
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

// Error Middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Настройка Eloquent ORM
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'digital_shop',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Псевдо база данных пользователей
$users = [
    'user' => 'password'
];

// Middleware для проверки авторизации
$authMiddleware = function (Request $request, $handler) {
    $authHeader = $request->getHeader('Authorization')[0] ?? '';
    if ($authHeader !== 'Bearer valid_token') {
        $response = new Response();
        $response = $response->withStatus(401);
        $response->getBody()->write(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json');
    }
    return $handler->handle($request);
};

// Маршрут для аутентификации
$app->post('/login', function (Request $request, ResponseInterface $response, $args) use ($users) {
    $params = (array)$request->getParsedBody();
    $username = $params['username'] ?? '';
    $password = $params['password'] ?? '';

    if (isset($users[$username]) && $users[$username] === $password) {
        $token = bin2hex(random_bytes(16)); // Генерация токена
        $data = ['status' => 'ok', 'token' => $token];
        $response->getBody()->write(json_encode($data));
    } else {
        $data = ['status' => 'error', 'message' => 'Invalid credentials'];
        $response = $response->withStatus(401);
        $response->getBody()->write(json_encode($data));
    }
    
    return $response->withHeader('Content-Type', 'application/json');
});

// Пример открытого маршрута
$app->get('/public-data', function (Request $request, ResponseInterface $response, $args) {
    $data = ['status' => 'ok', 'message' => 'This is public data'];
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json');
});

// Пример защищенных маршрутов с использованием Middleware
$app->get('/user-data', function (Request $request, ResponseInterface $response, $args) {
    $data = ['status' => 'ok', 'message' => 'This is protected user data'];
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json');
})->add($authMiddleware);

$app->get('/another-protected-route', function (Request $request, ResponseInterface $response, $args) {
    $data = ['status' => 'ok', 'message' => 'This is another protected data'];
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json');
})->add($authMiddleware);

// Группа маршрутов для orders
$app->group('/orders', function ($group) {
    $group->get('', [OrderController::class, 'index']);
    $group->get('/{id}', [OrderController::class, 'show']);
    $group->post('', [OrderController::class, 'store']);
    $group->put('/{id}', [OrderController::class, 'update']);
    $group->delete('/{id}', [OrderController::class, 'delete']);
});

// Обработка всех остальных маршрутов (404 Not Found)
$app->map(['GET', 'POST', 'PUT', 'DELETE'], '/{routes:.+}', function (Request $request, ResponseInterface $response) {
    $data = ['status' => 'error', 'message' => 'Endpoint not found'];
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
});

// Запуск приложения
$app->run();