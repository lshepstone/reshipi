<?php

namespace Reshipi\WebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Slug;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Reshipi\WebBundle\Repository\RecipeRepository")
 * @ORM\Table(name="recipes")
 */
class RecipeEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="CollectionEntity", inversedBy="recipes")
     * @ORM\JoinColumn(name="collection_id", referencedColumnName="id")
     */
    protected $collection;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     * @Slug(fields={"name"}, separator="-")
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=250)
     * @Assert\NotBlank()
     */
    protected $url;

    /**
     * Gets the ID.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the CollectionEntity instance.
     *
     * @param CollectionEntity $collection
     * @return RecipeEntity
     */
    public function setCollection(CollectionEntity $collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Gets the CollectionEntity instance.
     *
     * @return CollectionEntity
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Sets the name.
     *
     * @param string $name
     *
     * @return RecipeEntity
     */
    public function setName($name)
    {
        $this->name = (string) $name;

        return $this;
    }

    /**
     * Gets the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the URL.
     *
     * @param string $url
     *
     * @return RecipeEntity
     */
    public function setUrl($url)
    {
        $this->url = (string) $url;

        return $this;
    }

    /**
     * Gets the url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}
