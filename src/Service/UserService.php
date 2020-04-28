<?php


namespace App\Service;


use App\Utils\Features;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class UserService
{
    protected $session;
    protected $features;

    /**
     * UserService constructor.
     * @param $session
     */
    public function __construct(SessionInterface $session, Features $features)
    {
        $this->session = $session;
        $this->features = $features;
    }

    public function getUser($token = null, $username = null)
    {
        if ($this->session) {
            if (!$token){
                $token = $this->session->get('token');
            }
            if (!$username){
                $username = $this->session->get('username');
            }
            $client = HttpClient::create(['headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]]);
            $response = $client->request('GET', getenv('API_URL') . '/users/' . $username
            );
            $statusCode = $response->getStatusCode();
            if ($statusCode == 200) {
                $data = $response->toArray();
                if (array_key_exists('firstname',$data))
                {
                    return $data;
                }else
                    {
                    return null;
                }

            } else {
                return null;
            }
        }
        return null;
    }

    public function getPictures()
    {
        if ($this->session) {
            $client = HttpClient::create(['headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->session->get('token')
            ]]);
            $response = $client->request('GET', getenv('API_URL') . '/users/' . $this->session->get('username') . '/pictures'
            );
            $statusCode = $response->getStatusCode();
            if ($statusCode == 200) {
                return $response->toArray();

            } else {
                return null;
            }
        }
        return null;
    }

    public function addStep(int $step)
    {
        if ($this->session) {
            $client = HttpClient::create(['headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->session->get('token')
            ]]);
            $data =
                [
                    'step' => $step,
                ];
            $response = $client->request('PATCH', getenv('API_URL') . '/users/' . $this->session->get('username')
                , [
                    'headers' => ['content_type' => 'application/json'],
                    'body' => json_encode($data)
                ]);
            $statusCode = $response->getStatusCode();
            if ($statusCode == 200) {
                return true;

            } else {
                return false;
            }
        }
        return false;
    }

    public function getReviews()
    {
        if ($this->session) {
            $client = HttpClient::create(['headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->session->get('token')
            ]]);
            $response = $client->request('GET', getenv('API_URL') . '/users/' . $this->session->get('username') . '/reviews'
            );
            $statusCode = $response->getStatusCode();
            if ($statusCode == 200) {
                return $response->toArray();

            } else {
                return null;
            }
        }
        return null;
    }

    public function getInvitations()
    {
        if ($this->session) {
            $client = HttpClient::create(['headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->session->get('token')
            ]]);
            $response = $client->request('GET', getenv('API_URL') . '/affiliates');
            $statusCode = $response->getStatusCode();
            if ($statusCode == 200) {
                return $response->toArray();

            } else {
                return null;
            }
        }
        return null;
    }

    public function getMessages(string $username=null){
        if ($this->session) {
            if ($username){
                $client = HttpClient::create(['headers' => [
                    'Content-Type' => 'application/json',
                ]]);
                $response = $client->request('GET', getenv('API_URL') . '/users/' . $username . '/messages?expand=contact'
                );
                $statusCode = $response->getStatusCode();
                if ($statusCode == 200) {
                    return $response->toArray();

                } else {
                    return null;
                }
            }
            $client = HttpClient::create(['headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->session->get('token')
            ]]);
            $response = $client->request('GET', getenv('API_URL') . '/users/' . $this->session->get('username') . '/messages?expand=contact'
            );
            $statusCode = $response->getStatusCode();
            if ($statusCode == 200) {
                return $response->toArray();

            } else {
                return null;
            }
        }
        return null;
    }

    public function getEvaluations(string $username=null){
        if ($this->session) {
            if ($username){
                $client = HttpClient::create(['headers' => [
                    'Content-Type' => 'application/json',
                ]]);
                $response = $client->request('GET', getenv('API_URL') . '/users/' . $username . '/evaluations?expand=contact'
                );
                $statusCode = $response->getStatusCode();
                if ($statusCode == 200) {
                    return $response->toArray();

                } else {
                    return null;
                }
            }
            $client = HttpClient::create(['headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->session->get('token')
            ]]);
            $response = $client->request('GET', getenv('API_URL') . '/users/' . $this->session->get('username') . '/evaluations?expand=contact'
            );
            $statusCode = $response->getStatusCode();
            if ($statusCode == 200) {
                return $response->toArray();

            } else {
                return null;
            }
        }
        return null;
    }

    public function getRanking(){
        if ($this->session) {
            $client = HttpClient::create(['headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->session->get('token')
            ]]);
            $response = $client->request('GET', getenv('API_URL') . '/points'
            );
            $statusCode = $response->getStatusCode();
            if ($statusCode == 200) {
                return $response->toArray();

            } else {
                return null;
            }
        }
        return null;
    }

    public function getMyPoints(){
        if ($this->session) {
            $client = HttpClient::create(['headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->session->get('token')
            ]]);
            $response = $client->request('GET', getenv('API_URL') . '/users/my/points'
            );
            $statusCode = $response->getStatusCode();
            if ($statusCode == 200) {
                return $response->toArray();

            } else {
                return null;
            }
        }
        return null;
    }

    public function getProfilePicture()
    {
        $pictures = $this->getPictures();
        if ($pictures && !empty($pictures)) {
            foreach ($pictures as $picture) {
                if ($picture['name'] == 'profile_picture') {
                    return $picture;
                }
            }
            return null;
        }
        return null;
    }

    public function getEnvironmentPictures()
    {
        $pictures = $this->getPictures();
        $environment = [];
        if ($pictures && !empty($pictures)) {
            foreach ($pictures as $picture){
                if ($picture['name'] != 'profile_picture' && $picture['name'] != 'share_picture'){
                    $environment[] = $picture;
                }
            }
            return $environment;
        }
        return $environment;
    }

    public function getEnvironmentPictures0()
    {
        $pictures = $this->getPictures();
        if ($pictures && !empty($pictures)) {
            foreach ($pictures as $picture){
                if ($picture['name'] == 'environment_0'){
                    return $picture;
                }
            }
            return null;
        }
        return null;
    }

    public function getEnvironmentPictures1()
    {
        $pictures = $this->getPictures();
        if ($pictures && !empty($pictures)) {
            foreach ($pictures as $picture){
                if ($picture['name'] == 'environment_1'){
                    return $picture;
                }
            }
            return null;
        }
        return null;
    }

    public function getEnvironmentPictures2()
    {
        $pictures = $this->getPictures();
        if ($pictures && !empty($pictures)) {
            foreach ($pictures as $picture){
                if ($picture['name'] == 'environment_2'){
                    return $picture;
                }
            }
            return null;
        }
        return null;
    }

    public function getGpu()
    {
        $reviews = $this->getReviews();
        if ($reviews && !empty($reviews)) {
            foreach ($reviews as $review) {
                if ($review['type'] == 'gpu') {
                    return $review;
                }
            }
            return null;
        }
        return null;
    }

    public function getCpu()
    {
        $reviews = $this->getReviews();
        if ($reviews && !empty($reviews)) {
            foreach ($reviews as $review) {
                if ($review['type'] == 'cpu') {
                    return $review;
                }
            }
            return null;
        }
        return null;
    }

    public function getStructure(){
        $structure = [
            'header' => [
                'columns' => []
            ],
            'content' => []
        ];

        $header_columns_count = $this->features->get('profile.header.columns.count');
        for ($i = 1; $i <= $header_columns_count; $i++) {
            $elements = $this->features->get('profile.header.columns.' . $i);
            $column = [
                'elements' => []
            ];
            $column['title'] = $this->features->has("profile.header.columns.$i.title") ? $this->features->get("profile.header.columns.$i.title") : false;
            foreach ($elements as $slug) {
                $element = [];
                $element['slug'] = $slug;
                $element['type'] = $slug;
                $element['name'] = $slug;

                if (preg_match('/^(.*)\\/(.*)$/', $slug)) {
                    list($type, $name) = explode('/', $slug, 2);
                    $element['type'] = $type;
                    $element['name'] = $name;
                }
                $column['elements'][] = $element;
            }
            $structure['header']['columns'][] = $column;
        }

        $elements = $this->features->get('profile.content');
        foreach ($elements as $slug) {
            $element = [];
            $element['slug'] = $slug;

            if (preg_match('/^(.*)\\/(.*)$/', $slug)) {
                list($type, $name) = explode('/', $slug, 2);
                $element['type'] = $type;
                $element['name'] = $name;
            }
            $structure['content'][] = $element;
        }
        return $structure;
    }
}
