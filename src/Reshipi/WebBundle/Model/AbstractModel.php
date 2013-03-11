<?php

namespace Reshipi\WebBundle\Model;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Validator;

/**
 * AbstractModel
 *
 * Abstract Model base class to be used by concrete Model instances.
 */
abstract class AbstractModel
{
    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $repository;

    /**
     * @var \Symfony\Component\Validator\Validator
     */
    protected $validator;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * Constructs a Model instance.
     *
     * @param \Doctrine\ORM\EntityRepository $repository
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \Symfony\Component\Validator\Validator $validator
     */
    public function __construct(EntityRepository $repository, EntityManager $entityManager, Validator $validator)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    /**
     * Validate's a Doctrine Entity using the constraint annotations on the Entity class.
     *
     * @param $entity Doctrine entity to be validated
     *
     * @throws Exception\ValidationException
     */
    public function validateEntity($entity)
    {
        $errors = $this->validator->validate($entity);
        if (count($errors)) {
            throw new ValidationException("{$errors[0]->getPropertyPath()}: {$errors[0]->getMessage()}");
        }
    }
}
