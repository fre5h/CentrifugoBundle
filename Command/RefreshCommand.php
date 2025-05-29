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

use Fresh\CentrifugoBundle\Command\Argument\ArgumentUserTrait;
use Fresh\CentrifugoBundle\Command\Option\OptionClientTrait;
use Fresh\CentrifugoBundle\Command\Option\OptionExpireAtTrait;
use Fresh\CentrifugoBundle\Command\Option\OptionExpiredTrait;
use Fresh\CentrifugoBundle\Command\Option\OptionSessionTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * RefreshCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
#[AsCommand(name: 'centrifugo:refresh', description: 'Refresh user connection')]
final class RefreshCommand extends AbstractCommand
{
    use ArgumentUserTrait;
    use OptionClientTrait;
    use OptionExpireAtTrait;
    use OptionExpiredTrait;
    use OptionSessionTrait;

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDefinition(
                new InputDefinition([
                    new InputArgument('user', InputArgument::REQUIRED, 'User ID to refresh'),
                    new InputOption('client', null, InputOption::VALUE_OPTIONAL, 'Specific client ID to refresh (user still required to be set)'),
                    new InputOption('session', null, InputOption::VALUE_OPTIONAL, 'Specific client session to refresh (user still required to be set)'),
                    new InputOption('expired', null, InputOption::VALUE_OPTIONAL, 'Mark connection as expired and close with Disconnect Expired reason'),
                    new InputOption('expireAt', null, InputOption::VALUE_OPTIONAL, 'Unix time (in seconds) in the future when the connection will expire'),
                ])
            )
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command allows refreshing user connection (mostly useful when unidirectional transports are used):

<info>%command.full_name%</info> <comment>user123</comment>

You can refresh a specific user by their client ID:

<info>%command.full_name%</info> <comment>user123</comment> <comment>--client=clientID</comment>

You can refresh a specific user by their session ID:

<info>%command.full_name%</info> <comment>user123</comment> <comment>--session=sessionID</comment>

You can mark connection as expired:

<info>%command.full_name%</info> <comment>user123</comment> <comment>--expired</comment>

You can set a Unix time (in seconds) in the future when the connection will expire:

<info>%command.full_name%</info> <comment>user123</comment> <comment>--expireAt=1234567890</comment>

Read more at https://centrifugal.dev/docs/server/server_api#refresh
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
        $this->initializeClientOption($input);
        $this->initializeExpireAtOption($input);
        $this->initializeExpiredOption($input);
        $this->initializeSessionOption($input);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->centrifugo->refresh(
                user: $this->user,
                client: $this->client,
                session: $this->session,
                expired: $this->expired,
                expireAt: $this->expireAt,
            );
            $io->success('DONE');
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
