<?php
    function __autoload($classname){
        require_once 'PaypalApi/'.$classname.'.php';
    }

    $paypal = new PaypalApi([
        'sandbox_auth' => [
            'user' => 'seuusuario',
            'pswd' => 'sua senha',
            'signature' => 'seu signature'
        ],
        'production_auth' => [
            'user' => 'seuusuario',
            'pswd' => 'sua senha',
            'signature' => 'seu signature'
        ],
        'sandbox' => true
    ]);