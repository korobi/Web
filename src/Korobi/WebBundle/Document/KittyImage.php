<?php

namespace Korobi\WebBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="kitty_images",repositoryClass="Korobi\WebBundle\Repository\KittyImageRepository")
 */
class KittyImage {

    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(type="int")
     */
    private $image_id;

    /**
     * @MongoDB\Field(type="string")
     */
    private $url;

    /**
     * @MongoDB\Collection
     */
    private $tags;

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get imageId
     *
     * @return int $imageId
     */
    public function getImageId() {
        return $this->image_id;
    }

    /**
     * Set imageId
     *
     * @param int $imageId
     * @return self
     */
    public function setImageId($imageId) {
        $this->image_id = $imageId;
        return $this;
    }

    /**
     * Get url
     *
     * @return string $url
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return self
     */
    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    /**
     * Get tags
     *
     * @return collection $tags
     */
    public function getTags() {
        return $this->tags;
    }

    /**
     * Set tags
     *
     * @param collection $tags
     * @return self
     */
    public function setTags($tags) {
        $this->tags = $tags;
        return $this;
    }
}
