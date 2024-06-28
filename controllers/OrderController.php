<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Order;

class OrderController {
    // Получить все заказы
    public function index(Request $request, Response $response, $args) {
        $orders = Order::all();
        $response->getBody()->write($orders->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Получить заказ по ID
    public function show(Request $request, Response $response, $args) {
        $order = Order::find($args['id']);
        if ($order) {
            $response->getBody()->write($order->toJson());
        } else {
            $response = $response->withStatus(404);
            $response->getBody()->write(json_encode(['error' => 'Order not found']));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Создать новый заказ
    public function store(Request $request, Response $response, $args) {
        $data = $request->getParsedBody();
        $order = Order::create($data);
        $response->getBody()->write($order->toJson());
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    // Обновить заказ
    public function update(Request $request, Response $response, $args) {
        $order = Order::find($args['id']);
        if ($order) {
            $data = $request->getParsedBody();
            $order->update($data);
            $response->getBody()->write($order->toJson());
        } else {
            $response = $response->withStatus(404);
            $response->getBody()->write(json_encode(['error' => 'Order not found']));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Удалить заказ
    public function delete(Request $request, Response $response, $args) {
        $order = Order::find($args['id']);
        if ($order) {
            $order->delete();
            $response->getBody()->write(json_encode(['message' => 'Order deleted']));
        } else {
            $response = $response->withStatus(404);
            $response->getBody()->write(json_encode(['error' => 'Order not found']));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}