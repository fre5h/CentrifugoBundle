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
use Fresh\CentrifugoBundle\Command\Option\OptionSessionTrait;
use Fresh\CentrifugoBundle\Service\CentrifugoChecker;
use Fresh\CentrifugoBundle\Service\CentrifugoInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * UnsubscribeCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
#[AsCommand(name: 'centrifugo:unsubscribe', description: 'Unsubscribe user from a channel')]
final class UnsubscribeCommand extends AbstractCommand
{
    use ArgumentChannelTrait;
    use ArgumentUserTrait;
    use OptionClientTrait;
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
        $this
            ->setDefinition(
                new InputDefinition([
                                        new InputArgument('user', InputArgument::REQUIRED, 'User ID to unsubscribe'),
                                        new InputArgument('channel', InputArgument::REQUIRED, 'Name of channel to unsubscribe user to', null, $this->getChannelsForAutocompletion()),
                                        new InputOption('client', null, InputOption::VALUE_OPTIONAL, 'Specific client ID to unsubscribe (user still required to be set)'),
                                        new InputOption('session', null, InputOption::VALUE_OPTIONAL, 'Specific client session to disconnect (user still required to be set)'),
                ])
            )
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command allows unsubscribing user from a channel:

<info>%command.full_name%</info> <comment>user123</comment> <comment>channelName</comment>

You can unsubscribe specific user from a channel by client ID:

<info>%command.full_name%</info> <comment>user123</comment> <comment>channelName</comment> <comment>--client=clientID</comment>

You can unsubscribe specific user from a channel by session ID:

<info>%command.full_name%</info> <comment>user123</comment> <comment>channelName</comment> <comment>--session=sessionID</comment>

Read more at https://centrifugal.dev/docs/server/server_api#unsubscribe
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
        $this->initializeSessionOption($input);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->centrifugo->unsubscribe(
                user: $this->user,
                channel: $this->channel,
                client: $this->client,
                session: $this->session,
            );
            $io->success('DONE');
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
