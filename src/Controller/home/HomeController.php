<?php


namespace App\Controller\home;

use App\Service\UserService;
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
    public function index(Features $features, Request $request, UserService $userService){
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
            'actual_route'=>$actual_route,
            'user'=>$userService->getUser(),
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
    public function contactUs(Validator $validator, Request $request, UserService $userService)
    {
        if ($validator->post())
        {

            $validator
                ->required('firstname', 'lastname', 'email', 'message', 'reason')
                ->email('email');


            if ($request->get('phone')) {
                $validator->phone('phone');
            }

            if ($validator->check())
            {
                if ($validator->get('texte') != "") {
                    $validator->success('form.spam');
                    return $this->redirectToRoute('contact_us');
                }

                if ($validator->DatacheckSpam($validator->get('firstname')) != null) {
                    $error = $validator->DatacheckSpam($validator->get('firstname'));
                    $validator->keep()->fail('form.' . $error);
                    return $this->redirectToRoute('contact_us');

                }
                if ($validator->DatacheckSpam($validator->get('lastname')) != null) {
                    $error = $validator->DatacheckSpam($validator->get('lastname'));
                    $validator->keep()->fail('form.' . $error);
                    return $this->redirectToRoute('contact_us');

                }
                if ($validator->DatacheckSpam($validator->get('email')) != null) {
                    $error = $validator->DatacheckSpam($validator->get('email'));
                    $validator->keep()->fail('form.' . $error);
                    return $this->redirectToRoute('contact_us');

                }
                if ($validator->DatacheckSpam($validator->get('reason')) != null) {
                    $error = $validator->DatacheckSpam($validator->get('reason'));
                    $validator->keep()->fail('form.' . $error);
                    return $this->redirectToRoute('contact_us');

                }
                if ($validator->DatacheckSpam($validator->get('message')) != null) {
                    $error = $validator->DatacheckSpam($validator->get('message'));
                    $validator->keep()->fail('form.' . $error);
                    return $this->redirectToRoute('contact_us');

                }
                $dataContact =
                    [
                        'firstname'=>$validator->get('firstname'),
                        'lastname'=>$validator->get('lastname'),
                        'email'=>$validator->get('email'),
                        'phone'=>is_null($validator->get('phone')) ? null : $validator->get('phone'),
                        'subject'=>$validator->get('reason'),
                        'body'=>$validator->get('message')
                    ];
                $client = HttpClient::create();
                $responseContact = $client->request('POST', getenv('API_URL') . '/contactforms', [
                    'headers' => ['content_type' => 'application/json'],
                    'body' => json_encode($dataContact)
                ]);
                if ($responseContact->getStatusCode() == 200 || $responseContact->getStatusCode() == 201)
                {
                    $validator->success('form.success');
                    return $this->redirectToRoute('contact_us');
                }
                $validator->keep()->fail('form.errorApi');
                return $this->redirectToRoute('contact_us');
            }
            $validator->keep()->fail('form.error');
            return $this->redirectToRoute('contact_us');
        }

        $actual_route = $request->get('actual_route', 'contact_us');

        return $this->render('main/contact_us.html.twig', [
            'validator' => $validator,
            'actual_route'=>$actual_route,
            'user'=>$userService->getUser(),
        ]);
    }
}
