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

use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputInterface;

/**
 * OptionB64DataTrait.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
trait OptionB64DataTrait
{
    protected string $b64data = '';

    /**
     * @param InputInterface $input
     *
     * @throws InvalidOptionException
     */
    protected function initializeB64DataOption(InputInterface $input): void
    {
        $this->b64data = $input->getParameterOption(['--b64data', '-b'], '');

        if (!empty($this->b64data)) {
            $decodedData = \base64_decode($this->b64data, true);
            if (false === $decodedData) {
                throw new InvalidOptionException('Option "--b64data, -b" should be a valid base64 encoded string.');
            }
        }
    }
}
