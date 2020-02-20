<?php


namespace App\Controller\ambassador;


use App\Utils\Features;
use App\Utils\ImageEditor;
use App\Utils\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PictureController extends AbstractController
{
    /**
     * PictureController constructor.
     */
    protected $container;
    protected $session;

    public function __construct(ContainerInterface $container, SessionInterface $session)
    {
        $this->container=$container;
        $this->session=$session;

    }


    /**
     * @Route("/ambassador/picture/upload", name="ambassador_picture_upload")
     */
    public function upload(Request $request, Features $features, ImageEditor $imageEditor, Validator $validator)
    {
        if ($request->hasSession() && $this->session) {

            $id = $request->get('id');
            $type = $request->get('type');
            $display = $request->get('name');
            $height = $request->get('height');
            $width = $request->get('width');
            $full_type = $type;
            $type_n = 0;
            $matches = null;


            if (preg_match('/^environment_([0-9]+)$/', $type, $matches)) {
                $full_type = $type;
                $type = 'environment';
                $type_n = intval($matches[1]);

                if ($type_n < 0 || $type_n >= $features->get('environment.pictures.count')) {
                    return $this->json(['success' => false]);
                }
            }

            if (!empty($_POST['picture'])) {
                $base64 = explode(',', $_POST['picture']);

                if (count($base64) == 2) {
                    $data = base64_decode($base64[1]);
                    $handler = tmpfile();
                    $meta = stream_get_meta_data($handler);
                    $filename = $meta['uri'];

                    if (file_put_contents($filename, $data)) {

                        $meta = getimagesize($filename);

                        if ($meta && in_array($meta['mime'], ['image/jpg', 'image/jpeg', 'image/png'])) {

                            $uploads_dir = $this->getParameter('kernel.project_dir') . '/public/uploads';
                            $target_filename = sha1(microtime(true)) . date('Ymd') . image_type_to_extension($meta[2]);
                            $target_dir = substr($target_filename, 0, 2);

                            $fullpath = "$uploads_dir/$target_dir/$target_filename";
                            $fullpath_thumb = "$uploads_dir/thumbs/$target_dir/$target_filename";
                            if (!is_dir("$uploads_dir/$target_dir")) {
                                @mkdir("$uploads_dir/$target_dir", 0777, true);
                            }
                            if (!is_dir("$uploads_dir/thumbs/$target_dir")) {
                                @mkdir("$uploads_dir/thumbs/$target_dir", 0777, true);
                            }

                            if (@file_put_contents($fullpath, $data)) {

                                if ($type == 'profile_picture') {

                                    $imageEditor->crop($fullpath, 158, 158, true);

                                    $clientProfilePicture = HttpClient::create(['headers' => [
                                        'Content-Type' => 'application/json',
                                        'Authorization' => 'Bearer ' . $this->session->get('token')
                                    ]]);

                                    $dataProfilePicture = [
                                        'name' => $full_type,
                                        'path' => "$target_dir/$target_filename"
                                    ];

                                    $responsePictureProfile = $clientProfilePicture->request('POST', getenv('API_URL') . '/picture', [
                                        'body' => json_encode($dataProfilePicture)
                                    ]);
                                    $statusCodeResponsePictureProfile = $responsePictureProfile->getStatusCode();

                                    if ($statusCodeResponsePictureProfile == 201) {
                                        return $this->json([
                                            'success' => true,
                                        ]);
                                    }

                                    return $this->json([
                                        'success' => false,
                                        'code' => $statusCodeResponsePictureProfile
                                    ]);
                                } else {
                                    if ($width > $height) {
                                        $imageEditor->crop($fullpath, 720, 480, true);
                                        $imageEditor->crop($fullpath, 270, 180, false, $fullpath_thumb);
                                    } elseif ($height >= $width) {
                                        $imageEditor->crop($fullpath, 480, 720, true);
                                        $imageEditor->crop($fullpath, 180, 270, false, $fullpath_thumb);
                                    } else {
                                        $imageEditor->crop($fullpath, 720, 480, true);
                                        $imageEditor->crop($fullpath, 270, 180, false, $fullpath_thumb);
                                    }

                                    $clientEnvironmentPicture = HttpClient::create(['headers' => [
                                        'Content-Type' => 'application/json',
                                        'Authorization' => 'Bearer ' . $this->session->get('token')
                                    ]]);

                                    $dataEnvironmentPicture = [
                                        'name' => $full_type,
                                        'path' => "$target_dir/$target_filename"
                                    ];

                                    $responsePictureEnvironment = $clientEnvironmentPicture->request('POST', getenv('API_URL') . '/picture', [
                                        'body' => json_encode($dataEnvironmentPicture)
                                    ]);
                                    $statusCodeResponsePictureEnvironment = $responsePictureEnvironment->getStatusCode();

                                    if ($statusCodeResponsePictureEnvironment == 201) {
                                        return $this->json([
                                            'success' => true,
                                        ]);
                                    }

                                    return $this->json([
                                        'success' => false,
                                    ]);
                                }
                            }
                        }
                    }
                    fclose($handler);
                }
            }

            return $this->json(['success' => false]);
        }
        $validator->fail('no_session');
        return $this->redirectToRoute('login');
    }
}
