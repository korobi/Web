<?php

namespace Korobi\WebBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class ChannelAIRepository extends DocumentRepository {

    /**
     * @param string $network
     * @param string $channel
     * @return \Doctrine\MongoDB\Cursor
     */
    public function findByChannel($network, $channel) {
        return $this->createQueryBuilder()
            ->field('network')
                ->equals($network)
            ->field('channel')
                ->equals($channel)
            ->getQuery()
            ->execute();
    }
}
