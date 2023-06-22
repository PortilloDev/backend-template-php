<?php

namespace App\Tests\Behat\Context;

use Behat\Behat\Context\Context;
use Symfony\Component\HttpKernel\KernelInterface;
use Zenstruck\Messenger\Test\Transport\TestTransport;
use Zenstruck\Messenger\Test\Transport\TestTransportRegistry;

class MessengerContext implements Context
{
    public function __construct(private readonly KernelInterface $kernel)
    {
    }

    /**
     * @When I consume messages
     */
    public function iConsumeMessages()
    {
        $transport = $this->transport('async');
        $transport->process();
        $transport->queue()->assertEmpty();
    }

    /**
     * @When :num async messages must be scheduled
     */
    public function asyncMessagesMustBeScheduled(int $num)
    {
        $transport = $this->transport('async');
        $transport->queue()->assertCount($num);
    }

    private function transport(?string $transport = null): TestTransport
    {
        /** @var TestTransportRegistry $registry */
        $registry = $this->kernel->getContainer()->get('zenstruck_messenger_test.transport_registry');

        return $registry->get($transport);
    }
}
