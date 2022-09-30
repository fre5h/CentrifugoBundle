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
use Fresh\CentrifugoBundle\Command\Argument\ArgumentUserTrait;
use Fresh\CentrifugoBundle\Command\Option\OptionClientTrait;
use Fresh\CentrifugoBundle\Command\Option\OptionEpochTrait;
use Fresh\CentrifugoBundle\Command\Option\OptionOffsetTrait;
use Fresh\CentrifugoBundle\Command\Option\OptionSessionTrait;
use Fresh\CentrifugoBundle\Model\StreamPosition;
use Fresh\CentrifugoBundle\Service\CentrifugoChecker;
use Fresh\CentrifugoBundle\Service\CentrifugoInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\Kernel;

/**
 * SubscribeCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
#[AsCommand(name: 'centrifugo:subscribe', description: 'Subscribe user to a channel')]
final class SubscribeCommand extends AbstractCommand
{
    use ArgumentChannelTrait;
    use ArgumentUserTrait;
    use OptionClientTrait;
    use OptionEpochTrait;
    use OptionOffsetTrait;
    use OptionSessionTrait;

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
            $channelArgument = new InputArgument('channel', InputArgument::REQUIRED, 'Name of channel to subscribe user to', null, $this->getChannelsForAutocompletion());
        } else {
            $channelArgument = new InputArgument('channel', InputArgument::REQUIRED, 'Name of channel to subscribe user to');
        }

        $this
            ->setDefinition(
                new InputDefinition([
                    new InputArgument('user', InputArgument::REQUIRED, 'User ID to subscribe'),
                    $channelArgument,
                    new InputOption('client', null, InputOption::VALUE_OPTIONAL, 'Specific client ID to subscribe (user still required to be set, will ignore other user connections with different client IDs)'),
                    new InputOption('session', null, InputOption::VALUE_OPTIONAL, 'Specific client session to subscribe (user still required to be set)'),
                    new InputOption('offset', null, InputOption::VALUE_OPTIONAL, 'Offset in a stream'),
                    new InputOption('epoch', null, InputOption::VALUE_OPTIONAL, 'Stream epoch'),
                ])
            )
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command allows subscribing user to a channel:

<info>%command.full_name%</info> <comment>user123</comment> <comment>channelName</comment>

You can subscribe specific user to a channel by client ID (user still required to be set):

<info>%command.full_name%</info> <comment>user123</comment> <comment>channelName</comment> <comment>--client=clientID</comment>

You can subscribe specific user to a channel by session ID (user still required to be set):

<info>%command.full_name%</info> <comment>user123</comment> <comment>channelName</comment> <comment>--session=sessionID</comment>

Read more at https://centrifugal.dev/docs/server/server_api#history
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

        $this->initializeUserArgument($input);
        $this->initializeChannelArgument($input);
        $this->initializeClientOption($input);
        $this->initializeEpochOption($input);
        $this->initializeOffsetOption($input);
        $this->initializeSessionOption($input);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->centrifugo->subscribe(
                user: $this->user,
                channel: $this->channel,
                client: $this->client,
                session: $this->session,
                recoverSince: new StreamPosition($this->offset, $this->epoch),
            );
            $io->success('DONE');
        } catch (\Throwable $e) {
            echo $e->getMessage();
            $io->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
