<?php
/**
 * Copyright © Mago Assistant
 */
declare(strict_types=1);

namespace MagoAssistant\Higgsfield\Test\Unit\Fakes;

use MagoAssistant\Higgsfield\Service\Client\TokenStore;

/**
 * Token store held in memory, with the same merge semantics as the flag-backed one.
 */
class FakeTokenStore extends TokenStore
{
    /**
     * @param array<string,mixed> $data
     */
    public function __construct(public array $data = [])
    {
    }

    public function load(): array
    {
        return $this->data;
    }

    public function save(array $values): void
    {
        foreach ($values as $key => $value) {
            if ($value === null) {
                unset($this->data[$key]);
                continue;
            }
            $this->data[$key] = $value;
        }
    }

    public function clear(): void
    {
        $this->data = [];
    }
}
