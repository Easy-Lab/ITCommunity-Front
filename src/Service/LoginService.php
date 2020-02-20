<?php


namespace App\Service;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

class LoginService
{

    protected $sessionInterface;
    /**
     * LoginService constructor.
     */
    public function __construct(SessionInterface $sessionInterface)
    {
        $this->sessionInterface = $sessionInterface;
    }

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

    public function createSession(array $data, string $token, Request $request){
        if ($request->hasSession() && $this->sessionInterface){
            $this->sessionInterface->set('email',$data['email']);
            $this->sessionInterface->set('username',$data['username']);
            $this->sessionInterface->set('token', $token);
            $this->sessionInterface->set('role', 'ROLE_USER');

            return $this->sessionInterface;
        }
        $session = new Session(new NativeSessionStorage(), new AttributeBag());
        $session->start();

        $session->set('email',$data['email']);
        $session->set('username',$data['username']);
        $session->set('token', $token);
        $session->set('role', 'ROLE_USER');

        $response = new Response();
        $response->headers->set('Authorization','Bearer '.$token);
        $response->sendHeaders();

        return $session;
    }

}
