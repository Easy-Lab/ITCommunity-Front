<?php


namespace App\Controller\home;


use App\Service\UserService;
use App\Utils\Features;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

class MappingController extends AbstractController
{
    protected function prepareMarker(array $user, TranslatorInterface $translator)
    {
        $package = new Package(new EmptyVersionStrategy());

        $pin = 'pin.png';
        return [
            'icon' => $translator->trans("global.images_url").'/images/'.$pin,
            'latitude' => $user['latitude'],
            'longitude' => $user['longitude'],
            'id' => $user['username'],

        ];
    }

    protected function prepareMarkerPopUp(array $user, TranslatorInterface $translator)
    {
        $pin = 'pin.png';

        return [
            'icon' => $translator->trans("global.images_url").'/images/'.$pin,
            'latitude' => $user['latitude'],
            'longitude' => $user['longitude'],
            'id'=>$user['username'],
            'popup' => $this->renderView('mapping/popup/user.html.twig', [
                'user' => $user,
            ])
        ];
    }

    protected function prepareResult(array $user, $idx=0)
    {

            return [
                'html' => $this->renderView('mapping/result/user.html.twig', [
                    'idx' => $idx,
                    'user' => $user,
                    'url'=>"https://test.com",

                ])
            ];
    }

    /**
     * @Route("/mapping/ambassadors", name="mapping_ambassadors")
     */
    public function ambassadors(Request $request, Features $features, TranslatorInterface $translator)
    {

        // Récupérations des paramêtres de la recette.
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $zipcode = $request->get('zipcode');
        $page = $request->get('page', null);

        // Check de la latitude et de la longitude
        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            $latitude = $longitude = null;
        }

        // Check du zipcode
        if (!preg_match('/^(([0-9]{2})|([0-9]{5}))$/', $zipcode)) {
            $zipcode = null;
        }

        // Markers
        $markers = [];

        if (is_null($page) || $page == 1) {
            $client = HttpClient::create(['headers' => [
                'Content-Type' => 'application/json',
            ]]);
            $response = $client->request('GET', getenv('API_URL') . '/users?user_filter[step]=3'
            );
            $statusCode = $response->getStatusCode();
            if ($statusCode == 200) {
                $users = $response->toArray();

            } else {
                $users = null;
            }

            //dump($ambassadors);
            // if (!is_null($zipcode) && !is_null($family_id) && !is_null($subfamily_id)){

            if ($users) {
                foreach ($users['users'] as $user) {

                    $markers[] = $this->prepareMarker($user, $translator);

                }
            }
            //}
            // else{
            /* foreach($ambassadors as $ambassador)
             {
                 $in_zone = true;

                 if($features->get('dealer.zones.enabled') && count($zone) > 0) {

                     if(!$features->get("dealer.concession.enabled")){
                         $in_zone = in_array($ambassador->getZipcode(), $zone) || in_array(substr($ambassador->getZipcode(), 0, 2), $zone);
                         if(!$in_zone && $features->get('dealer.zones.hide_outside')) {
                             continue;
                         }

                     } else{
                         //Les concessions sont activés...tryba
                         //$concessionId (l'id de la concession au plein coeur de la recherche ???)
                         if(!empty($ambassador->getDealer())){
                             $in_zone = in_array($ambassador->getDealer()->getIdConcession(), $concessionIds);
                         }
                     }

                 }
                 $markers[] = $this->prepareMarker2($ambassador, 'ambassador', $in_zone);

             }
        // }*/
        }


        // Résultats de la recherche
        $results = [];

            $response = $client->request('GET', getenv('API_URL') . '/users?expand=profile,reviews,pictures&user_filter[step]=3&limit=50'
            );
            $statusCode = $response->getStatusCode();
            if ($statusCode == 200) {
                $users = $response->toArray();

            } else {
                $users = null;
            }
            if ($users) {
                foreach ($users['users'] as $idx => $user) {

                    $results[] = $this->prepareResult($user, $idx);

                }
            }

        //dump($markers);
        //dump($results);

        return $this->json([
            'markers' => $markers,
            'results' => $results,
            'users'=>$users
        ]);
    }

    /**
     * @Route("/mapping/popup_amb", name="mapping_popup_amb")
     */
    public function popupAmb(Request $request, TranslatorInterface $translator)
    {
        $client = HttpClient::create(['headers' => [
            'Content-Type' => 'application/json',
        ]]);
        $response = $client->request('GET', getenv('API_URL') . '/users?expand=profile,reviews,pictures&user_filter[username]='.$request->get('id')
        );
        $statusCode = $response->getStatusCode();
        if ($statusCode == 200) {
            $user = $response->toArray();

        } else {
            $user = null;
        }

        $markers = $this->prepareMarkerPopUp($user, $translator);

        return new JsonResponse($markers);
    }

}
