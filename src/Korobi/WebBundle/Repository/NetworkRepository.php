<?php

namespace Korobi\WebBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class NetworkRepository extends DocumentRepository {

    /**
     * @return mixed
     */
    public function findNetworks() {
        return $this->createQueryBuilder()
            ->sort('name', 'ASC')
            ->getQuery()
            ->execute();
    }

    /**
     * @param $slug
     * @return mixed
     */
    public function findNetwork($slug) {
        return $this->createQueryBuilder()
            ->sort('name', 'ASC')
            ->field('slug')
                ->equals($slug)
            ->getQuery()
            ->execute();
    }
}
