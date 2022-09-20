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

namespace Fresh\CentrifugoBundle\Command\Option;

use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputInterface;

/**
 * OptionTagsTrait.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
trait OptionTagsTrait
{
    /** @var array<string, mixed> */
    protected array $tags = [];

    /**
     * @param InputInterface $input
     *
     * @throws InvalidOptionException
     */
    protected function initializeTagsOption(InputInterface $input): void
    {
        $tags = $input->getParameterOption(['--tags', '-t'], null);

        if (\is_string($tags) && !empty($tags)) {
            try {
                /** @var array $decodedData */
                $decodedData = \json_decode($tags, true, 512, \JSON_THROW_ON_ERROR);

                if (!\is_array($decodedData)) {
                    throw new InvalidOptionException('Option "--tags, -t" should be an associative array of strings.');
                }

                foreach ($decodedData as $tag => $value) {
                    if (!\is_string($tag) || !\is_string($value)) {
                        throw new InvalidOptionException('Option "--tags, -t" should be an associative array of strings.');
                    }
                }

                $this->tags = $decodedData;
            } catch (\JsonException) {
                throw new InvalidOptionException('Option "--tags, -t" is not a valid JSON.');
            }
        }
    }
}
