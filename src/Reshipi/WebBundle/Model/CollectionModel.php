<?php

namespace Reshipi\WebBundle\Model;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Validator;

use Reshipi\WebBundle\Model\AbstractModel;
use Reshipi\WebBundle\Entity\CollectionEntity;
use Reshipi\WebBundle\Entity\RecipeEntity;
use Reshipi\WebBundle\Model\Exception\EntityAlreadyExistsException;
use Reshipi\WebBundle\Model\Exception\EntityNotFoundException;

use PhpGit\Git;
use PhpProc\Process;

class CollectionModel extends AbstractModel
{
    protected $tempDir;
    protected $dataDir;

    /**
     * Constructs a CollectionModel instance.
     *
     * @param \Doctrine\ORM\EntityRepository $repository
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \Symfony\Component\Validator\Validator $validator
     * @param string Optional. Absolute path to the temp directory, defaults to sys_get_temp_dir()
     */
    public function __construct(EntityRepository $repository, EntityManager $entityManager, Validator $validator, $dataDir, $tempDir = null)
    {
        parent::__construct($repository, $entityManager, $validator);

        $this->dataDir = $dataDir;
        $this->tempDir = $tempDir ?: sys_get_temp_dir();
    }

    /**
     * Gets all collections.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->repository->findAll();
    }

    /**
     * Gets a specific collection by slug.
     *
     * @param string $slug
     *
     * @return \Reshipi\WebBundle\Entity\CollectionEntity
     *
     * @throws \Reshipi\WebBundle\Model\Exception\EntityNotFoundException
     */
    public function getOneBySlug($slug)
    {
        $collection = $this->repository->findOneBySlug($slug);
        if (null === $collection) {
            throw new EntityNotFoundException("Collection '{$slug}' was not found");
        }

        return $collection;
    }

    /**
     * Adds a collection if it does not already exist.
     *
     * @param \Reshipi\WebBundle\Entity\CollectionEntity $collection
     *
     * @return \Reshipi\WebBundle\Entity\CollectionEntity
     *
     * @throws \Reshipi\WebBundle\Model\Exception\EntityAlreadyExistsException
     */
    public function add(CollectionEntity $collection)
    {
        $this->validateEntity($collection);

        if ($this->repository->findOneBySlug($collection->getSlug())) {
            throw new EntityAlreadyExistsException("Collection '{$collection->getName()}' already exists");
        }

        $this->entityManager->persist($collection);
        $this->entityManager->flush();

        return $collection;
    }

    /**
     * Ensures all collection recipe repos are up to date, creates a tar.gz archive of them all and returns the file path.
     *
     * @param CollectionEntity Collection containing recipes
     *
     * @return string File path to the tar.gz file
     */
    public function getArchiveForCollection(CollectionEntity $collection)
    {
        $id = uniqid();
        $archiveDir = "{$this->tempDir}/recipes-{$id}";
        $recipesDir = "{$archiveDir}/cookbooks";
        $archiveFile = "recipes-{$id}.tar.gz";

        mkdir($archiveDir);
        mkdir($recipesDir);

        foreach ($collection->getRecipes() as $recipe) {
            $this->copyRecipeToScratch($this->pullOrCloneRecipe($recipe), "{$recipesDir}/{$recipe->getName()}");
        }

        $process = new Process("tar -czf ../{$archiveFile} . && rm -rf {$archiveDir}");
        $result = $process->setWorkingDirectory($archiveDir)->execute();
        if ($result->hasErrors()) {
            throw new \RuntimeException($result->getStdErrContents());
        }

        return "{$this->tempDir}/{$archiveFile}";
    }

    /**
     * Updates an existing collection.
     *
     * @param \Reshipi\WebBundle\Entity\CollectionEntity $collection
     *
     * @return \Reshipi\WebBundle\Entity\CollectionEntity
     */
    public function update(CollectionEntity $collection)
    {
        $this->validateEntity($collection);
        $this->entityManager->merge($collection);
        $this->entityManager->flush();

        return $collection;
    }

    /**
     * Deletes an existing collection.
     *
     * @param string $slug
     *
     * @return \Reshipi\WebBundle\Entity\CollectionEntity
     */
    public function delete($slug)
    {
        $collection = $this->repository->findOneBySlug($slug);
        if (null === $collection) {
            throw new EntityNotFoundException("Collection '{$slug}' was not found");
        }

        $this->entityManager->remove($collection);
        $this->entityManager->flush();

        return $collection;
    }

    /**
     * Pulls from or clones a remote recipe repository and returns the path to it's local clone.
     *
     * @param RecipeEntity $recipe
     *
     * @return string Path to local repository
     */
    protected function pullOrCloneRecipe(RecipeEntity $recipe)
    {
        $git = new Git('/usr/bin/git', new Process());
        $path = "{$this->dataDir}/{$recipe->getName()}";
        if (false === $git->repoExists($path)) {
            $git->clone($recipe->getUrl(), $path);
        } else {
            $git->pull($path);
        }

        return $path;
    }

    /**
     * Copies files from directory recursively into another, ignoring the .git directory.
     *
     * @param $source string Path to the source directory
     * @param $target string Path to the target directory
     */
    protected function copyRecipeToScratch($source, $target)
    {
        $directory = opendir($source);
        false === is_dir($target) && mkdir($target);

        while (false !== ($file = readdir($directory))) {
            if ('.' !== $file && '..' !== $file && '.git' !== $file) {
                if (is_dir("{$source}/{$file}")) {
                    $this->copyRecipeToScratch("{$source}/{$file}", "{$target}/{$file}");
                } else {
                    copy("{$source}/{$file}", "{$target}/{$file}");
                }
            }
        }

        closedir($directory);
    }
}
