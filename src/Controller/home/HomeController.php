<?php


namespace App\Controller\home;

use App\Utils\Features;
use App\Utils\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Features $features, Request $request){
        $client = HttpClient::create();
        $responseCpu = $client->request('GET','http://gpu-cpu-api.atcreative.fr/api/cpu');
        $statusCodeCpu = $responseCpu->getStatusCode();
        if ($statusCodeCpu == 200){
            $content = $responseCpu->toArray();
        }else{
            $content = null;
        }
        $responseGpu = $client->request('GET','http://gpu-cpu-api.atcreative.fr/api/gpu');
        $statusCodeGpu = $responseGpu->getStatusCode();
        if ($statusCodeGpu == 200){
            $contentGpu = $responseGpu->toArray();
        }else{
            $contentGpu = null;
        }
        $map = $features->get('map.search');
        $zipcode_maxlength = $features->get('home.zipcode.maxlength');
        $actual_route = $request->get('actual_route', 'home');
        return $this->render('home/index.html.twig', [
            'map'=>$map,
            'zipcode_maxlength'=>$zipcode_maxlength,
            'products_cpu'=>array_reverse($content),
            'products_gpu'=>array_reverse($contentGpu),
            'actual_route'=>$actual_route
        ]);
    }

    /**
     * @Route("/sitemap", name="sitemap")
     */
    public function sitemap(Request $request,Validator $validator)
    {
        return $this->render('main/sitemap.html.twig', [
            'validator' => $validator,
            'meta_key' => 'page.sitemap',
        ]);
    }

    /**
     * @Route("/sitemap.xml", name="sitemapxml", options={"i18n": false})
     */
    public function sitemapAction()
    {
        $paths = [];
        $paths[] = ['home', []];
        $paths[] = ['discover', []];
        $paths[] = ['static_page', ['slug' => 'cgv']];
        $paths[] = ['static_page', ['slug' => 'personal_data']];
        $paths[] = ['static_page', ['slug' => 'faq']];
        $paths[] = ['static_page', ['slug' => 'cgu']];
        $response = $this->render('sitemap.xml.twig', [
            'paths' => $paths
        ]);
        $response->headers->set('Content-Type', 'application/xml');
        return $response;
    }

    /**
     * @Route("/contact-us", name="contact_us")
     */
    public function contactUs(Validator $validator, Request $request)
    {
//        $reasons = $this->getDoctrine()->getRepository(ContactUsReason::class)->findAll(['id' => 'ASC']);
//
//        if ($validator->post())
//        {
//            if ($features->get('ambassador.contact.captcha')) {
//                $recaptcha = new \ReCaptcha\ReCaptcha($this->getParameter('google_recaptcha_secret'));
//                if (!$recaptcha->verify($request->get('g-recaptcha-response'), $request->getClientIp())->isSuccess()) {
//                    $validator->fail('form.google_recaptcha_failed');
//                }
//            }
//
//            $validator
//                ->required('firstname', 'lastname', 'email', 'message', 'reason')
//                ->email('email');
//
//            if($validator->get('reason') !== null) {
//                $reason = $this->getDoctrine()->getRepository(ContactUsReason::class)->find(
//                    $validator->get('reason')
//                );
//                if(!$reason) $validator->error('reason', 'in_array', true);
//            }
//
//            if ($request->get('phone')) {
//                $validator->phone('phone');
//            }
//
//            if ($validator->check())
//            {
//                if ($validator->get('texte') != "") {
//                    $validator->success('form.spam');
//                    return $this->redirectToRoute('contact_us');
//                }
//
//                if ($validator->DatacheckSpam($validator->get('firstname')) != null) {
//                    $error = $validator->DatacheckSpam($validator->get('firstname'));
//                    $validator->keep()->fail('form.' . $error);
//                    return $this->redirectToRoute('contact_us');
//
//                }
//                if ($validator->DatacheckSpam($validator->get('lastname')) != null) {
//                    $error = $validator->DatacheckSpam($validator->get('lastname'));
//                    $validator->keep()->fail('form.' . $error);
//                    return $this->redirectToRoute('contact_us');
//
//                }
//                if ($validator->DatacheckSpam($validator->get('email')) != null) {
//                    $error = $validator->DatacheckSpam($validator->get('email'));
//                    $validator->keep()->fail('form.' . $error);
//                    return $this->redirectToRoute('contact_us');
//
//                }
//                if ($validator->DatacheckSpam($validator->get('message')) != null) {
//                    $error = $validator->DatacheckSpam($validator->get('message'));
//                    $validator->keep()->fail('form.' . $error);
//                    return $this->redirectToRoute('contact_us');
//
//                }
//
//                // Store
//                $newContactUs = $this->getDoctrine()->getRepository(ContactUs::class)->blankInstance(
//                    $request->get('firstname'),
//                    $request->get('lastname'),
//                    ($request->get('phone')) ? $request->get('phone') : null,
//                    $request->get('email'),
//                    $request->get('message'),
//                    $reason
//                );
//
//                $userService->setCrypted($newContactUs, 'firstname', $request->get('firstname'));
//                $userService->setCrypted($newContactUs, 'lastname', $request->get('lastname'));
//                $userService->setCrypted($newContactUs, 'email', strtolower($request->get('email')));
//                if($request->get('phone')) $userService->setCrypted($newContactUs, 'phone', $request->get('phone'));
//
//                $em = $this->getDoctrine()->getManager();
//                $em->persist($newContactUs);
//                $em->flush();
//
//                // Send e-mail
//                $mailService->sendContactUsMail(
//                    $request->get('firstname'),
//                    $request->get('lastname'),
//                    ($request->get('phone')) ? $request->get('phone') : null,
//                    $request->get('email'),
//                    $request->get('message'),
//                    $reason
//                );
//
//                $validator->success('contact.contact_us.success_send');
//
//                return $this->redirectToRoute('home');
//            }
//
//            $validator->fail();
//        }

        $actual_route = $request->get('actual_route', 'contact_us');

        return $this->render('main/contact_us.html.twig', [
            'validator' => $validator,
            'actual_route'=>$actual_route
//            "reasons" => $reasons,
//            'openGarden'=>$features->get('environment.events.enabled'),
//
//            'meta_key' => 'page.contact_us',
//            'dealerHusqSearch'=>$features->get('dealer.search.husq')

        ]);
    }
}
