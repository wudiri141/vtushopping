<?php

class CartController
{
    public static function add(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['status' => false, 'message' => 'Invalid request method.'], 405);
        }

        $data = json_decode(file_get_contents('php://input') ?: '{}', true);
        $productId = (int) ($data['product_id'] ?? 0);
        $quantity = max(1, (int) ($data['quantity'] ?? 1));

        if ($productId <= 0) {
            json_response(['status' => false, 'message' => 'Invalid product.'], 422);
        }

        $product = Product::find($productId);
        if (!$product) {
            json_response(['status' => false, 'message' => 'Product not found.'], 404);
        }

        Cart::addToCart($productId, $product['name'], (float) $product['price'], $quantity);

        json_response([
            'status' => true,
            'message' => 'Added to cart',
            'cart_count' => Cart::getCount(),
        ]);
    }

    public static function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['status' => false, 'message' => 'Invalid request method.'], 405);
        }

        $data = json_decode(file_get_contents('php://input') ?: '{}', true);
        $productId = (int) ($data['product_id'] ?? 0);
        $quantity = (int) ($data['quantity'] ?? 1);

        if ($productId <= 0) {
            json_response(['status' => false, 'message' => 'Invalid product.'], 422);
        }

        Cart::updateQuantity($productId, $quantity);

        json_response([
            'status' => true,
            'message' => 'Cart updated',
            'cart_count' => Cart::getCount(),
        ]);
    }

    public static function remove(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['status' => false, 'message' => 'Invalid request method.'], 405);
        }

        $data = json_decode(file_get_contents('php://input') ?: '{}', true);
        $productId = (int) ($data['product_id'] ?? 0);

        if ($productId <= 0) {
            json_response(['status' => false, 'message' => 'Invalid product.'], 422);
        }

        Cart::removeFromCart($productId);

        json_response([
            'status' => true,
            'message' => 'Removed from cart',
            'cart_count' => Cart::getCount(),
        ]);
    }

    public static function clear(): void
    {
        Cart::clear();
        json_response([
            'status' => true,
            'message' => 'Cart cleared',
        ]);
    }

    public static function count(): void
    {
        json_response([
            'count' => Cart::getCount(),
        ]);
    }
}
