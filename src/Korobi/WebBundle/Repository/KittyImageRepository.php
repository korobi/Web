<?php

namespace Korobi\WebBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class KittyImageRepository extends DocumentRepository {

    /**
     * @return \Doctrine\MongoDB\Cursor
     */
    public function findAllImages() {
        return $this->createQueryBuilder()
            ->sort('image_id', 'ASC')
            ->getQuery()
            ->execute();
    }
}
