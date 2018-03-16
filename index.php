<?php
    include 'paypal-init.php';

    $payment = [
        'products' => [
            0 => [
                'name' => 'Camarão Cinza',
                'desc' => 'desc 1',
                'amount' => 50,
                'qtd' => 1
            ],
            1 => [
                'name' => 'X-Bacon',
                'desc' => 'desc 2',
                'amount' => 50,
                'qtd' => 1
            ],
            2 => [
                'name' => 'Lasanha',
                'desc' => 'desc 3',
                'amount' => 50,
                'qtd' => 1
            ],
            3 => [
                'name' => 'Curso desenvolvimento Intel XDK',
                'desc' => 'desc 4',
                'amount' => 50,
                'qtd' => 1
            ]
        ],
        'currency' => 'BRL',
        'type' => 'SALE',
        'amount' => 200,
        'checkout_url' => 'https://iorder.me/api-paypal/checkout.php',
        'cancel_url' => 'http://google.com.br'
    ];

    $paymentUrl = $paypal->setPaymentInfo($payment)->doPayment();

    if($paymentUrl){
        header("Location: ".$paymentUrl);
    }else{
        echo 'Erro';
    }