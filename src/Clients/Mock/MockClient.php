<?php declare(strict_types=1);

namespace StrictPhp\HttpClients\Clients\Mock;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use StrictPhp\HttpClients\Helpers\Response;

/**
 * @phpstan-import-type inputType from Response
 */
final class MockClient implements ClientInterface
{
    /**
     * @var inputType|TestQueue<inputType>
     */
    private $content;

    /**
     * @param inputType|list<inputType>|TestQueue<inputType> $content
     */
    public function __construct($content)
    {
        if (is_array($content)) {
            /** @var list<inputType> $content */
            $this->content = new TestQueue($content);
        } else {
            $this->content = $content;
        }
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return Response::fromContent(
            $this->content instanceof TestQueue
                ? $this->content->first()
                : $this->content,
            $request,
        );
    }
}
