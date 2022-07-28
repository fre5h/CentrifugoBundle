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

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * ChannelsCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
#[AsCommand(name: 'centrifugo:channels', description: 'Get list of active (with one or more subscribers) channels')]
final class ChannelsCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command allows to get list of active (with one or more subscribers) channels:

Read more at https://centrifugal.github.io/centrifugo/server/http_api/#channels
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
            $data = $this->centrifugo->channels();

            if (!empty($data['channels'])) {
                $io->title('Channels');
                $io->listing($data['channels']);
                $io->text(\sprintf('<info>TOTAL</info>: %d', \count($data['channels'])));
            } else {
                $io->success('NO DATA');
            }
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
