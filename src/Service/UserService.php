<?php


namespace App\Service;


use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class UserService
{
    protected $session;

    /**
     * UserService constructor.
     * @param $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function getUser()
    {
        if ($this->session)
        {
            $client = HttpClient::create(['headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->session->get('token')
            ]]);
            $response = $client->request('GET', getenv('API_URL') . '/users/'.$this->session->get('username')
            );
            $statusCode = $response->getStatusCode();
            if ($statusCode == 200){
                return $response->toArray();

            }else{
                return null;
            }
        }
        return null;
    }
}
