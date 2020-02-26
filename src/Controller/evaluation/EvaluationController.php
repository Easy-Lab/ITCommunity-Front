<?php

namespace App\Controller\evaluation;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Utils\Validator;
use App\Utils\Features;

class EvaluationController extends AbstractController
{
    /**
     * @Route("/evaluation/{hash}", name="evaluation")
     */
    public function index($hash, Validator $validator, UserService $userService, Features $features)
    {
        try {
            $contact = null;
            $ambassador = null;

            if (is_null($contact)) throw new \Exception('Null contact');

            $context = $contact->getContext();

            if (!$features->get("contact.evaluation.$context.enabled")) {
                throw new \Exception("Bad context");
            }

            $evaluations = null;

            if (!is_null($evaluations) && count($evaluations) > 0) throw new \Exception('Existing evaluation');

            if ($validator->post()) {

                $validator->required('rating', 'feedback')->inArray('rating', [1, 2, 3, 4, 5]);

                if ($validator->check()) {

                    $validator->success('contact.evaluation_success');
                    return $this->redirectToRoute('home');
                }
                $validator->fail();
            }
        } catch (\Exception $e) {
            //die(var_dump($e->getMessage()));
            $validator->success('contact.evaluation_success');
            return $this->redirectToRoute('home');
        }

        return $this->render('contact/evaluation.html.twig', [
            'contact' => $contact,
            'hash'=>$hash,
            'ambassador' => $ambassador,
            'validator' => $validator,
        ]);
    }

}
