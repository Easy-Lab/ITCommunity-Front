<?php

namespace App\Controller\home;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class PageController extends AbstractController
{
    /**
     * @Route("/page/{slug}", name="static_page")
     */
    public function index($slug, Request $request, UserService $userService)
    {
        $lang = $request->getLocale();
//        $staticPage = $this->getDoctrine()->getRepository(StaticPage::class)->findOneBySlug($slug);
//
//        //Dans le cas de la faq, il peut y avoir une faq-ambassador et une faq-dealer
//        if($slug == "faq"){
//
//            $specialFAQ =  $this->getDoctrine()->getRepository(StaticPage::class)->getSpecialFaq($this->getUser());
//            if(!empty($specialFAQ))
//                $staticPage = $specialFAQ;
//        }
//
//        $staticPageContent = $this->getDoctrine()->getRepository(StaticPageContent::class)->findOneBy(array(
//            'lang' => $lang,
//            'staticPage' => $staticPage
//        ));
//        //Cas particulier de la faq spéciale
//
//
//        if (!$staticPageContent) {
//            $staticPageContent = $this->getDoctrine()->getRepository(StaticPageContent::class)->findOneBy(array(
//                'lang' => "fr",
//                'staticPage' => $staticPage
//            ));
//        }
//        if (!$staticPageContent) throw $this->createNotFoundException();

        return $this->render('page/index.html.twig', [
            'user' => $userService->getUser(),
            'google_analytics_id' => getenv("ANALYTICS_KEY"),
//            'staticPageContent' => $staticPageContent,
//            'title' => $staticPageContent->getTitle(),

        ]);
    }
}
