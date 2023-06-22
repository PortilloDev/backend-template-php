<?php

namespace App\Tests\Behat\Context;

use App\Tests\Behat\Client\MockComicClientMarvel;
use App\Tests\Behat\Persistence\BehatVariablesDatabase;
use Behat\Gherkin\Node\PyStringNode;
use Behat\MinkExtension\Context\RawMinkContext;
use Behatch\Asserter;

final class MarvelContext extends RawMinkContext
{
    use Asserter;

    /**
     * @BeforeScenario
     */
    public function resetVariable(): void
    {
        BehatVariablesDatabase::reset();
    }

    /**
     * @Given Marvel will respond for :url and method :method with content:
     */
    public function pfsWillRespondForUrlWithMethodAndBody(string $url, string $method, PyStringNode $stringNode): void
    {
        BehatVariablesDatabase::set(MockComicClientMarvel::getName().md5($method.$url), json_decode($stringNode->getRaw(), true, 512, JSON_THROW_ON_ERROR));
    }

    /**
     * @Given Marvel will throw exception :exception for :url with method :method
     */
    public function pfsWillThrowExceptionForUrlWithMethodAndBody(string $exception, string $url, string $method): void
    {
        BehatVariablesDatabase::set(MockComicClientMarvel::getName().'exception'.md5($method.$url), $exception);
    }

    /**
     * @Then A request to Marvel is sent with :url method :method
     */
    public function aRequestIsSentToPfs(string $url, string $method): void
    {
        $data = BehatVariablesDatabase::get(MockComicClientMarvel::getName().md5($method.$url).'sent');

        $this->assertTrue(null !== $data, 'Request is not sent.');
    }

    /**
     * @Then A request to Marvel is sent with :url method :method and contains:
     */
    public function aRequestIsSentToPfsWithBody(string $url, string $method, PyStringNode $stringNode): void
    {
        $this->aRequestIsSentToPfs($url, $method);
        $data = BehatVariablesDatabase::get(MockComicClientMarvel::getName().md5($method.$url).'sent');
        $node = json_decode($stringNode->getRaw(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayAreSimilar($data, $node);
    }

    private function assertArrayAreSimilar(array $a, array $b): void
    {
        foreach ($b as $key => $value) {
            if (!\array_key_exists($key, $a)) {
                $this->assert(false, sprintf("Error asserting %s is sent: \n%s \n%s", $key, json_encode($a, JSON_THROW_ON_ERROR), json_encode($b, JSON_THROW_ON_ERROR)));
            }

            if ($value !== $a[$key]) {
                $this->assert(false, sprintf("Error asserting content equals: \n%s \n%s", json_encode($a, JSON_THROW_ON_ERROR), json_encode($b, JSON_THROW_ON_ERROR)));
            }
        }
    }
}
