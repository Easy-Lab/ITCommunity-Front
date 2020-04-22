<?php


namespace App\Controller\contact;


use App\Utils\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/user/contact-conseil/{username}", name="user_contact_conseil")
     */
    public function contactConseil($username, Validator $validator)
    {
        if ($validator->post()) {
            if ($validator->get('texte') != "") {
                $validator->success('form.spam');
                return $this->redirectToRoute('user_unknow_profile', array('username' => $username));
            }
            $validator->required('message', 'type');
            if ($validator->check()) {
                if ($validator->get('type')) {
                    if ($validator->get('conseil_notification') == 'public') $type = false;
                    if ($validator->get('conseil_notification') == 'private') $type = true;
                    if ($validator->get('emailConseil') && $validator->get('emailConseilOnly') === '') {
                        if (!filter_var($validator->get('emailConseil'), FILTER_VALIDATE_EMAIL)) {
                            $validator->keep()->fail();
                            return $this->redirectToRoute('user_unknow_profile', array('username' => $username));
                        }
                        if ($validator->DatacheckSpam($validator->get('firstname')) != null) {
                            $error = $validator->DatacheckSpam($validator->get('firstname'));
                            $validator->keep()->fail('form.' . $error);
                            return $this->redirectToRoute('user_unknow_profile', array('username' => $username));

                        }
                        if ($validator->DatacheckSpam($validator->get('lastname')) != null) {
                            $error = $validator->DatacheckSpam($validator->get('lastname'));
                            $validator->keep()->fail('form.' . $error);
                            return $this->redirectToRoute('user_unknow_profile', array('username' => $username));

                        }
                        if ($validator->DatacheckSpam($validator->get('emailConseil')) != null) {
                            $error = $validator->DatacheckSpam($validator->get('emailConseil'));
                            $validator->keep()->fail('form.' . $error);
                            return $this->redirectToRoute('user_unknow_profile', array('username' => $username));

                        }
                        if ($validator->DatacheckSpam($validator->get('message')) != null) {
                            $error = $validator->DatacheckSpam($validator->get('message'));
                            $validator->keep()->fail('form.' . $error);
                            return $this->redirectToRoute('user_unknow_profile', array('username' => $username));

                        }
                        $dataContact = [
                            'firstname' => $validator->get('firstname'),
                            'lastname' => $validator->get('lastname'),
                            'email' => $validator->get('emailConseil')
                        ];
                        $client = HttpClient::create();
                        $responseContact = $client->request('POST', getenv('API_URL') . '/contacts', [
                            'headers' => ['content_type' => 'application/json'],
                            'body' => json_encode($dataContact)
                        ]);
                        if ($responseContact->getStatusCode() == 200 || $responseContact->getStatusCode() == 201) {
                            $dataMessage = [
                                'email' => $validator->get('emailConseil'),
                                'username' => $username,
                                'type' => $type,
                                'question' => $validator->get('message')
                            ];
                            $responseMessage = $client->request('POST', getenv('API_URL') . '/messages', [
                                'headers' => ['content_type' => 'application/json'],
                                'body' => json_encode($dataMessage)
                            ]);
                            if ($responseMessage->getStatusCode() == 200 || $responseMessage->getStatusCode() == 201) {
                                $validator->success('contact.mail.success_send');
                                return $this->redirectToRoute('user_unknow_profile', array('username' => $username));
                            }
                        }
                    }
                    if ($validator->get('emailConseil') === '' && $validator->get('emailConseilOnly')) {
                        if (!filter_var($validator->get('emailConseilOnly'), FILTER_VALIDATE_EMAIL)) {
                            $validator->keep()->fail();
                            return $this->redirectToRoute('user_unknow_profile', array('username' => $username));
                        }
                        if ($validator->DatacheckSpam($validator->get('emailConseilOnly')) != null) {
                            $error = $validator->DatacheckSpam($validator->get('emailConseilOnly'));
                            $validator->keep()->fail('form.' . $error);
                            return $this->redirectToRoute('user_unknow_profile', array('username' => $username));

                        }
                        if ($validator->DatacheckSpam($validator->get('message')) != null) {
                            $error = $validator->DatacheckSpam($validator->get('message'));
                            $validator->keep()->fail('form.' . $error);
                            return $this->redirectToRoute('user_unknow_profile', array('username' => $username));

                        }

                        $dataMessage = [
                            'email' => $validator->get('emailConseilOnly'),
                            'username' => $username,
                            'type' => $type,
                            'question' => $validator->get('message')
                        ];
                        $client = HttpClient::create();
                        $responseMessage = $client->request('POST', getenv('API_URL') . '/messages', [
                            'headers' => ['content_type' => 'application/json'],
                            'body' => json_encode($dataMessage)
                        ]);
                        if ($responseMessage->getStatusCode() == 200 || $responseMessage->getStatusCode() == 201) {
                            $validator->success('contact.mail.success_send');
                            return $this->redirectToRoute('user_unknow_profile', array('username' => $username));
                        }
                    }
                    $validator->keep()->fail();
                    return $this->redirectToRoute('user_unknow_profile', array('username' => $username));
                }
                $validator->keep()->fail();
                return $this->redirectToRoute('user_unknow_profile', array('username' => $username));
            }
            $validator->keep()->fail();
            return $this->redirectToRoute('user_unknow_profile', array('username' => $username));
        }
        $validator->keep()->fail();
        return $this->redirectToRoute('user_unknow_profile', array('username' => $username));
    }

    /**
     * @Route("/user/contact-demo/{username}", name="user_contact_demo")
     */
    public function contactDemo($username, Validator $validator)
    {
        if ($validator->post()) {
            if ($validator->get('texte') != "") {
                $validator->success('form.spam');
                return $this->redirectToRoute('user_unknow_profile', array('username' => $username));
            }
            $validator->required('message', 'type');
            if ($validator->check()) {
                if ($validator->get('type')) {
                    if ($validator->get('demo_notification') == 'public') $type = false;
                    if ($validator->get('demo_notification') == 'private') $type = true;
                    if ($validator->get('emailDemo') && $validator->get('emailDemoOnly') === '') {
                        if (!filter_var($validator->get('emailDemo'), FILTER_VALIDATE_EMAIL)) {
                            $validator->keep()->fail();
                            return $this->redirectToRoute('user_unknow_profile', array('username' => $username));
                        }
                        if ($validator->DatacheckSpam($validator->get('firstname')) != null) {
                            $error = $validator->DatacheckSpam($validator->get('firstname'));
                            $validator->keep()->fail('form.' . $error);
                            return $this->redirectToRoute('user_unknow_profile', array('username' => $username));

                        }
                        if ($validator->DatacheckSpam($validator->get('lastname')) != null) {
                            $error = $validator->DatacheckSpam($validator->get('lastname'));
                            $validator->keep()->fail('form.' . $error);
                            return $this->redirectToRoute('user_unknow_profile', array('username' => $username));

                        }
                        if ($validator->DatacheckSpam($validator->get('emailDemo')) != null) {
                            $error = $validator->DatacheckSpam($validator->get('emailDemo'));
                            $validator->keep()->fail('form.' . $error);
                            return $this->redirectToRoute('user_unknow_profile', array('username' => $username));

                        }
                        if ($validator->DatacheckSpam($validator->get('message')) != null) {
                            $error = $validator->DatacheckSpam($validator->get('message'));
                            $validator->keep()->fail('form.' . $error);
                            return $this->redirectToRoute('user_unknow_profile', array('username' => $username));

                        }
                        $dataContact = [
                            'firstname' => $validator->get('firstname'),
                            'lastname' => $validator->get('lastname'),
                            'email' => $validator->get('emailDemo')
                        ];
                        $client = HttpClient::create();
                        $responseContact = $client->request('POST', getenv('API_URL') . '/contacts', [
                            'headers' => ['content_type' => 'application/json'],
                            'body' => json_encode($dataContact)
                        ]);
                        if ($responseContact->getStatusCode() == 200 || $responseContact->getStatusCode() == 201) {
                            $dataMessage = [
                                'email' => $validator->get('emailDemo'),
                                'username' => $username,
                                'type' => $type,
                                'question' => $validator->get('message')
                            ];
                            $responseMessage = $client->request('POST', getenv('API_URL') . '/messages', [
                                'headers' => ['content_type' => 'application/json'],
                                'body' => json_encode($dataMessage)
                            ]);
                            if ($responseMessage->getStatusCode() == 200 || $responseMessage->getStatusCode() == 201) {
                                $validator->success('contact.mail.success_send');
                                return $this->redirectToRoute('user_unknow_profile', array('username' => $username));
                            }
                        }
                    }
                    if ($validator->get('emailDemo') === '' && $validator->get('emailDemoOnly')) {
                        if (!filter_var($validator->get('emailDemoOnly'), FILTER_VALIDATE_EMAIL)) {
                            $validator->keep()->fail();
                            return $this->redirectToRoute('user_unknow_profile', array('username' => $username));
                        }
                        if ($validator->DatacheckSpam($validator->get('emailDemoOnly')) != null) {
                            $error = $validator->DatacheckSpam($validator->get('emailDemoOnly'));
                            $validator->keep()->fail('form.' . $error);
                            return $this->redirectToRoute('user_unknow_profile', array('username' => $username));

                        }
                        if ($validator->DatacheckSpam($validator->get('message')) != null) {
                            $error = $validator->DatacheckSpam($validator->get('message'));
                            $validator->keep()->fail('form.' . $error);
                            return $this->redirectToRoute('user_unknow_profile', array('username' => $username));

                        }
                        $client = HttpClient::create();
                        $dataMessage = [
                            'email' => $validator->get('emailDemoOnly'),
                            'username' => $username,
                            'type' => $type,
                            'question' => $validator->get('message')
                        ];
                        $responseMessage = $client->request('POST', getenv('API_URL') . '/messages', [
                            'headers' => ['content_type' => 'application/json'],
                            'body' => json_encode($dataMessage)
                        ]);
                        if ($responseMessage->getStatusCode() == 200 || $responseMessage->getStatusCode() == 201) {
                            $validator->success('contact.mail.success_send');
                            return $this->redirectToRoute('user_unknow_profile', array('username' => $username));
                        }
                    }
                    $validator->keep()->fail();
                    return $this->redirectToRoute('user_unknow_profile', array('username' => $username));
                }
                $validator->keep()->fail();
                return $this->redirectToRoute('user_unknow_profile', array('username' => $username));
            }
            $validator->keep()->fail();
            return $this->redirectToRoute('user_unknow_profile', array('username' => $username));
        }
        $validator->keep()->fail();
        return $this->redirectToRoute('user_unknow_profile', array('username' => $username));
    }

}
