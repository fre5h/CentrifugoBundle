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

use Fresh\CentrifugoBundle\Command\Argument\ArgumentChannelTrait;
use Fresh\CentrifugoBundle\Service\CentrifugoChecker;
use Fresh\CentrifugoBundle\Service\CentrifugoInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\Kernel;

/**
 * PresenceStatsCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
#[AsCommand(name: 'centrifugo:presence-stats', description: 'Get short channel presence information - number of clients and number of unique users (based on user ID)')]
final class PresenceStatsCommand extends AbstractCommand
{
    use ArgumentChannelTrait;

    /**
     * @param CentrifugoInterface $centrifugo
     * @param CentrifugoChecker   $centrifugoChecker
     */
    public function __construct(CentrifugoInterface $centrifugo, protected readonly CentrifugoChecker $centrifugoChecker)
    {
        parent::__construct($centrifugo);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        // @phpstan-ignore-next-line
        if (Kernel::MAJOR_VERSION >= 6) {
            // @phpstan-ignore-next-line
            $channelArgument = new InputArgument('channel', InputArgument::REQUIRED, 'Name of channel to call presence from', null, $this->getChannelsForAutocompletion());
        } else {
            $channelArgument = new InputArgument('channel', InputArgument::REQUIRED, 'Name of channel to call presence from');
        }

        $this
            ->setDefinition(
                new InputDefinition([
                    $channelArgument,
                ])
            )
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command allows to get short channel presence information - number of clients and number of unique users (based on user ID):

<info>%command.full_name%</info> <comment>channelName</comment>

Read more at https://centrifugal.dev/docs/server/server_api#presence_stats
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

        $this->initializeChannelArgument($input);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $data = $this->centrifugo->presenceStats($this->channel);

            $io->title('Presence Stats');
            $io->text(\sprintf('<info>Total number of clients in channel</info>: <comment>%d</comment>', $data['num_clients']));
            $io->text(\sprintf('<info>Total number of unique users in channel</info>: <comment>%d</comment>', $data['num_users']));
            $io->newLine();
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
