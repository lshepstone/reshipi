<?php

namespace Reshipi\WebBundle\Model\Exception;

use Reshipi\WebBundle\Model\Exception;

class EntityAlreadyExistsException extends \RuntimeException implements Exception
{
    /**
     * Entity related to the exception.
     *
     * @var null|object
     */
    protected $entity;

    /**
     * Constructs an EntityAlreadyExistsException instance.
     *
     * @param string $message
     * @param int $code
     * @param null $entity
     * @param \Reshipi\WebBundle\Model\Exception $previous
     */
    public function __construct($message = '', $entity = null, $code = 0, Exception $previous = null)
    {
        $this->entity = $entity;

        parent::__construct($message, $code, $previous);
    }

    /**
     * Gets the entity related to the exception.
     *
     * @return object
     */
    public function getEntity()
    {
        return $this->entity;
    }
}
