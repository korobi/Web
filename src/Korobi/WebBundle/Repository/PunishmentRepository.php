<?php

namespace Korobi\WebBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class PunishmentRepository extends DocumentRepository {

    /**
     * @param string $network
     * @param string $channel
     * @return \Doctrine\MongoDB\Cursor
     */
    public function findAllByChannel($network, $channel) {
        return $this->createQueryBuilder()
            ->sort('date', 'DESC')
            ->field('network')
                ->equals($network)
            ->field('channel')
                ->equals($channel)
            ->getQuery()
            ->execute();
    }
}
