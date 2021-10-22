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

namespace Fresh\CentrifugoBundle\Command;

use Symfony\Component\Console\Input\InputInterface;

/**
 * ArgumentPatternTrait.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
trait ArgumentPatternTrait
{
    protected ?string $pattern;

    /**
     * @param InputInterface $input
     */
    protected function initializePatternArgument(InputInterface $input): void
    {
        $pattern = $input->getArgument('pattern');

        if (null !== $pattern) {
            $this->pattern = (string) $pattern;
        }
    }
}
