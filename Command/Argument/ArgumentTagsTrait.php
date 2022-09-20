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

namespace Fresh\CentrifugoBundle\Command\Argument;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;

/**
 * ArgumentTagsTrait.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
trait ArgumentTagsTrait
{
    /** @var array<string, mixed> */
    protected array $tags = [];

    /**
     * @param InputInterface $input
     *
     * @throws InvalidArgumentException
     */
    protected function initializeTagsArgument(InputInterface $input): void
    {
        $tags = $input->getArgument('tags');

        if (\is_string($tags) && !empty($tags)) {
            try {
                /** @var array $decodedData */
                $decodedData = \json_decode($tags, true, 512, \JSON_THROW_ON_ERROR);

                if (!\is_array($decodedData)) {
                    throw new InvalidArgumentException('Argument "tags" should be an associative array of strings.');
                }

                foreach ($decodedData as $tag => $value) {
                    if (!\is_string($tag) || !\is_string($value)) {
                        throw new InvalidArgumentException('Argument "tags" should be an associative array of strings.');
                    }
                }

                $this->tags = $decodedData;
            } catch (\JsonException) {
                throw new InvalidArgumentException('Argument "tags" is not a valid JSON.');
            }
        }
    }
}
