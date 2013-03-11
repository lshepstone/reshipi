<?php

namespace Reshipi\WebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Reshipi\WebBundle\Repository\CollectionRepository")
 * @ORM\Table(name="collections")
 */
class CollectionEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     * @Gedmo\Slug(fields={"name"}, separator="-")
     */
    protected $slug;

    /**
     * @ORM\ManyToMany(targetEntity="RecipeEntity")
     * @ORM\JoinTable(
     *      name="collections_recipes",
     *      joinColumns={@ORM\JoinColumn(name="collection_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="recipe_id", referencedColumnName="id")})
     **/
    protected $recipes;

    /**
     * Constructs an entity instance.
     */
    public function __construct()
    {
        $this->recipes = new ArrayCollection();
    }

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
     * Sets the name.
     *
     * @param string $name
     *
     * @return Collection
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
     * Gets the slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Gets all collection recipes.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getRecipes()
    {
        return $this->recipes;
    }

    /**
     * Adds a recipe to the collection.
     *
     * @param RecipeEntity $recipe
     */
    public function addRecipe(RecipeEntity $recipe)
    {
        $this->recipes[] = $recipe;
    }
}
