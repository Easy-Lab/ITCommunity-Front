<?php

namespace App\Controller\evaluation;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
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
    public function index($hash, Validator $validator, Request $request)
    {
        $actual_route = $request->get('actual_route', 'evaluation');
        $client = HttpClient::create();
        $responseMessage = $client->request('GET', getenv('API_URL') . '/messages/' . $hash);
        $statusCode = $responseMessage->getStatusCode();
        if ($statusCode == 200) {
            $message = $responseMessage->toArray();
        } else {
            $validator->fail();
            return $this->redirectToRoute('home');
        }

        if ($validator->post()) {
            $validator->required('rating', 'feedback');

            if ($validator->check() && $validator->get('rating') && $validator->get('feedback')) {
                $dataEvaluation =
                    [
                        'hash' => $hash,
                        'rating' => (int)$validator->get('rating'),
                        'feedback' => $validator->get('feedback'),
                    ];
                $responseEvaluation = $client->request('POST', getenv('API_URL') . '/evaluations', [
                    'headers' => ['content_type' => 'application/json'],
                    'body' => json_encode($dataEvaluation)
                ]);

                if ($responseEvaluation->getStatusCode() == 201) {
                    $validator->success('evaluation.success_send');
                    return $this->redirectToRoute('home');
                }
            }
        }

        return $this->render('contact/evaluation.html.twig',
            [
                'hash' => $hash,
                'validator' => $validator,
                'message' => $message,
                'actual_route' => $actual_route,
                'google_analytics_id'=>getenv("ANALYTICS_KEY"),
            ]);
    }

}
