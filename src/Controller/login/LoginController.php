<?php


namespace App\Controller\login;

use App\Utils\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    /**
     * @Route("/se-connecter", name="login")
     */
    public function index(Request $request, Validator $validator)
    {
        $actual_route = $request->get('actual_route', 'login');
        if ($validator->post()){
            $validator->required('username','password');
            if ($validator->check()){
//                $client = HttpClient::create(['headers' => [
//                    'Content-Type' => 'application/json',
//                    'Authorization' => 'Bearer ' . $this->session->get('token')
//                ]]);
//                $response = $client->request('GET', getenv('API_URL') . '/users/'.$this->session->get('username')
//                );
//                $statusCode = $response->getStatusCode();
//                if ($statusCode == 200){
//                    return $response->toArray();
//
//                }else{
//                    return null;
//                }
            }
        }
        return $this->render('login/index.html.twig', [
            'actual_route'=>$actual_route,
            'validator'=>$validator
        ]);
    }

}
