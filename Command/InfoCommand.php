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

use Fresh\CentrifugoBundle\Exception\UnexpectedValueException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * InfoCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
#[AsCommand(name: 'centrifugo:info', description: 'Get information about running Centrifugo nodes')]
final class InfoCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command allows getting information about running Centrifugo nodes:

<info>%command.full_name%</info>

Read more at https://centrifugal.dev/docs/server/server_api#info
HELP
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $data = $this->centrifugo->info();

            if (!empty($data['nodes'])) {
                $io->title('Info');

                foreach ($data['nodes'] as $nodeInfo) {
                    $io->section(\sprintf('<comment>Node</comment> <info>%s</info>', $nodeInfo['name']));
                    foreach ($nodeInfo as $key => $value) {
                        $this->writeParameter($io, $key, $value);
                    }
                }

                $io->newLine();
            } else {
                $io->success('NO DATA');
            }
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * @param SymfonyStyle $io
     * @param string       $key
     * @param mixed        $value
     * @param int          $padding
     * @param bool         $last
     *
     * @throws UnexpectedValueException
     */
    private function writeParameter(SymfonyStyle $io, string $key, mixed $value, int $padding = 0, bool $last = false): void
    {
        $formattedKey = $key;
        if ($padding > 0) {
            $formattedKey = $last ? '└ ' : '├ ';
            $formattedKey .= $key;
            $formattedKey = \str_pad($formattedKey, \strlen($formattedKey) + $padding, ' ', \STR_PAD_LEFT);
        }

        if (!\is_array($value)) {
            if (!\is_scalar($value)) {
                throw new UnexpectedValueException('Value is not an array, nor a string', 4);
            }

            $text = \sprintf('<info>%s</info>: %s', $formattedKey, $value);
            $io->text($text);
        } else {
            $io->text(\sprintf('<info>%s</info>', $formattedKey));

            $total = \count($value);
            $i = 0;
            foreach ($value as $innerKey => $innerValue) {
                ++$i;
                $this->writeParameter($io, $innerKey, $innerValue, $padding + 2, $total === $i);
            }
        }
    }
}
