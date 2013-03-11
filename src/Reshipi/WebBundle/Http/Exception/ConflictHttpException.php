<?php

namespace Reshipi\WebBundle\Http\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ConflictHttpException extends HttpException
{
    /**
     * {@inheritDoc}
     */
    public function __construct($message = null, \Exception $previous = null, $code = 0)
    {
        parent::__construct(409, $message, $previous, array(), $code);
    }
}
