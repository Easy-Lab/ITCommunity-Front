<?php


namespace App\Controller\login;

use App\Utils\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        return $this->render('login/index.html.twig', [
            'actual_route'=>$actual_route,
            'validator'=>$validator
        ]);
    }

}
