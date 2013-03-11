<?php

namespace Reshipi\WebBundle\Model;

use Doctrine\ORM\EntityRepository;
use Reshipi\WebBundle\Model\AbstractModel;
use Reshipi\WebBundle\Entity\RecipeEntity;
use Reshipi\WebBundle\Model\Exception\EntityAlreadyExistsException;
use Reshipi\WebBundle\Model\Exception\EntityNotFoundException;
use Reshipi\WebBundle\Entity\CollectionEntity;

class RecipeModel extends AbstractModel
{
    /**
     * Gets all recipes.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->repository->findAll();
    }

    /**
     * Gets all recipes for a collection.
     *
     * @param \Reshipi\WebBundle\Entity\CollectionEntity $collection
     *
     * @return array
     */
    public function getByCollection(CollectionEntity $collection)
    {
        return $this->repository->findByCollectionId($collection->getId());
    }

    /**
     * Gets a specific recipe by slug.
     *
     * @param string $slug
     *
     * @return \Reshipi\WebBundle\Entity\RecipeEntity
     *
     * @throws \Reshipi\WebBundle\Model\Exception\EntityNotFoundException
     */
    public function getOneByName($slug)
    {
        $recipe = $this->repository->findOneByName($slug);
        if (null === $recipe) {
            throw new EntityNotFoundException("Recipe '{$slug}' was not found");
        }

        return $recipe;
    }

    /**
     * Adds a recipe if it does not already exist.
     *
     * @param \Reshipi\WebBundle\Entity\RecipeEntity $recipe
     *
     * @return \Reshipi\WebBundle\Entity\RecipeEntity
     *
     * @throws \Reshipi\WebBundle\Model\Exception\EntityAlreadyExistsException
     */
    public function add(RecipeEntity $recipe)
    {
        $this->validateEntity($recipe);

        if ($existingRecipe = $this->repository->findOneByName($recipe->getName())) {
            throw new EntityAlreadyExistsException("Recipe '{$recipe->getName()}' already exists", $existingRecipe);
        }

        $this->entityManager->persist($recipe);
        $this->entityManager->flush();

        return $recipe;
    }

    /**
     * Updates an existing Recipe.
     *
     * @param \Reshipi\WebBundle\Entity\RecipeEntity $recipe
     *
     * @return \Reshipi\WebBundle\Entity\RecipeEntity
     */
    public function update(RecipeEntity $recipe)
    {
        $this->validateEntity($recipe);
        $this->entityManager->merge($recipe);
        $this->entityManager->flush();

        return $recipe;
    }

    /**
     * Deletes an existing Recipe.
     *
     * @param string $name
     *
     * @return \Reshipi\WebBundle\Entity\RecipeEntity
     */
    public function delete($name)
    {
        $recipe = $this->repository->findOneByName($name);
        if (null === $recipe) {
            throw new EntityNotFoundException("Recipe '{$name}' was not found");
        }

        $this->entityManager->remove($recipe);
        $this->entityManager->flush();

        return $recipe;
    }
}
