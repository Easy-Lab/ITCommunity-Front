<?php


namespace App\Controller\user;


use App\Service\UserService;
use App\Utils\Features;
use App\Utils\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    protected $features;
    protected $session;

    public function __construct(Features $features, SessionInterface $session)
    {
        $this->features = $features;
        $this->session = $session;
    }

    /**
     * @Route("/user/message", name="user_dashboard_question")
     */
    public function index(Validator $validator, Request $request, UserService $userService)
    {
        if ($request->hasSession() && $this->session)
        {
            $user = $userService->getUser();
            $profilePicture = null;
            $myPoints = 0;
            if ($user)
            {
                $profilePicture = $userService->getProfilePicture();
                $myPoints = $userService->getMyPoints();
            }
            $actual_route = $request->get('actual_route', 'user_dashboard_question');
            $messages = $userService->getMessages();
            $unanswered = [];
            $answered = [];
            foreach ($messages as $message)
            {
                if ($message['type'] == false)
                {
                    if (array_key_exists('answer',$message))
                    {
                        $answered[] = $message;
                    }else
                        {
                        $unanswered[] = $message;
                    }
                }
            }

        return $this->render('user/message/index.html.twig', [
            'validator' => $validator,
            'user'=>$user,
            'profilePicture'=>$profilePicture,
            'actual_route'=>$actual_route,
            'answered'=>$answered,
            'unanswered'=>$unanswered,
            'myPoints'=>$myPoints
        ]);

        }
        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/user/message/edit/{hash}", name="user_message_edit")
     */
    public function edit($hash, Validator $validator, Request $request)
    {
        if ($request->hasSession() && $this->session)
        {
            if ($validator->post())
            {
                $validator->required('answer');

                if ($validator->check())
                {

                    $client = HttpClient::create(['headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $this->session->get('token')
                    ]]);
                    $data =
                        [
                            'answer' => $validator->get('answer')
                        ];
                    $response = $client->request('PATCH', getenv('API_URL') . '/messages/answer/' . $hash
                        , [
                            'headers' => ['content_type' => 'application/json'],
                            'body' => json_encode($data)
                        ]);
                    $statusCode = $response->getStatusCode();
                    if ($statusCode == 200)
                    {
                        $validator->success('ambassador.message.success_create');
                    } else
                        {
                        $validator->keep()->fail();
                    }
                    return $this->redirectToRoute('user_dashboard_question');
                }
                $validator->keep()->fail();
            }
            return $this->redirectToRoute('user_dashboard_question');
        }
        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/user/message/delete/{hash}", name="user_message_delete")
     */
    public function delete($hash, Validator $validator, Request $request)
    {

    }
}
