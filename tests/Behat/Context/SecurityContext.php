<?php

namespace App\Tests\Behat\Context;

use Behat\Mink\Driver\BrowserKitDriver;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\MinkExtension\Context\RawMinkContext;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class SecurityContext extends RawMinkContext
{
    public function __construct(private readonly JWTEncoderInterface $encoder)
    {
    }

    /**
     * @Given I am authenticated as :username
     */
    public function iAmAuthenticatedAs(string $username): void
    {
        $driver = $this->getSession()->getDriver();
        if (!$driver instanceof BrowserKitDriver) {
            throw new UnsupportedDriverActionException('Unsupported driver', $driver);
        }

        /** @var KernelBrowser $client */
        $client = $driver->getClient();
        $bearer = $this->encoder->encode(['username' => $username]);

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $bearer));
    }
}
