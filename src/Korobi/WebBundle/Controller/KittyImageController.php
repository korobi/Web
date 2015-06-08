<?php

namespace Korobi\WebBundle\Controller;

use Korobi\WebBundle\Document\KittyImage;

class KittyImageController extends BaseController {

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homeAction() {
        $dbImages = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:KittyImage')
            ->findAllImages()
            ->toArray(false);

        // make sure we actually have a network
        if(empty($dbImages)) {
            throw $this->createNotFoundException();
        }

        $images = [];

        // create an entry for each image
        foreach($dbImages as $dbImage) {
            /** @var KittyImage $dbImage */

            $images[$dbImage->getImageId()] = [
                'image_id' => $dbImage->getImageId(),
                'source' => $dbImage->getUrl(),
                'tags' => implode(', ', $dbImage->getTags())
            ];
        }

        ksort($images, SORT_NATURAL | SORT_FLAG_CASE);

        return $this->render('KorobiWebBundle::kitty_image.html.twig', [
            'images' => $images
        ]);
    }
}
