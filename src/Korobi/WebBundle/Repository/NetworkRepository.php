<?php

namespace Korobi\WebBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class NetworkRepository extends DocumentRepository {

    public function findNetworks() {
        return $this->createQueryBuilder()
            ->sort('name', 'ASC')
            ->getQuery()
            ->execute();
    }

    public function findNetwork($slug) {
        return $this->createQueryBuilder()
            ->sort('name', 'ASC')
            ->field('slug')->equals($slug)
            ->getQuery()
            ->execute();
    }
}
