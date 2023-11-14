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
use Fresh\CentrifugoBundle\Command\Option\OptionBase64DataTrait;
use Fresh\CentrifugoBundle\Command\Option\OptionBase64InfoTrait;
use Fresh\CentrifugoBundle\Command\Option\OptionClientTrait;
use Fresh\CentrifugoBundle\Command\Option\OptionDataTrait;
use Fresh\CentrifugoBundle\Command\Option\OptionEpochTrait;
use Fresh\CentrifugoBundle\Command\Option\OptionInfoTrait;
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
    use OptionBase64DataTrait;
    use OptionBase64InfoTrait;
    use OptionClientTrait;
    use OptionDataTrait;
    use OptionEpochTrait;
    use OptionInfoTrait;
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
            $channelArgument = new InputArgument('channel', InputArgument::REQUIRED, 'Name of channel to subscribe user to', null, $this->getChannelsForAutocompletion());
        } else {
            $channelArgument = new InputArgument('channel', InputArgument::REQUIRED, 'Name of channel to subscribe user to');
        }

        $this
            ->setDefinition(
                new InputDefinition([
                    new InputArgument('user', InputArgument::REQUIRED, 'User ID to subscribe'),
                    $channelArgument,
                    new InputOption('info', null, InputOption::VALUE_OPTIONAL, 'Attach custom data to subscription (will be used in presence and join/leave messages)'),
                    new InputOption('base64info', null, InputOption::VALUE_OPTIONAL, 'Info in base64 for binary mode (will be decoded by Centrifugo)'),
                    new InputOption('client', null, InputOption::VALUE_OPTIONAL, 'Specific client ID to subscribe (user still required to be set, will ignore other user connections with different client IDs)'),
                    new InputOption('session', null, InputOption::VALUE_OPTIONAL, 'Specific client session to subscribe (user still required to be set)'),
                    new InputOption('data', null, InputOption::VALUE_OPTIONAL, 'Custom subscription data (will be sent to client in Subscribe push)'),
                    new InputOption('base64data', null, InputOption::VALUE_OPTIONAL, 'Same as data but in base64 format (will be decoded by Centrifugo)'),
                    new InputOption('offset', null, InputOption::VALUE_OPTIONAL, 'Offset in a stream'),
                    new InputOption('epoch', null, InputOption::VALUE_OPTIONAL, 'Stream epoch'),
                ])
            )
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command allows subscribing user to a channel:

<info>%command.full_name%</info> <comment>user123</comment> <comment>channelName</comment>

You can attach custom data to subscription in json or base64 format:

<info>%command.full_name%</info> <comment>user123</comment> <comment>channelName</comment> <comment>--info='{"foo":"bar"}'</comment>
<info>%command.full_name%</info> <comment>user123</comment> <comment>channelName</comment> <comment>--base64info=SGVsbG8gd29ybGQ=</comment>

You can subscribe specific user to a channel by client ID (user still required to be set):

<info>%command.full_name%</info> <comment>user123</comment> <comment>channelName</comment> <comment>--client=clientID</comment>

You can subscribe specific user to a channel by session ID (user still required to be set):

<info>%command.full_name%</info> <comment>user123</comment> <comment>channelName</comment> <comment>--session=sessionID</comment>

You can set custom subscription data in json or base64 format:

<info>%command.full_name%</info> <comment>user123</comment> <comment>channelName</comment> <comment>--data='{"foo":"bar"}'</comment>
<info>%command.full_name%</info> <comment>user123</comment> <comment>channelName</comment> <comment>--base64data=SGVsbG8gd29ybGQ=</comment>

You can set a specific stream position to recover from:

<info>%command.full_name%</info> <comment>user123</comment> <comment>channelName</comment> <comment>--offset=10 --epoch=ABCD</comment>

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
        $this->initializeB64DataOption($input);
        $this->initializeB64InfoOption($input);
        $this->initializeClientOption($input);
        $this->initializeDataOption($input);
        $this->initializeInfoOption($input);
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
                info: $this->info,
                base64Info: $this->base64info,
                client: $this->client,
                session: $this->session,
                data: $this->data,
                base64Data: $this->base64data,
                recoverSince: new StreamPosition($this->offset, $this->epoch),
            );
            $io->success('DONE');
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
