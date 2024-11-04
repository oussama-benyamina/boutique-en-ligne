<?php
require 'vendor/autoload.php'; // Make sure you have the Stripe PHP library installed
\Stripe\Stripe::setApiKey('sk_test_51Q5msRP1qIvvzGGYRrbmtiY0QybxYeoUMZhzr8LRNTr4BOclqzF15QUfDbS0hJQws1P0htM8ziEMEBMSpyLVbA0x00ZleNM23a');

header('Content-Type: application/json');

$YOUR_DOMAIN = 'http://localhost/boutiaue_plateforme';

$checkout_session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price_data' => [
            'currency' => 'usd',
            'product_data' => [
                'name' => 'Total Amount',
            ],
            'unit_amount' => $_POST['amount'], // amount in cents
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'success_url' => $YOUR_DOMAIN . '/success.html',
    'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
]);

echo json_encode(['id' => $checkout_session->id]);
