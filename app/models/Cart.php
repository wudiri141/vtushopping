<?php

class Cart
{
    public static function getFromSession(): array
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        return $_SESSION['cart'];
    }

    public static function addToCart(int $productId, string $productName, float $price, int $quantity = 1): void
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $productId = (int) $productId;
        
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = [
                'id' => $productId,
                'name' => $productName,
                'price' => $price,
                'quantity' => max(1, $quantity),
            ];
        }
    }

    public static function updateQuantity(int $productId, int $quantity): void
    {
        if (!isset($_SESSION['cart'][$productId])) {
            return;
        }

        if ($quantity <= 0) {
            self::removeFromCart($productId);
        } else {
            $_SESSION['cart'][$productId]['quantity'] = max(1, min(99, $quantity));
        }
    }

    public static function removeFromCart(int $productId): void
    {
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }
    }

    public static function clear(): void
    {
        $_SESSION['cart'] = [];
    }

    public static function getCount(): int
    {
        $count = 0;
        foreach (self::getFromSession() as $item) {
            $count += (int) ($item['quantity'] ?? 1);
        }
        return $count;
    }

    public static function getSubtotal(): float
    {
        $subtotal = 0.0;
        foreach (self::getFromSession() as $item) {
            $subtotal += (float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 1);
        }
        return $subtotal;
    }

    public static function getItemsWithDetails(): array
    {
        $items = [];
        foreach (self::getFromSession() as $productId => $item) {
            $product = Product::find($productId);
            if ($product) {
                $items[] = [
                    'id' => $productId,
                    'name' => $product['name'],
                    'short_name' => $product['short_name'] ?: $product['name'],
                    'price' => (float) $product['price'],
                    'quantity' => (int) ($item['quantity'] ?? 1),
                    'image' => $product['image'] ?? 'images/product-necklace.png',
                    'stock' => (int) ($product['stock'] ?? 0),
                    'discount_percent' => (int) ($product['discount_percent'] ?? 0),
                ];
            }
        }
        return $items;
    }
}
