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

use Fresh\CentrifugoBundle\Service\Centrifugo;
use Fresh\CentrifugoBundle\Service\CentrifugoChecker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * PresenceStatsCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class PresenceStatsCommand extends Command
{
    protected static $defaultName = 'centrifugo:presence-stats';

    /** @var Centrifugo */
    private $centrifugo;

    /** @var CentrifugoChecker */
    private $centrifugoChecker;

    /** @var string */
    private $channel;

    /**
     * @param Centrifugo        $centrifugo
     * @param CentrifugoChecker $centrifugoChecker
     */
    public function __construct(Centrifugo $centrifugo, CentrifugoChecker $centrifugoChecker)
    {
        $this->centrifugo = $centrifugo;
        $this->centrifugoChecker = $centrifugoChecker;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Get short channel presence information')
            ->setDefinition(
                new InputDefinition([
                    new InputArgument('channel', InputArgument::REQUIRED, 'Channel name'),
                ])
            )
            ->setHelp(<<<EOT
The <info>%command.name%</info> command allows to get short channel presence information:

<info>%command.full_name%</info> <comment>channelAbc</comment>

Read more at https://centrifugal.github.io/centrifugo/server/http_api/#presence_stats
EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        parent::initialize($input, $output);

        try {
            $channel = (string) $input->getArgument('channel');
            $this->centrifugoChecker->assertValidChannelName($channel);
            $this->channel = $channel;
        } catch (\Exception $e) {
            $this->channel = null;

            throw new InvalidArgumentException($e->getMessage());
        }
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
            $io->text(\sprintf('<info>num_clients</info>: <comment>%d</comment>', $data['num_clients']));
            $io->text(\sprintf('<info>num_users</info>: <comment>%d</comment>', $data['num_users']));
            $io->newLine();
        } catch (\Exception $e) {
            $io->error($e->getMessage());
        }

        return 0;
    }
}
