<?php
// config/tripay.php

define('TRIPAY_API_KEY', 'DEV-j9aMrcnm4B57sWITO2RwTSVTsHGdsh9hP7C89CqH');
define('TRIPAY_PRIVATE_KEY', '00P7V-zbuTP-Mn88G-WKtSf-D0hDg');
define('TRIPAY_MERCHANT_CODE', 'T37399');
define('TRIPAY_IS_PRODUCTION', false);

// Base URLs
define('TRIPAY_URL_BASE', TRIPAY_IS_PRODUCTION ? 'https://tripay.co.id/api/' : 'https://tripay.co.id/api-sandbox/');

/**
 * Mendapatkan daftar channel pembayaran (Metode Pembayaran) yang tersedia
 */
function getTripayChannels() {
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_FRESH_CONNECT  => true,
        CURLOPT_URL            => TRIPAY_URL_BASE . 'merchant/payment-channel',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . TRIPAY_API_KEY],
        CURLOPT_FAILONERROR    => false,
    ]);

    $response = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);

    if (empty($error)) {
        $result = json_decode($response, true);
        if ($result['success']) {
            return $result['data'];
        }
    }
    return [];
}

/**
 * Membuat transaksi baru di Tripay (Closed Payment)
 */
function createTripayTransaction($order_id, $amount, $method, $customer_name, $customer_email, $customer_phone, $order_items, $return_url) {
    $privateKey = TRIPAY_PRIVATE_KEY;
    $apiKey = TRIPAY_API_KEY;
    $merchantCode = TRIPAY_MERCHANT_CODE;
    
    $signature = hash_hmac('sha256', $merchantCode . $order_id . $amount, $privateKey);

    $data = [
        'method'         => $method,
        'merchant_ref'   => $order_id,
        'amount'         => $amount,
        'customer_name'  => $customer_name,
        'customer_email' => $customer_email,
        'customer_phone' => $customer_phone,
        'order_items'    => $order_items,
        'return_url'     => $return_url,
        'expired_time'   => (time() + (24 * 60 * 60)), // 24 hours
        'signature'      => $signature
    ];

    $curl = curl_init();
    
    curl_setopt_array($curl, [
        CURLOPT_FRESH_CONNECT  => true,
        CURLOPT_URL            => TRIPAY_URL_BASE . 'transaction/create',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $apiKey],
        CURLOPT_FAILONERROR    => false,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($data),
    ]);
    
    $response = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);
    
    if (empty($error)) {
        return json_decode($response, true);
    }
    
    return [
        'success' => false,
        'message' => $error
    ];
}
?>
