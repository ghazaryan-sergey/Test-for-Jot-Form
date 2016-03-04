<?php

include_once 'config.php';

class CUrlClient
{
    private $curl;

    public function __construct()
    {
        $this->curl = curl_init();
    }

    public function send($option)
    {
        //reset curl option
        curl_reset($this->curl);

        //add default option
        $default = $this->defaultOption();

        foreach($option as $key => $seting){
            if(is_array($seting) && isset($default[$key])){
                $seting = array_merge($seting, $default[$key]);
                unset($default[$key]);
            }
        }

        $option = array_merge($default, $option);

        //set curl option
        foreach($option as $seting => $value){
            switch($seting){
                case 'type':
                    switch ($value){
                        case 'GET':
                            curl_setopt($this->curl, CURLOPT_HTTPGET, true);
                            break;
                        case "POST":
                            curl_setopt($this->curl, CURLOPT_POST, true);
                            break;
                        case "POST":
                            curl_setopt($this->curl, CURLOPT_PUT, true);
                            break;
                    }
                    break;
                case 'url':
                    if(isset($option['data']) && $option['type'] == 'GET') {
                        $value .= '?' . $this->encodeParemeters($option['data']);
                    }

                    curl_setopt($this->curl, CURLOPT_URL, $value);
                    break;
                case 'return_transfer':
                    curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, $value);
                    break;
                case 'user_agent':
                    curl_setopt($this->curl, CURLOPT_USERAGENT, $value);
                    break;
                case 'header':
                    curl_setopt($this->curl, CURLOPT_HTTPHEADER, $value);
                    break;
                case 'ssl':
                    curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, $value);
                    break;
                case 'data':
                    switch ($option['type']) {
                        case 'PUT':
                        case 'POST':
                            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $value);
                            break;
                    }
                    break;
            }
        }

        return curl_exec($this->curl);
    }

    private function encodeParemeters($params){
        foreach($params as $key => &$value){
            $value = $key . '=' . $value;
        }

        return implode('&', $params);
    }

   private function defaultOption(){
        return [
            'type' => 'GET',
            'return_transfer' => true,
            'ssl' => false
        ];
    }
}