<?php


namespace App\Controller\bug;


use App\Utils\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Routing\Annotation\Route;

class BugReportController extends AbstractController
{
    /**
     * @Route("/bugreport", name="bug_report")
     */
    public function index(Validator $validator)
    {
        if ($validator->post()) {
            $validator->required('firstname', 'lastname', 'email', 'reason', 'message');
            if ($validator->check()) {
                if (!filter_var($validator->get('email'), FILTER_VALIDATE_EMAIL)) {
                    $validator->keep()->fail();
                    return $this->redirectToRoute('home');
                }
                $dataReport =
                    [
                        'firstname' => $validator->get('firstname'),
                        'lastname' => $validator->get('lastname'),
                        'email' => $validator->get('email'),
                        'subject' => $validator->get('reason'),
                        'body' => $validator->get('message')
                    ];
                $client = HttpClient::create();
                $responseBug = $client->request('POST', getenv('API_URL') . '/bugrepports', [
                    'headers' => ['content_type' => 'application/json'],
                    'body' => json_encode($dataReport)
                ]);
                if ($responseBug->getStatusCode() == 201 || $responseBug->getStatusCode() == 200) {
                    $validator->success('bug.success_send');
                    return $this->redirectToRoute('home');
                }
                $validator->keep()->fail();
                return $this->redirectToRoute('home');
            }
            $validator->keep()->fail();
            return $this->redirectToRoute('home');
        }
        $validator->keep()->fail();
        return $this->redirectToRoute('home');
    }
}
