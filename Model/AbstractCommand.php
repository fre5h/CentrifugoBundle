<?php
/*
 * This file is part of the FreshCentrifugoBundle.
 *
 * (c) Artem Henvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Fresh\CentrifugoBundle\Model;

use JetBrains\PhpStorm\ArrayShape;

/**
 * AbstractCentrifugoCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
abstract class AbstractCommand implements SerializableCommandInterface
{
    /**
     * @param Method $method
     * @param array  $params
     */
    public function __construct(private readonly Method $method, private readonly array $params)
    {
    }

    /**
     * @return Method
     */
    public function getMethod(): Method
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * {@inheritdoc}
     */
    public function getChannels(): iterable
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    #[ArrayShape(['method' => 'string', 'params' => 'array'])]
    public function jsonSerialize(): array
    {
        return [
            'method' => $this->method->value,
            'params' => $this->params,
        ];
    }
}
