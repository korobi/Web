<?php

namespace Korobi\WebBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class KittyImageRepository extends DocumentRepository {

    public function findAllImages() {
        return $this->createQueryBuilder()
            ->sort('image_id', 'ASC')
            ->getQuery()
            ->execute();
    }
}
