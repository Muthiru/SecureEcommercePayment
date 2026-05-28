<?php
use Vault\SecurityHeaders;

require_once __DIR__ . '/../api/bootstrap.php';

$nonce = base64_encode(random_bytes(16));
SecurityHeaders::apply($nonce);
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed.']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);
if (!$body || !isset($body['token'], $body['amount'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request payload.']);
    exit;
}

$token    = $body['token'];
$amount   = (int) $body['amount'];
$currency = $body['currency'] ?? 'usd';

// Reject anything that doesn't look like a Stripe token (tok_...)
if (!preg_match('/^tok_\w+$/', $token)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid token format.']);
    exit;
}

// 50¢ minimum (Stripe lower bound); cap at $999,999.99 to guard against tampering
if ($amount < 50 || $amount > 99999999) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid amount.']);
    exit;
}


// Send the charge to Stripe using HTTP Basic Auth (secret key as username, no password)
$ch = curl_init('https://api.stripe.com/v1/charges');

$params = http_build_query([
    'amount'      => $amount,
    'currency'    => $currency,
    'source'      => $token,
    'description' => 'VAULT Store Order',
    'metadata'    => [
        'demo' => 'true',
    ]
]);

curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $params,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_USERPWD        => STRIPE_SECRET_KEY . ':',
    CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
    CURLOPT_TIMEOUT        => 30,
]);

$responseBody = curl_exec($ch);
$httpStatus   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError    = curl_error($ch);


if ($curlError) {
    error_log('[VAULT] cURL error: ' . $curlError);
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Gateway connection failed.']);
    exit;
}

$charge = json_decode($responseBody, true);

if ($httpStatus === 200 && isset($charge['status']) && $charge['status'] === 'succeeded') {
    error_log('[VAULT] Charge succeeded. charge_id=' . $charge['id'] . ' token=' . $token . ' amount=' . $amount);

    echo json_encode([
        'success'   => true,
        'charge_id' => $charge['id'],
        'token_used'=> $token,
        'amount'    => $amount,
        'currency'  => $currency,
    ]);

} else {
    $stripeError = $charge['error']['message'] ?? 'Payment declined.';
    error_log('[VAULT] Charge failed. status=' . $httpStatus . ' error=' . $stripeError);

    http_response_code($httpStatus >= 400 ? $httpStatus : 400);
    echo json_encode(['success' => false, 'error' => $stripeError]);
}
