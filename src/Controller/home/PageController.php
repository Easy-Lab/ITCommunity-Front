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
        $actual_route = $request->get('actual_route', 'cgu');

        return $this->render('main/page.html.twig', [
            'user' => $userService->getUser(),
            'slug'=>$slug,
            'google_analytics_id' => getenv("ANALYTICS_KEY"),
            'actual_route'=>$actual_route
        ]);
    }
}
