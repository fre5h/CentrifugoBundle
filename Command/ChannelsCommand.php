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

use Fresh\CentrifugoBundle\Command\Argument\ArgumentPatternTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\Kernel;

/**
 * ChannelsCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
#[AsCommand(name: 'centrifugo:channels', description: 'Get list of active (with one or more subscribers) channels')]
final class ChannelsCommand extends AbstractCommand
{
    use ArgumentPatternTrait;

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        if (Kernel::MAJOR_VERSION >= 6) { // @phpstan-ignore-line
            $patternArgument = new InputArgument('pattern', InputArgument::OPTIONAL, 'Pattern to filter channels', null, $this->getChannelsForAutocompletion());
        } else { // @phpstan-ignore-line
            $patternArgument = new InputArgument('pattern', InputArgument::OPTIONAL, 'Pattern to filter channels');
        }

        $this
            ->setDefinition(
                new InputDefinition([
                    $patternArgument,
                ])
            )
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command returns active channels (with one or more active subscribers in it):

<info>bin/console %command.name%</info>

You can optionally specify the <comment>pattern</comment> to filter channels by names:

<info>bin/console %command.name% <comment>channelName</comment></info>

Read more at https://centrifugal.dev/docs/server/server_api#channels
HELP
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        parent::initialize($input, $output);

        $this->initializePatternArgument($input);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $data = $this->centrifugo->channels(pattern: $this->pattern);

            if (!empty($data['channels'])) {
                $rows = [];
                foreach ($data['channels'] as $channelName => $item) {
                    $rows[] = [$channelName, $item['num_clients']];
                }
                $io->table(['Channel Name', 'Number Of Subscriptions'], $rows);

                $io->text(\sprintf('<info>Total Channels</info>: %d', \count($data['channels'])));
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
