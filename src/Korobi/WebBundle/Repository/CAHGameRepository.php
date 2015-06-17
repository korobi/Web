<?php

namespace Korobi\WebBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class CAHGameRepository extends DocumentRepository {

    /**
     * @param $id
     * @return mixed
     */
    public function findById($id) {
        return $this->createQueryBuilder()
            ->field('_id')
                ->equals($id)
            ->getQuery()
            ->execute();
    }
}
