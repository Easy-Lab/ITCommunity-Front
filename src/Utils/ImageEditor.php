<?php

namespace App\Utils;

use Symfony\Component\Debug\Exception\FatalErrorException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImageEditor
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function save($resource, $filename)
    {
        return file_put_contents($filename, stream_get_contents($resource));
    }

    public function crop($filename, $thumb_width, $thumb_height, $replace = false, $savepath = null)
    {
        try {
            $meta = @getimagesize($filename);
            if(!$meta) return false;
            $image = null;

            switch($meta['mime']) {
                case 'image/jpg':
                case 'image/jpeg':
                    $output = exec("php -r \"imagecreatefromjpeg('$filename');\" 2>&1");
                    if (!empty($output)) return false;

                    $image = @imagecreatefromjpeg($filename);
                    break;
                case 'image/png':
                    $output = exec("php -r \"imagecreatefrompng('$filename');\" 2>&1");
                    if (!empty($output)) return false;

                    $image = @imagecreatefrompng($filename);
                    break;
                case 'image/gif':
                    $output = exec("php -r \"imagecreatefromgif('$filename');\" 2>&1");
                    if (!empty($output)) return false;

                    $image = @imagecreatefromgif($filename);
                    break;
            }

            if(!$image || is_null($image)) return false;

            $width = imagesx($image);
            $height = imagesy($image);

            $original_aspect = $width / $height;
            $thumb_aspect = $thumb_width / $thumb_height;
            if ( $original_aspect >= $thumb_aspect ) {
                if ($thumb_height>$thumb_width){
                    $new_height = $thumb_height;
                    $new_width = $width / ($height / $thumb_height);
                }
                else{
                $new_height = $thumb_height;
                $new_width = $width / ($height / $thumb_height);
                }
            }
            else {
                $new_width = $thumb_width;
                $new_height = $height / ($width / $thumb_width);
            }

            $thumb = imagecreatetruecolor( $thumb_width, $thumb_height );

                imagecopyresampled(
                    $thumb, $image,
                    0 - ($new_width - $thumb_width) /2,
                    0 - ($new_height - $thumb_height)/2,
                    0, 0,
                    $new_width, $new_height,
                    $width, $height
                );


            if($replace) {
                switch($meta['mime']) {
                    case 'image/jpg':
                    case 'image/jpeg':
                        imagejpeg($thumb, $filename);
                        break;
                    case 'image/png':
                        imagepng($thumb, $filename);
                        break;
                    case 'image/gif':
                        imagegif($thumb, $filename);
                        break;
                }
            }
            elseif(!is_null($savepath)) {
                switch($meta['mime']) {
                    case 'image/jpg':
                    case 'image/jpeg':
                        imagejpeg($thumb, $savepath);
                        break;
                    case 'image/png':
                        imagepng($thumb, $savepath);
                        break;
                    case 'image/gif':
                        imagegif($thumb, $savepath);
                        break;
                }
            }


            return $thumb;
        }
        catch(FatalErrorException $e) {
            return false;
        }
        catch(\Exception $e) {
            return false;
        }
        catch(\Throwable $e) {
            return false;
        }
    }

    public function resize($filename, $max_width, $max_height, $replace = false)
    {
        $image = fopen($filename, 'r');
        $meta = getimagesize($filename);

        $orig_width = $meta[0];
        $orig_height = $meta[1];

        $width = $orig_width;
        $height = $orig_height;

        if ($height > $max_height) {
            $width = ($max_height / $height) * $width;
            $height = $max_height;
        }

        if ($width > $max_width) {
            $height = ($max_width / $width) * $height;
            $width = $max_width;
        }

        $image_p = imagecreatetruecolor($width, $height);

        switch($meta['mime']) {
            case 'image/jpg':
            case 'image/jpeg':
                $image = imagecreatefromjpeg($filename);
                break;
            case 'image/png':
                $image = imagecreatefrompng($filename);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($filename);
                break;
        }

        // $ratio = max($width / $orig_width, $height / $orig_height);

        imagecopyresampled(
            $image_p, $image,
            0, 0, ($orig_width - $width) / 2, ($orig_height - $height) / 2,
            $width, $height, $orig_width, $orig_height
        );

        if($replace) {
            switch($meta['mime']) {
                case 'image/jpg':
                case 'image/jpeg':
                    imagejpeg($image_p, $filename);
                    break;
                case 'image/png':
                    imagepng($image_p, $filename);
                    break;
                case 'image/gif':
                    imagegif($image_p, $filename);
                    break;
            }
        }

        return $image_p;
    }
}