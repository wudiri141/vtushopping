<?php
declare(strict_types=1);

session_start();

$config = require __DIR__ . '/config/app.php';

if ($config['base_url'] === '') {
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $config['base_url'] = $scriptDir === '/' ? '' : $scriptDir;
}

require __DIR__ . '/app/core/Database.php';
require __DIR__ . '/app/models/Product.php';
require __DIR__ . '/app/models/Banner.php';
require __DIR__ . '/app/models/User.php';
require __DIR__ . '/app/models/Order.php';
require __DIR__ . '/app/models/Transaction.php';
require __DIR__ . '/app/models/Setting.php';
require __DIR__ . '/app/models/Category.php';
require __DIR__ . '/app/models/AdminLog.php';
require __DIR__ . '/app/models/SupportTicket.php';
require __DIR__ . '/app/helpers/mail.php';
require __DIR__ . '/app/helpers/admin.php';

function app_url(string $path = ''): string
{
    global $config;
    return rtrim($config['base_url'], '/') . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return app_url('public/assets/' . ltrim($path, '/'));
}

function media_url(?string $path): string
{
    $path = $path ?: 'images/product-necklace.png';

    if (starts_with($path, 'uploads/')) {
        return app_url('public/' . $path);
    }

    return asset($path);
}

function starts_with(string $value, string $prefix): bool
{
    return $prefix === '' || strncmp($value, $prefix, strlen($prefix)) === 0;
}

function view(string $template, array $data = []): void
{
    extract($data, EXTR_SKIP);
    require __DIR__ . '/resources/views/layouts/header.php';
    require __DIR__ . '/resources/views/layouts/navbar.php';
    require __DIR__ . '/resources/views/' . $template . '.php';
    require __DIR__ . '/resources/views/layouts/footer.php';
}

function redirect(string $path): void
{
    header('Location: ' . app_url($path));
    exit;
}

/**
 * Renders an admin page inside the dedicated admin shell (own header,
 * collapsible sidebar, topbar) — completely separate from the storefront
 * header/navbar/footer used by view().
 */
function admin_view(string $template, array $data = []): void
{
    global $route;
    $data['route'] = $data['route'] ?? $route ?? '';
    extract($data, EXTR_SKIP);
    require __DIR__ . '/resources/views/layouts/admin-header.php';
    require __DIR__ . '/resources/views/layouts/admin-sidebar.php';
    require __DIR__ . '/resources/views/admin/' . $template . '.php';
    require __DIR__ . '/resources/views/layouts/admin-footer.php';
}

function flash_set(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_get(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($payload);
    exit;
}

function paystack_request(string $endpoint, array $payload = [], string $method = 'POST'): array
{
    $config = require __DIR__ . '/config/paystack.php';
    $secret = $config['secret_key'] ?? '';

    if ($secret === '') {
        return ['status' => false, 'message' => 'Paystack secret key is not configured.'];
    }

    $url = 'https://api.paystack.co/' . ltrim($endpoint, '/');

    if (!function_exists('curl_init')) {
        $options = [
            'http' => [
                'method' => $method,
                'header' => implode("\r\n", [
                    'Authorization: Bearer ' . $secret,
                    'Content-Type: application/json',
                ]),
                'timeout' => 30,
                'ignore_errors' => true,
            ],
        ];

        if ($method === 'POST') {
            $options['http']['content'] = json_encode($payload);
        }

        $body = @file_get_contents($url, false, stream_context_create($options));
        if ($body === false) {
            return ['status' => false, 'message' => 'Paystack request failed. Enable PHP curl or allow HTTPS streams.'];
        }

        $decoded = json_decode($body, true);
        return is_array($decoded) ? $decoded : ['status' => false, 'message' => 'Invalid Paystack response.'];
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $secret,
            'Content-Type: application/json',
        ],
        CURLOPT_TIMEOUT => 30,
    ]);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    }

    $body = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($body === false) {
        return ['status' => false, 'message' => $error ?: 'Paystack request failed.'];
    }

    $decoded = json_decode($body, true);
    return is_array($decoded) ? $decoded : ['status' => false, 'message' => 'Invalid Paystack response.'];
}

function complete_paid_order(string $reference, int $paidKobo): bool
{
    $order = Order::findByReference($reference);
    $expectedKobo = $order ? (int) round(((float) $order['total']) * 100) : 0;

    if (!$order || $paidKobo !== $expectedKobo) {
        error_log("Paystack payment mismatch for {$reference}. Expected {$expectedKobo}, got {$paidKobo}.");
        return false;
    }

    Transaction::markSuccessful($reference);
    Order::markPaid($reference);

    return true;
}

