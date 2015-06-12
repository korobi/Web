<?php

namespace Korobi\WebBundle\Controller;

use Korobi\WebBundle\Document\KittyImage;
use Korobi\WebBundle\Util\Camo;
use Korobi\WebBundle\Util\StringUtil;

class KittyImageController extends BaseController {

    /**
     * @var Camo
     */
    private $camo;

    /**
     * @param Camo $camo
     */
    public function __construct(Camo $camo) {
        $this->camo = $camo;
    }

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
        $videos = [];

        // create an entry for each image
        foreach($dbImages as $dbImage) {
            /** @var KittyImage $dbImage */

            $imageUrl = $dbImage->getUrl();
            $imageData = [
                'image_id' => $dbImage->getImageId(),
                'source' => $this->camo->create($imageUrl),
                'tags' => implode(', ', $dbImage->getTags()),
                'has_tags' => sizeof(implode(', ', $dbImage->getTags())) > 0
            ];
            if (StringUtil::endsWith($imageUrl, 'gifv', true)) {
                $videos[$dbImage->getImageId()] = $imageData;
            } else {
                $images[$dbImage->getImageId()] = $imageData;
            }
        }

        ksort($images, SORT_NATURAL | SORT_FLAG_CASE);

        return $this->render('KorobiWebBundle::kitty_image.html.twig', [
            'images' => $images,
            'videos' => $videos
        ]);
    }
}
