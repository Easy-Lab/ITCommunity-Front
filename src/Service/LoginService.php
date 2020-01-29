<?php


namespace App\Service;


class LoginService
{
    public function getToken(array $data){
        $data_string = json_encode($data);

        $ch = curl_init(getenv('API_URL').'/login_check');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
        );

        $result = curl_exec($ch);
        return json_decode($result,true);
    }

}
