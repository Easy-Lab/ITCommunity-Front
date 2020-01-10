<?php

namespace App\Controller\ambassador;

use App\Utils\Features;
use App\Utils\Validator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @Route("/ambassador/register")
 */
class RegisterController extends AbstractController
{
    protected $features;

    public function __construct(Features $features)
    {
        $this->features = $features;
    }

    /**
     * @Route("/{hash}", name="ambassador_register")
     */
    public function index($hash = null, Validator $validator,Features $features, Request $request)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        $form = [];

        $properties = $features->get("forms.register.step_0");

        if (is_array($properties)) {
            foreach ($properties as $property) {
                $form[] = $property;
            }
        }

        $actual_route = $request->get('actual_route', 'step_0');

        return $this->render('ambassador/register/step_0.html.twig', [
            'validator'=>$validator,
            'features'=>$features,
            'actual_route'=>$actual_route,
            'form_properties'=>$form

        ]);
    }

}
