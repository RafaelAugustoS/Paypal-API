<?php
    include 'paypal-init.php';
    $token = $_GET['token'];
    $payerid = $_GET['PayerID'];

    $checkout = $paypal->setCheckoutInfo([
        $token,
        $payerid,
        'SALE',
        200,
        'BRL'
    ])->doCheckout();

    if($checkout){
        echo '<pre>';
        print_r($checkout);
    }else{
        echo 'Erro no checkout';
    }