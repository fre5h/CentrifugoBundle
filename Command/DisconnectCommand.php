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
use Fresh\CentrifugoBundle\Command\Option\OptionDisconnectCodeTrait;
use Fresh\CentrifugoBundle\Command\Option\OptionDisconnectReasonTrait;
use Fresh\CentrifugoBundle\Command\Option\OptionSessionTrait;
use Fresh\CentrifugoBundle\Model\DisconnectObject;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * DisconnectCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
#[AsCommand(name: 'centrifugo:disconnect', description: 'Disconnect a user by ID')]
final class DisconnectCommand extends AbstractCommand
{
    use ArgumentUserTrait;
    use OptionClientTrait;
    use OptionDisconnectCodeTrait;
    use OptionDisconnectReasonTrait;
    use OptionSessionTrait;

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDefinition(
                new InputDefinition([
                    new InputArgument('user', InputArgument::REQUIRED, 'User ID to disconnect'),
                    new InputOption('whitelist', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'Array of client IDs to keep', []),
                    new InputOption('client', null, InputOption::VALUE_OPTIONAL, 'Specific client ID to disconnect (user still required to be set)'),
                    new InputOption('session', null, InputOption::VALUE_OPTIONAL, 'Specific client session to disconnect (user still required to be set)'),
                    new InputOption('disconnectCode', null, InputOption::VALUE_OPTIONAL, 'Disconnect code'),
                    new InputOption('disconnectReason', null, InputOption::VALUE_OPTIONAL, 'Disconnect reason'),
                ])
            )
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command allows to disconnect user by ID:

<info>%command.full_name%</info> <comment>user123</comment>

You can set a whitelist of client IDs to keep:

<info>%command.full_name%</info> <comment>user123</comment> <comment>--whitelist=clientID1 --whitelist=clientID2</comment>

You can disconnect a specific user by their client ID:

<info>%command.full_name%</info> <comment>user123</comment> <comment>--client=clientID</comment>

You can disconnect a specific user by their session ID:

<info>%command.full_name%</info> <comment>user123</comment> <comment>--session=sessionID</comment>

You can set a specific disconnect code and reason:

<info>%command.full_name%</info> <comment>user123</comment> <comment>--disconnectCode=999 --disconnectReason=some reason</comment>

Read more at https://centrifugal.dev/docs/server/server_api#disconnect
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
        $this->initializeDisconnectCodeOption($input);
        $this->initializeDisconnectReasonOption($input);
        $this->initializeSessionOption($input);

        $emptyDisconnectReason = \is_int($this->disconnectCode) && empty($this->disconnectReason);
        $notSetDisconnectCode = null === $this->disconnectCode && !empty($this->disconnectReason);
        if ($emptyDisconnectReason || $notSetDisconnectCode) {
            throw new InvalidOptionException('Options "--disconnectReason" and "--disconnectCode" should set be together.');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $disconnectObject = null;
            if (\is_int($this->disconnectCode) && !empty($this->disconnectReason)) {
                $disconnectObject = new DisconnectObject($this->disconnectCode, $this->disconnectReason);
            }

            /** @var array<string> $whitelist */
            $whitelist = (array) $input->getOption('whitelist');

            $this->centrifugo->disconnect(
                user: $this->user,
                whitelist: $whitelist,
                client: $this->client,
                session: $this->session,
                disconnectObject: $disconnectObject,
            );
            $io->success('DONE');
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
