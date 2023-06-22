<?php

namespace App\Shared\Domain\Exception;

class ResourcesNotFoundException extends DomainException
{
    /**
     * ResourcesNotFoundException constructor.
     */
    public function __construct(string $resource)
    {
        $message = sprintf(
            'Resource %s not exist',
            $resource
        );
        parent::__construct($message);
    }
}
