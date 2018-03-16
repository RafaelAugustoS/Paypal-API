<?php
    abstract class Paypal{
        protected $sandbox = true;
        protected $user;
        protected $pswd;
        protected $signature;

        protected $endopint;
        public function __construct(array $params){
            $this->sandbox = $params['sandbox'];

            if(!$this->sandbox){
                $index = 'production_auth';
            }else{
                $index = 'sandbox_auth';
            }
            
            $this->user = $params[$index]['user'];
            $this->pswd = $params[$index]['pswd'];
            $this->signature = $params[$index]['signature'];
            

            $this->setEndpoint();
        }

        private function setEndpoint(){
            $this->endpoint = 'https://www.';
            if($this->sandbox){
                $this->endpoint .= 'sandbox.';
            }

            $this->endpoint .= 'paypal.com/cgi-bin/webscr';
        }

        protected function sendNvpRequest(array $requestNvp){
            $apiEndpoint  = 'https://api-3t.' . ($this->sandbox? 'sandbox.': null);
            $apiEndpoint .= 'paypal.com/nvp';
         
            $curl = curl_init();
         
            curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($requestNvp));
         
            $response = urldecode(curl_exec($curl));
         
            curl_close($curl);
         
            $responseNvp = array();
            if (preg_match_all('/(?<name>[^\=]+)\=(?<value>[^&]+)&?/', $response, $matches)) {
                foreach ($matches['name'] as $offset => $name) {
                    $responseNvp[$name] = $matches['value'][$offset];
                }
            }
         
  
            if (isset($responseNvp['ACK']) && $responseNvp['ACK'] != 'Success') {
                for ($i = 0; isset($responseNvp['L_ERRORCODE' . $i]); ++$i) {
                    $message = sprintf(
                        "PayPal NVP %s[%d]: %s\n",
                        $responseNvp['L_SEVERITYCODE'.$i],
                        $responseNvp['L_ERRORCODE' . $i],
                        $responseNvp['L_LONGMESSAGE'.$i]
                    );
         
                    error_log($message);
                }
            }
         
            return $responseNvp;
        }
    }