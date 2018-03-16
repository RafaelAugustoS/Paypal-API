<?php
    class PaypalApi extends Paypal{
        private $payment_info = array();

        /*
        * Seta as informações para pagamento
        * tipo, valor, titulo, urlcheckout
        */
        public function setPaymentInfo(array $params){
            $this->payment_info = $params;

            return $this;
        }

        public function setCheckoutInfo(array $params){
            $this->payment_info = array(
                'token' => $params[0],
                'payerid' => $params[1],
                'type' => $params[2],
                'value' => $params[3],
                'currency' => $params[4]
            );

            return $this;
        }

        /*
        * retorna url de redirecionamento para o paypal caso sucesso
        * retorna false caso erro
        */
        public function doPayment(){
            $requestNvp = array(
                'USER' => $this->user,
                'PWD' => $this->pswd,
                'SIGNATURE' => $this->signature, 
                'VERSION' => '95',
                'METHOD'=> 'SetExpressCheckout',
                /*'PAYMENTREQUEST_0_CUSTOM' => $transacao,
                'PAYMENTREQUEST_0_NOTIFYURL' => 'listener.php',
                */
                'PAYMENTREQUEST_0_PAYMENTACTION' => $this->payment_info['type'],
                //valor da COMPRA 
                'PAYMENTREQUEST_0_AMT' => $this->payment_info['amount'],
                'PAYMENTREQUEST_0_CURRENCYCODE' => $this->payment_info['currency'],
                /*'PAYMENTREQUEST_0_ITEMAMT' => $this->payment_info['value'],*/
            );
            /*'L_PAYMENTREQUEST_0_ITEMAMT' => $this->payment_info['value'],*/

            //Sobre o Item
            foreach($this->payment_info['products'] as $i => $product){
                $requestNvp['L_PAYMENTREQUEST_0_NAME'.$i] = $product['name'];
                $requestNvp['L_PAYMENTREQUEST_0_AMT'.$i] = $product['amount'];
                $requestNvp['L_PAYMENTREQUEST_0_QTY'.$i] = $product['qtd'];
                $requestNvp['L_PAYMENTREQUEST_0_DESC'.$i] = $product['desc'];
            }
                

            $requestNvp['RETURNURL'] = $this->payment_info['checkout_url'];
            $requestNvp['CANCELURL'] = $this->payment_info['cancel_url'];
            $requestNvp['BUTTONSOURCE'] = 'MOBIDESK';
        
            $responseNvp = $this->sendNvpRequest($requestNvp);
            if (isset($responseNvp['ACK']) && $responseNvp['ACK'] == 'Success') {
                $query = array(
                    'cmd' => '_express-checkout',
                    'token'  => $responseNvp['TOKEN']
                ); 
                $redirectURL = sprintf('%s?%s', $this->endpoint, http_build_query($query)); 
                return $redirectURL;
            }
            
            return false;
        }
        /*
        * Efetua o DoExpressCheckoutPayment 
        * para receber o dinheiro do comprador
        * retorna o $responseNvp caso sucesso e false caso erro
        */
        public function doCheckout(){
            $requestNvp = array(
                'USER' => $this->user,
                'PWD' => $this->pswd,
                'SIGNATURE' => $this->signature,
             
                'VERSION' => '95',
                'METHOD'=> 'DoExpressCheckoutPayment',
                'TOKEN' => $this->payment_info['token'],
                'PAYERID' => $this->payment_info['payerid'],
                'PAYMENTREQUEST_0_PAYMENTACTION' => $this->payment_info['type'],
                'PAYMENTREQUEST_0_AMT' => $this->payment_info['value'],
                'PAYMENTREQUEST_0_CURRENCYCODE' => $this->payment_info['currency']
            );

            $responseNvp = $this->sendNvpRequest($requestNvp);
            if (isset($responseNvp['ACK']) && $responseNvp['ACK'] == 'Success') {
                return $responseNvp;
            }

            return false;
        }
    }