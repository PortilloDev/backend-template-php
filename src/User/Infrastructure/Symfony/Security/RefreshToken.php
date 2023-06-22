<?php

namespace App\User\Infrastructure\Symfony\Security;

use Doctrine\ORM\Mapping as ORM;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken as BaseRefreshToken;

#[ORM\Table(name: 'refresh_tokens')]
#[ORM\Entity]
class RefreshToken extends BaseRefreshToken
{
    /**
     * @return int|string|null
     */
    public function getId()
    {
        return $this->id;
    }
}