function cart_payload_total(array $items, ?string $discountCode = null): array
{
    $subtotal = 0.0;
    $normalizedItems = [];

    foreach ($items as $item) {
        $product = Product::find((int) ($item['id'] ?? 0));
        $quantity = max(1, min(99, (int) ($item['quantity'] ?? 1)));
        $price = (float) ($product['price'] ?? 0);
        $subtotal += $price * $quantity;
        $normalizedItems[] = [
            'id' => (int) ($product['id'] ?? 0),
            'name' => $product['name'] ?? 'Product',
            'quantity' => $quantity,
            'price' => $price,
        ];
    }

    $discounts = [
        'SAVE10' => ['type' => 'percent', 'value' => 10],
        'WELCOME5' => ['type' => 'percent', 'value' => 5],
        'DEAL5000' => ['type' => 'fixed', 'value' => 5000, 'min' => 50000],
    ];
    $code = strtoupper(trim((string) $discountCode));
    $discount = 0.0;

    if (isset($discounts[$code]) && (!isset($discounts[$code]['min']) || $subtotal >= $discounts[$code]['min'])) {
        $discount = $discounts[$code]['type'] === 'percent'
            ? round($subtotal * ($discounts[$code]['value'] / 100), 2)
            : min($subtotal, (float) $discounts[$code]['value']);
    }

    return [
        'items' => $normalizedItems,
        'subtotal' => $subtotal,
        'discount' => $discount,
        'total' => max(0, $subtotal - $discount),
        'discount_code' => $discount > 0 ? $code : null,
    ];
}

function product_collection(array $products, ?string $collection, ?string $query = null): array
{
    $collectionMap = [
        'women' => "Women's Fashion",
        'men' => "Men's Fashion",
        'beauty-skincare' => 'Beauty & Skincare',
        'makeup-cosmetics' => 'Makeup & Cosmetics',
        'deals' => 'Deals',
    ];

    $filtered = $products;

    if ($collection && isset($collectionMap[$collection])) {
        $label = $collectionMap[$collection];
        $filtered = array_filter($filtered, static function (array $product) use ($collection, $label): bool {
            if ($collection === 'deals') {
                return (int) ($product['discount_percent'] ?? 0) > 0;
            }

            return strcasecmp((string) ($product['collection'] ?? ''), $label) === 0
                || strcasecmp((string) ($product['category'] ?? ''), $label) === 0;
        });
    }

    $query = trim((string) $query);
    if ($query !== '') {
        $filtered = array_filter($filtered, static function (array $product) use ($query): bool {
            return stripos((string) $product['name'], $query) !== false
                || stripos((string) ($product['category'] ?? ''), $query) !== false
                || stripos((string) ($product['collection'] ?? ''), $query) !== false;
        });
    }

    return array_values($filtered);
}

function collection_title(?string $collection, ?string $query = null): string
{
    $titles = [
        'women' => "Women's Fashion",
        'men' => "Men's Fashion",
        'beauty-skincare' => 'Beauty & Skincare',
        'makeup-cosmetics' => 'Makeup & Cosmetics',
        'deals' => 'Deals',
    ];

    $query = trim((string) $query);
    if ($query !== '') {
        return 'Search results for "' . $query . '"';
    }

    return $titles[$collection ?? ''] ?? 'All Products';
}

function require_user(): void
{
    if (!isset($_SESSION['user_id'])) {
        redirect('login');
    }
}

function require_admin(): void
{
    if (($_SESSION['role'] ?? '') !== 'admin') {
        redirect('login');
    }
}

function upload_product_image(array $file): string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return 'images/product-necklace.png';
    }

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    $mime = mime_content_type($file['tmp_name']);

    if (!isset($allowed[$mime])) {
        return 'images/product-necklace.png';
    }

    $filename = 'product-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
    $dir = __DIR__ . '/public/uploads/products';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $target = $dir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        error_log('Product image upload failed: ' . $target);
        return 'images/product-necklace.png';
    }

    return 'uploads/products/' . $filename;
}

function upload_banner_image(array $file, ?string $fallback = null): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return $fallback;
    }

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];
    $mime = mime_content_type($file['tmp_name']);

    if (!isset($allowed[$mime])) {
        return $fallback;
    }

    $dir = __DIR__ . '/public/uploads/banners';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $filename = 'banner-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
    $target = $dir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        error_log('Banner image upload failed: ' . $target);
        return $fallback;
    }

    return 'uploads/banners/' . $filename;
}

function upload_product_images(array $files, ?string $fallback = null): array
{
    $images = [];

    if (!isset($files['name']) || !is_array($files['name'])) {
        $single = upload_product_image($files);
        return [$single ?: ($fallback ?: 'images/product-necklace.png')];
    }

    for ($i = 0; $i < min(5, count($files['name'])); $i++) {
        $file = [
            'name' => $files['name'][$i] ?? '',
            'type' => $files['type'][$i] ?? '',
            'tmp_name' => $files['tmp_name'][$i] ?? '',
            'error' => $files['error'][$i] ?? UPLOAD_ERR_NO_FILE,
            'size' => $files['size'][$i] ?? 0,
        ];

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        $uploaded = upload_product_image($file);
        if ($uploaded) {
            $images[] = $uploaded;
        }
    }

    if (!$images && $fallback) {
        $images[] = $fallback;
    }

    return $images ?: ['images/product-necklace.png'];
}

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$basePath = parse_url($config['base_url'], PHP_URL_PATH) ?: '';

