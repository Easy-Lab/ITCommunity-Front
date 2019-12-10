<?php


namespace App\Controller\discover;

use App\Utils\Features;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DiscoverController extends AbstractController
{
    /**
     * @Route("/devenir-un-utilisateur", name="discover")
     */
    public function index(Request $request)
    {
        $actual_route = $request->get('actual_route', 'discover');
        return $this->render('discover/user.html.twig', [
            'actual_route'=>$actual_route,
        ]);
    }

}
