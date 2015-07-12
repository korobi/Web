<?php

namespace Korobi\WebBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class NetworkRepository extends DocumentRepository {

    /**
     * @return \Doctrine\MongoDB\Cursor
     */
    public function findNetworks() {
        return $this->createQueryBuilder()
            ->sort('name', 'ASC')
            ->getQuery()
            ->execute();
    }

    /**
     * @param string $slug
     * @return \Doctrine\MongoDB\Cursor
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