if ($basePath !== '' && starts_with($path, $basePath)) {
    $path = substr($path, strlen($basePath)) ?: '/';
}

$path = trim($path, '/');
$route = $path === '' ? 'products' : $path;

switch ($route) {
    case 'home':
        view('products/products', [
            'title' => 'All Products',
            'products' => Product::all(),
            'collectionTitle' => 'All Products',
            'heroBanner' => Banner::active('hero'),
            'weddingBanner' => Banner::active('wedding'),
        ]);
        break;

    case 'about':
        view('home/about', ['title' => 'About Us']);
        break;

    case 'contact':
        view('home/contact', ['title' => 'Contact Us']);
        break;

    case 'faq':
        view('home/faq', ['title' => 'FAQ']);
        break;

    case 'cart':
        view('cart/cart', [
            'title' => 'Cart',
            'cartItems' => [],
        ]);
        break;

    case 'checkout':
        view('cart/checkout', [
            'title' => 'Checkout',
            'cartItems' => [],
        ]);
        break;

    case 'success':
        view('cart/success', ['title' => 'Order Successful']);
        break;

    case 'paystack/initialize':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['status' => false, 'message' => 'Invalid request method.'], 405);
        }

        $payload = json_decode(file_get_contents('php://input') ?: '{}', true);
        $payload = is_array($payload) ? $payload : [];
        $email = trim((string) ($payload['email'] ?? ''));
        $name = trim((string) ($payload['name'] ?? ''));
        $phone = trim((string) ($payload['phone'] ?? ''));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            json_response(['status' => false, 'message' => 'Enter a valid email address.'], 422);
        }

        $cart = cart_payload_total($payload['items'] ?? [], $payload['discount_code'] ?? null);
        if ($cart['total'] <= 0 || !$cart['items']) {
            json_response(['status' => false, 'message' => 'Your cart is empty.'], 422);
        }

        $reference = 'VTUSHOP-' . date('YmdHis') . '-' . bin2hex(random_bytes(4));
        $orderId = Order::create([
            'user_id' => (int) ($_SESSION['user_id'] ?? 0),
            'reference' => $reference,
            'total' => $cart['total'],
            'status' => 'pending',
            'customer_email' => $email,
            'customer_name' => $name,
            'customer_phone' => $phone,
            'items' => $cart,
            'payment_provider' => 'paystack',
        ]);
        Transaction::create([
            'order_id' => $orderId,
            'provider' => 'paystack',
            'reference' => $reference,
            'amount' => $cart['total'],
            'status' => 'pending',
        ]);

        $response = paystack_request('transaction/initialize', [
            'email' => $email,
            'amount' => (int) round($cart['total'] * 100),
            'reference' => $reference,
            'callback_url' => app_absolute_url('paystack/callback'),
            'metadata' => [
                'customer_name' => $name,
                'customer_phone' => $phone,
                'discount_code' => $cart['discount_code'],
                'items' => $cart['items'],
            ],
        ]);

        if (!($response['status'] ?? false)) {
            json_response(['status' => false, 'message' => $response['message'] ?? 'Could not initialize Paystack.'], 502);
        }

        json_response([
            'status' => true,
            'authorization_url' => $response['data']['authorization_url'] ?? '',
            'reference' => $reference,
        ]);
        break;

    case 'paystack/callback':
        $reference = trim((string) ($_GET['reference'] ?? ''));
        if ($reference === '') {
            redirect('checkout?payment=missing-reference');
        }

        $response = paystack_request('transaction/verify/' . rawurlencode($reference), [], 'GET');
        $paidKobo = (int) ($response['data']['amount'] ?? 0);

        if (($response['status'] ?? false)
            && (($response['data']['status'] ?? '') === 'success')
            && complete_paid_order($reference, $paidKobo)) {
            redirect('success?reference=' . urlencode($reference));
        }

        redirect('checkout?payment=failed');
        break;

    case 'paystack/webhook':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method not allowed');
        }

        $config = require __DIR__ . '/config/paystack.php';
        $secret = $config['secret_key'] ?? '';
        $rawBody = file_get_contents('php://input') ?: '';
        $signature = $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] ?? '';
        $expectedSignature = $secret !== '' ? hash_hmac('sha512', $rawBody, $secret) : '';

        if ($secret === '' || $signature === '' || !hash_equals($expectedSignature, $signature)) {
            error_log('Invalid Paystack webhook signature.');
            http_response_code(401);
            exit('Invalid signature');
        }

        $event = json_decode($rawBody, true);
        if (!is_array($event)) {
            http_response_code(400);
            exit('Invalid payload');
        }

        if (($event['event'] ?? '') === 'charge.success') {
            $reference = (string) ($event['data']['reference'] ?? '');
            $status = (string) ($event['data']['status'] ?? '');
            $amount = (int) ($event['data']['amount'] ?? 0);

            if ($reference !== '' && $status === 'success') {
                complete_paid_order($reference, $amount);
            }
        }

        http_response_code(200);
        echo 'OK';
        break;

    case 'track-order':
        $trackedOrder = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $trackedOrder = Order::findForTracking(
                trim((string) ($_POST['reference'] ?? '')),
                trim((string) ($_POST['email'] ?? ''))
            );
        }
        view('user/track-order', ['title' => 'Track Order', 'trackedOrder' => $trackedOrder]);
        break;

    case 'wishlist':
        require_user();
        view('user/wishlist', ['title' => 'Wishlist']);
        break;

    case 'notifications':
        require_user();
        view('user/notifications', ['title' => 'Notifications']);
        break;

    case 'support':
        require_user();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            SupportTicket::create([
                'user_id' => $_SESSION['user_id'] ?? null,
                'name' => $_SESSION['user_name'] ?? '',
                'email' => $_SESSION['user_email'] ?? '',
                'subject' => trim((string) ($_POST['subject'] ?? '')),
                'message' => trim((string) ($_POST['message'] ?? '')),
            ]);
            flash_set('success', 'Your message has been sent. Our team will get back to you soon.');
            redirect('support');
        }

        view('user/support', [
            'title' => 'Support',
            'tickets' => SupportTicket::forUser((int) ($_SESSION['user_id'] ?? 0)),
            'flash' => flash_get(),
        ]);
        break;

    case 'user/dashboard':
        require_user();
        view('user/dashboard', [
            'title' => 'Dashboard',
            'orders' => Order::forUser((int) ($_SESSION['user_id'] ?? 0)),
        ]);
        break;

    case 'user/profile':
        require_user();
        view('user/profile', ['title' => 'Profile']);
        break;

    case 'user/orders':
        require_user();
        view('user/orders', [
            'title' => 'Orders',
            'orders' => Order::forUser((int) ($_SESSION['user_id'] ?? 0)),
        ]);
        break;

    case 'user/order':
        require_user();
        view('user/order-detail', ['title' => 'Order Details']);
        break;

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = User::findByEmail(trim($_POST['email'] ?? ''));

            if ($user && password_verify($_POST['password'] ?? '', $user['password'])) {
                User::ensureAuthSchema();

                if ((int) ($user['email_verified'] ?? 1) !== 1) {
                    view('auth/login', ['title' => 'Login', 'error' => 'Please verify your email address before signing in.']);
                    break;
                }

                $otp = (string) random_int(1000, 9999);
                User::updateAuthFields((int) $user['id'], [
                    'login_otp' => $otp,
                    'login_otp_expires' => date('Y-m-d H:i:s', strtotime('+10 minutes')),
                ]);
                send_login_otp_email($user['email'], $user['name'], $otp);
                $_SESSION['otp_user_id'] = (int) $user['id'];
                redirect('verify-login-otp');
            }

            view('auth/login', ['title' => 'Login', 'error' => 'Invalid email or password.']);
            break;
        }

        view('auth/login', ['title' => 'Login']);
        break;

    case 'logout':
    case 'admin/logout':
        session_destroy();
        redirect('products');
        break;

    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $verificationToken = bin2hex(random_bytes(32));
                $userId = User::create([
                    'name' => trim($_POST['name'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'phone' => trim($_POST['phone'] ?? ''),
                    'password' => $_POST['password'] ?? '',
                    'email_verified' => 0,
                    'verification_token' => $verificationToken,
                ]);

                send_verification_email(trim($_POST['email'] ?? ''), trim($_POST['name'] ?? ''), $verificationToken);
                view('auth/message', [
                    'title' => 'Verify Email',
                    'heading' => 'Check your email',
                    'message' => 'We sent a verification link to your email address. Verify your email before signing in.',
                    'actionUrl' => app_url('login'),
                    'actionLabel' => 'Back to sign in',
                ]);
            } catch (Throwable $exception) {
                view('auth/register', ['title' => 'Create Account', 'error' => $exception->getMessage()]);
            }
            break;
        }

        view('auth/register', ['title' => 'Create Account']);
        break;

    case 'verify-email':
        User::ensureAuthSchema();
        $token = trim((string) ($_GET['token'] ?? ''));
        $user = $token === '' ? null : User::findByToken('verification_token', $token);

        if (!$user) {
            view('auth/message', [
                'title' => 'Email Verification',
                'heading' => 'Verification link is invalid',
                'message' => 'The verification link is invalid or has already been used.',
                'actionUrl' => app_url('login'),
                'actionLabel' => 'Go to sign in',
            ]);
            break;
        }

        User::updateAuthFields((int) $user['id'], [
            'email_verified' => 1,
            'verification_token' => null,
        ]);
        view('auth/message', [
            'title' => 'Email Verified',
            'heading' => 'Email verified successfully',
            'message' => 'Your account is active. You can now sign in.',
            'actionUrl' => app_url('login'),
            'actionLabel' => 'Sign in',
        ]);
        break;

    case 'verify-login-otp':
        $otpUserId = (int) ($_SESSION['otp_user_id'] ?? 0);
        $otpUser = $otpUserId ? User::findById($otpUserId) : null;

        if (!$otpUser) {
            redirect('login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $otp = trim((string) ($_POST['otp'] ?? ''));
            $expires = strtotime((string) ($otpUser['login_otp_expires'] ?? ''));

            if ($otp === (string) ($otpUser['login_otp'] ?? '') && $expires && $expires >= time()) {
                User::updateAuthFields($otpUserId, [
                    'login_otp' => null,
                    'login_otp_expires' => null,
                    'last_otp_verified_at' => date('Y-m-d H:i:s'),
                ]);
                unset($_SESSION['otp_user_id']);
                $_SESSION['user_id'] = $otpUserId;
                $_SESSION['user_name'] = $otpUser['name'];
                $_SESSION['user_email'] = $otpUser['email'];
                $_SESSION['role'] = $otpUser['role'];

                if (($otpUser['role'] ?? '') === 'admin') {
                    AdminLog::log('Logged in');
                }

                redirect(($otpUser['role'] ?? '') === 'admin' ? 'admin/dashboard' : 'user/dashboard');
            }

            view('auth/verify-otp', ['title' => 'Verify OTP', 'error' => 'Invalid or expired OTP.']);
            break;
        }

        view('auth/verify-otp', ['title' => 'Verify OTP']);
        break;

    case 'resend-login-otp':
        $otpUserId = (int) ($_SESSION['otp_user_id'] ?? 0);
        $otpUser = $otpUserId ? User::findById($otpUserId) : null;

        if (!$otpUser) {
            redirect('login');
        }

        $otp = (string) random_int(1000, 9999);
        User::updateAuthFields($otpUserId, [
            'login_otp' => $otp,
            'login_otp_expires' => date('Y-m-d H:i:s', strtotime('+10 minutes')),
        ]);
        send_login_otp_email($otpUser['email'], $otpUser['name'], $otp);
        redirect('verify-login-otp?resent=1');
        break;

    case 'forgot-password':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim((string) ($_POST['email'] ?? ''));
            $user = filter_var($email, FILTER_VALIDATE_EMAIL) ? User::findByEmail($email) : null;

            if ($user) {
                $token = bin2hex(random_bytes(32));
                User::updateAuthFields((int) $user['id'], [
                    'reset_token' => $token,
                    'reset_expires' => date('Y-m-d H:i:s', strtotime('+1 hour')),
                ]);
                send_password_reset_email($user['email'], $user['name'], $token);
            }

            view('auth/message', [
                'title' => 'Password Reset',
                'heading' => 'Check your email',
                'message' => 'If an account exists for that email, a password reset link has been sent.',
                'actionUrl' => app_url('login'),
                'actionLabel' => 'Back to sign in',
            ]);
            break;
        }

        view('auth/forgot-password', ['title' => 'Forgot Password']);
        break;

    case 'reset-password':
        User::ensureAuthSchema();
        $token = trim((string) ($_GET['token'] ?? $_POST['token'] ?? ''));
        $user = $token === '' ? null : User::findByToken('reset_token', $token);

        if (!$user || empty($user['reset_expires']) || strtotime((string) $user['reset_expires']) < time()) {
            view('auth/message', [
                'title' => 'Reset Password',
                'heading' => 'Reset link expired',
                'message' => 'Please request a new password reset link.',
                'actionUrl' => app_url('forgot-password'),
                'actionLabel' => 'Request new link',
            ]);
            break;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = (string) ($_POST['password'] ?? '');
            $confirm = (string) ($_POST['confirm_password'] ?? '');

            if (strlen($password) < 6 || $password !== $confirm) {
                view('auth/reset-password', ['title' => 'Reset Password', 'token' => $token, 'error' => 'Passwords must match and be at least 6 characters.']);
                break;
            }

            User::updateAuthFields((int) $user['id'], [
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'reset_token' => null,
                'reset_expires' => null,
                'password_changed_at' => date('Y-m-d H:i:s'),
                'last_otp_verified_at' => null,
            ]);
            view('auth/message', [
                'title' => 'Password Reset',
                'heading' => 'Password updated',
                'message' => 'Your password has been reset. Sign in with your new password.',
                'actionUrl' => app_url('login'),
                'actionLabel' => 'Sign in',
            ]);
            break;
        }

        view('auth/reset-password', ['title' => 'Reset Password', 'token' => $token]);
        break;

    case 'admin/dashboard':
        require_admin();
        admin_view('dashboard', [
            'title' => 'Dashboard',
            'products' => Product::all(),
            'users' => User::all(),
            'banners' => Banner::all(),
            'orders' => Order::all(),
            'lowStock' => Product::lowStock((int) Setting::get('low_stock_threshold', '5')),
            'pendingReviews' => Product::pendingReviewCount(),
            'openTickets' => SupportTicket::openCount(),
        ]);
        break;

    case 'admin/products':
        require_admin();

        admin_view('products', [
            'title' => 'Products',
            'products' => Product::all(),
        ]);
        break;

    case 'admin/products/store':
        require_admin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $images = upload_product_images($_FILES['images'] ?? ($_FILES['image'] ?? []));
            Product::create([
                'name' => trim($_POST['name'] ?? ''),
                'short_name' => trim($_POST['short_name'] ?? ''),
                'category' => trim($_POST['category'] ?? ''),
                'collection' => trim($_POST['collection'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'price' => (float) ($_POST['price'] ?? 0),
                'stock' => (int) ($_POST['stock'] ?? 0),
                'original_price' => (float) ($_POST['original_price'] ?? 0),
                'discount_percent' => (int) ($_POST['discount_percent'] ?? 0),
                'rating' => (float) ($_POST['rating'] ?? 3.5),
                'reviews_count' => (int) ($_POST['reviews_count'] ?? 0),
                'image' => $images[0] ?? 'images/product-necklace.png',
                'images' => $images,
            ]);
            AdminLog::log('Created product "' . trim((string) ($_POST['name'] ?? '')) . '"', 'product');
            flash_set('success', 'Product created successfully.');
        }

        redirect('admin/products');
        break;

    case 'admin/products/update':
        require_admin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int) ($_POST['id'] ?? 0);
            $product = Product::find($id);
            $existingImages = Product::images($id, $product['image'] ?? null);
            $images = upload_product_images($_FILES['images'] ?? [], $existingImages[0] ?? ($product['image'] ?? null));
            $hasUploaded = isset($_FILES['images']['name']) && is_array($_FILES['images']['name'])
                ? count(array_filter($_FILES['images']['name'])) > 0
                : false;

            Product::update($id, [
                'name' => trim($_POST['name'] ?? ''),
                'short_name' => trim($_POST['short_name'] ?? ''),
                'category' => trim($_POST['category'] ?? ''),
                'collection' => trim($_POST['collection'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'price' => (float) ($_POST['price'] ?? 0),
                'stock' => (int) ($_POST['stock'] ?? 0),
                'original_price' => (float) ($_POST['original_price'] ?? 0),
                'discount_percent' => (int) ($_POST['discount_percent'] ?? 0),
                'rating' => (float) ($_POST['rating'] ?? 3.5),
                'reviews_count' => (int) ($_POST['reviews_count'] ?? 0),
                'image' => $hasUploaded ? ($images[0] ?? $product['image']) : ($product['image'] ?? 'images/product-necklace.png'),
                'images' => $hasUploaded ? $images : [],
            ]);
            AdminLog::log('Updated product #' . $id, 'product', $id);
            flash_set('success', 'Product updated successfully.');
        }

        redirect('admin/products');
        break;

    case 'admin/products/delete':
        require_admin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int) ($_POST['id'] ?? 0);
            Product::delete($id);
            AdminLog::log('Deleted product #' . $id, 'product', $id);
            flash_set('success', 'Product deleted.');
        }

        redirect('admin/products');
        break;

    case 'admin/orders':
        require_admin();
        admin_view('orders', ['title' => 'Orders', 'orders' => Order::all()]);
        break;

    case 'admin/orders/status':
        require_admin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int) ($_POST['id'] ?? 0);
            $status = trim((string) ($_POST['status'] ?? 'pending'));
            Order::updateStatus($id, $status);
            AdminLog::log('Set order #' . $id . ' status to ' . $status, 'order', $id);
            flash_set('success', 'Order status updated.');
        }

        redirect('admin/orders');
        break;

    case 'admin/users':
        require_admin();
        admin_view('users', ['title' => 'Users', 'users' => User::all()]);
        break;

    case 'admin/payments':
        require_admin();
        admin_view('payments', ['title' => 'Payments', 'transactions' => Transaction::all()]);
        break;

    case 'admin/delivery':
        require_admin();
        admin_view('delivery', ['title' => 'Delivery', 'orders' => Order::all()]);
        break;

    case 'admin/categories':
        require_admin();
        admin_view('categories', ['title' => 'Categories', 'categories' => Category::withCounts()]);
        break;

    case 'admin/categories/store':
        require_admin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim((string) ($_POST['name'] ?? ''));
            if ($name !== '') {
                Category::create([
                    'name' => $name,
                    'description' => $_POST['description'] ?? '',
                    'sort_order' => $_POST['sort_order'] ?? 0,
                ]);
                AdminLog::log('Created category "' . $name . '"', 'category');
                flash_set('success', 'Category created.');
            } else {
                flash_set('error', 'Category name is required.');
            }
        }

        redirect('admin/categories');
        break;

    case 'admin/categories/update':
        require_admin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int) ($_POST['id'] ?? 0);
            Category::update($id, [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'sort_order' => $_POST['sort_order'] ?? 0,
            ]);
            AdminLog::log('Updated category #' . $id, 'category', $id);
            flash_set('success', 'Category updated.');
        }

        redirect('admin/categories');
        break;

    case 'admin/categories/delete':
        require_admin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int) ($_POST['id'] ?? 0);
            Category::delete($id);
            AdminLog::log('Deleted category #' . $id, 'category', $id);
            flash_set('success', 'Category deleted.');
        }

        redirect('admin/categories');
        break;

    case 'admin/banners':
        require_admin();
        admin_view('banners', ['title' => 'Banners', 'banners' => Banner::all()]);
        break;

    case 'admin/banners/store':
        require_admin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Banner::create([
                'title' => trim($_POST['title'] ?? ''),
                'subtitle' => trim($_POST['subtitle'] ?? ''),
                'button_text' => trim($_POST['button_text'] ?? ''),
                'link_url' => trim($_POST['link_url'] ?? ''),
                'placement' => trim($_POST['placement'] ?? 'hero'),
                'is_active' => isset($_POST['is_active']),
                'sort_order' => (int) ($_POST['sort_order'] ?? 0),
                'image' => upload_banner_image($_FILES['image'] ?? []),
            ]);
            AdminLog::log('Created banner "' . trim((string) ($_POST['title'] ?? '')) . '"', 'banner');
            flash_set('success', 'Banner created.');
        }

        redirect('admin/banners');
        break;

    case 'admin/banners/update':
        require_admin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $existing = null;
            foreach (Banner::all() as $banner) {
                if ((int) $banner['id'] === (int) ($_POST['id'] ?? 0)) {
                    $existing = $banner;
                    break;
                }
            }
            Banner::update((int) ($_POST['id'] ?? 0), [
                'title' => trim($_POST['title'] ?? ''),
                'subtitle' => trim($_POST['subtitle'] ?? ''),
                'button_text' => trim($_POST['button_text'] ?? ''),
                'link_url' => trim($_POST['link_url'] ?? ''),
                'placement' => trim($_POST['placement'] ?? 'hero'),
                'is_active' => isset($_POST['is_active']),
                'sort_order' => (int) ($_POST['sort_order'] ?? 0),
                'image' => upload_banner_image($_FILES['image'] ?? [], $existing['image'] ?? null),
            ]);
            AdminLog::log('Updated banner #' . (int) ($_POST['id'] ?? 0), 'banner');
            flash_set('success', 'Banner updated.');
        }

        redirect('admin/banners');
        break;

    case 'admin/banners/delete':
        require_admin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int) ($_POST['id'] ?? 0);
            Banner::delete($id);
            AdminLog::log('Deleted banner #' . $id, 'banner', $id);
            flash_set('success', 'Banner deleted.');
        }

        redirect('admin/banners');
        break;

    case 'admin/reviews':
        require_admin();
        admin_view('reviews', ['title' => 'Reviews', 'reviews' => Product::reviewsForAdmin()]);
        break;

    case 'admin/reviews/status':
        require_admin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int) ($_POST['id'] ?? 0);
            $status = trim((string) ($_POST['status'] ?? 'approved'));
            Product::setReviewStatus($id, $status);
            AdminLog::log('Set review #' . $id . ' to ' . $status, 'review', $id);
            flash_set('success', 'Review ' . $status . '.');
        }

        redirect('admin/reviews');
        break;

    case 'admin/reviews/delete':
        require_admin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int) ($_POST['id'] ?? 0);
            Product::deleteReview($id);
            AdminLog::log('Deleted review #' . $id, 'review', $id);
            flash_set('success', 'Review deleted.');
        }

        redirect('admin/reviews');
        break;

    case 'admin/reports':
        require_admin();

        $range = $_GET['range'] ?? '30';
        $allOrders = Order::all();
        $now = time();
        $rangeStarts = [
            'today' => strtotime('today midnight'),
            '7' => $now - 7 * 86400,
            '30' => $now - 30 * 86400,
            'all' => 0,
        ];

        if ($range === 'custom' && !empty($_GET['start'])) {
            $start = strtotime((string) $_GET['start']) ?: 0;
            $end = !empty($_GET['end']) ? strtotime((string) $_GET['end'] . ' 23:59:59') : $now;
        } else {
            $start = $rangeStarts[$range] ?? $rangeStarts['30'];
            $end = $now;
        }

        $filteredOrders = array_filter($allOrders, static function (array $order) use ($start, $end): bool {
            $created = strtotime((string) ($order['created_at'] ?? 'now'));
            return $created >= $start && $created <= $end;
        });

        $paidStatuses = ['paid', 'packed', 'shipped', 'delivered'];
        $revenue = 0.0;
        $paidCount = 0;
        $pendingCount = 0;
        $productTotals = [];

        foreach ($filteredOrders as $order) {
            $status = strtolower((string) ($order['status'] ?? 'pending'));
            if (in_array($status, $paidStatuses, true)) {
                $revenue += (float) ($order['total'] ?? 0);
                $paidCount++;
            }
            if ($status === 'pending') {
                $pendingCount++;
            }

            $items = json_decode((string) ($order['items_json'] ?? '[]'), true);
            $items = is_array($items['items'] ?? null) ? $items['items'] : (is_array($items) ? $items : []);
            foreach ($items as $item) {
                $name = (string) ($item['name'] ?? 'Product');
                $qty = (int) ($item['quantity'] ?? 1);
                $productTotals[$name] = ($productTotals[$name] ?? 0) + $qty;
            }
        }

        arsort($productTotals);
        $topProducts = array_slice($productTotals, 0, 5, true);

        admin_view('reports', [
            'title' => 'Reports',
            'range' => $range,
            'revenue' => $revenue,
            'paidCount' => $paidCount,
            'pendingCount' => $pendingCount,
            'orderCount' => count($filteredOrders),
            'topProducts' => $topProducts,
            'lowStock' => Product::lowStock((int) Setting::get('low_stock_threshold', '5')),
        ]);
        break;

    case 'admin/settings':
        require_admin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Setting::setMany([
                'store_name' => trim((string) ($_POST['store_name'] ?? '')),
                'support_email' => trim((string) ($_POST['support_email'] ?? '')),
                'support_phone' => trim((string) ($_POST['support_phone'] ?? '')),
                'free_shipping_threshold' => (string) (float) ($_POST['free_shipping_threshold'] ?? 0),
                'low_stock_threshold' => (string) (int) ($_POST['low_stock_threshold'] ?? 5),
            ]);
            AdminLog::log('Updated store settings', 'setting');
            flash_set('success', 'Settings saved.');
            redirect('admin/settings');
        }

        admin_view('settings', ['title' => 'Settings', 'settings' => Setting::all()]);
        break;

    case 'admin/notifications':
        require_admin();

        $lowStockProducts = Product::lowStock((int) Setting::get('low_stock_threshold', '5'));
        $pendingOrdersList = array_values(array_filter(Order::all(), static function (array $order): bool {
            return strtolower((string) ($order['status'] ?? '')) === 'pending';
        }));

        admin_view('notifications', [
            'title' => 'Notifications',
            'lowStockProducts' => $lowStockProducts,
            'pendingOrders' => array_slice($pendingOrdersList, 0, 8),
            'pendingReviewCount' => Product::pendingReviewCount(),
            'openTicketCount' => SupportTicket::openCount(),
        ]);
        break;

    case 'admin/support':
        require_admin();
        admin_view('support', ['title' => 'Support', 'tickets' => SupportTicket::all()]);
        break;

    case 'admin/support/reply':
        require_admin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int) ($_POST['id'] ?? 0);
            SupportTicket::reply($id, trim((string) ($_POST['reply'] ?? '')), 'resolved');
            AdminLog::log('Replied to support ticket #' . $id, 'support_ticket', $id);
            flash_set('success', 'Reply sent and ticket marked resolved.');
        }

        redirect('admin/support');
        break;

    case 'admin/support/status':
        require_admin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int) ($_POST['id'] ?? 0);
            $status = trim((string) ($_POST['status'] ?? 'open'));
            SupportTicket::updateStatus($id, $status);
            AdminLog::log('Set ticket #' . $id . ' to ' . $status, 'support_ticket', $id);
            flash_set('success', 'Ticket updated.');
        }

        redirect('admin/support');
        break;

    case 'admin/security':
        require_admin();
        admin_view('security', [
            'title' => 'Security',
            'logs' => AdminLog::recent(30),
            'admin' => User::findById((int) ($_SESSION['user_id'] ?? 0)),
        ]);
        break;

    case 'admin/security/password':
        require_admin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $current = (string) ($_POST['current_password'] ?? '');
            $new = (string) ($_POST['new_password'] ?? '');
            $confirm = (string) ($_POST['confirm_password'] ?? '');
            $adminUser = User::findById((int) ($_SESSION['user_id'] ?? 0));

            if (!$adminUser || !password_verify($current, $adminUser['password'])) {
                flash_set('error', 'Current password is incorrect.');
            } elseif (strlen($new) < 6 || $new !== $confirm) {
                flash_set('error', 'New password must be at least 6 characters and match confirmation.');
            } else {
                User::updateAuthFields((int) $adminUser['id'], [
                    'password' => password_hash($new, PASSWORD_DEFAULT),
                    'password_changed_at' => date('Y-m-d H:i:s'),
                ]);
                AdminLog::log('Changed admin password', 'security');
                flash_set('success', 'Password updated.');
            }
        }

        redirect('admin/security');
        break;

    case 'product':
    case 'products/show':
        $id = (int) ($_GET['id'] ?? 1);
        $product = Product::find($id);
        view('products/single-product', [
            'title' => $product['name'],
            'product' => $product,
            'productImages' => Product::images((int) $product['id'], $product['image'] ?? null),
            'reviews' => Product::reviews((int) $product['id']),
        ]);
        break;

    case 'product/review':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = (int) ($_POST['product_id'] ?? 0);
            Product::addReview($productId, [
                'customer_name' => trim($_POST['customer_name'] ?? ''),
                'rating' => (int) ($_POST['rating'] ?? 5),
                'comment' => trim($_POST['comment'] ?? ''),
            ]);
            redirect('product?id=' . $productId . '#reviews');
        }

        redirect('products');
        break;

    case 'products':
    default:
        $collection = $_GET['collection'] ?? null;
        $query = $_GET['q'] ?? null;
        view('products/products', [
            'title' => collection_title($collection, $query),
            'products' => product_collection(Product::all(), $collection, $query),
            'collectionTitle' => collection_title($collection, $query),
            'heroBanner' => Banner::active('hero'),
            'weddingBanner' => Banner::active('wedding'),
        ]);
        break;
}
