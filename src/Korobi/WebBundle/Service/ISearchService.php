<?php

namespace Korobi\WebBundle\Service;

use Korobi\WebBundle\Search\ChannelSuggestion;

interface ISearchService {

    /**
     * Gets autocomplete suggestions for a given fragment of a channel name.
     *
     * @param string $channelName Part of the channel name.
     * @return ChannelSuggestion[]
     */
    public function getSuggestionsForChannelName($channelName);

}
