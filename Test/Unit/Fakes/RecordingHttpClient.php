<?php
/**
 * Copyright © Mago Assistant
 */
declare(strict_types=1);

namespace MagoAssistant\Higgsfield\Test\Unit\Fakes;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Answers requests from a queue of responses and records what was sent.
 */
class RecordingHttpClient implements ClientInterface
{
    /**
     * @var list<array{method:string,uri:string,options:array<string,mixed>}>
     */
    public array $requests = [];

    /**
     * @param list<ResponseInterface> $responses
     */
    public function __construct(private array $responses = [])
    {
    }

    public function queue(ResponseInterface ...$responses): void
    {
        array_push($this->responses, ...$responses);
    }

    public function request(string $method, $uri = '', array $options = []): ResponseInterface
    {
        $this->requests[] = ['method' => $method, 'uri' => (string)$uri, 'options' => $options];
        $response = array_shift($this->responses);
        if ($response === null) {
            throw new \LogicException('No response queued for ' . $method . ' ' . $uri);
        }

        return $response;
    }

    public function send(RequestInterface $request, array $options = []): ResponseInterface
    {
        throw new \LogicException('Not used');
    }

    public function sendAsync(RequestInterface $request, array $options = []): PromiseInterface
    {
        throw new \LogicException('Not used');
    }

    public function requestAsync(string $method, $uri, array $options = []): PromiseInterface
    {
        throw new \LogicException('Not used');
    }

    public function getConfig(?string $option = null)
    {
        return null;
    }
}
