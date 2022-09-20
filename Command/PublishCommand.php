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
use Fresh\CentrifugoBundle\Command\Argument\ArgumentDataTrait;
use Fresh\CentrifugoBundle\Command\Argument\ArgumentTagsTrait;
use Fresh\CentrifugoBundle\Command\Argument\OptionB64DataTrait;
use Fresh\CentrifugoBundle\Command\Argument\OptionSkipHistoryTrait;
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
 * PublishCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
#[AsCommand(name: 'centrifugo:publish', description: 'Publishes data into a channel')]
final class PublishCommand extends AbstractCommand
{
    use ArgumentChannelTrait;
    use ArgumentDataTrait;
    use ArgumentTagsTrait;
    use OptionB64DataTrait;
    use OptionSkipHistoryTrait;

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
                    new InputArgument('data', InputArgument::REQUIRED, 'Custom JSON data to publish into a channel'),
                    new InputArgument('channel', InputArgument::REQUIRED, 'Name of channel to publish'),
                    new InputArgument('tags', InputArgument::OPTIONAL, 'Publication tags - map with arbitrary string keys and values which is attached to publication and will be delivered to clients'),
                    new InputOption('skipHistory', 's', InputOption::VALUE_NONE, 'Skip adding publication to history for this request'),
                    new InputOption('b64data', 'b', InputOption::VALUE_OPTIONAL, 'Custom binary data to publish into a channel encoded to base64 so it\'s possible to use HTTP API to send binary to clients. Centrifugo will decode it from base64 before publishing.'),
                ])
            )
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command allows to publish data into a channel:

<info>%command.full_name%</info> <comment>'{"foo":"bar"}'</comment> <comment>channelName</comment>

You can skip adding publication to history for this request:

<info>%command.full_name%</info> <comment>'{"foo":"bar"}'</comment> <comment>channelName</comment> <comment>--skipHistory</comment>
or
<info>%command.full_name%</info> <comment>'{"foo":"bar"}'</comment> <comment>channelName</comment> <comment>-s</comment>

You can add tags which are attached to publication and will be delivered to clients:

<info>%command.full_name%</info> <comment>'{"foo":"bar"}'</comment> <comment>channelName</comment> <comment>'{"tag1":"value1","tag2":"value2"}'</comment>

You can add custom binary data to publish into a channel encoded to base64, so it's possible to use HTTP API to send binary to clients.
Centrifugo will decode it from base64 before publishing:

<info>%command.full_name%</info> <comment>'{"foo":"bar"}'</comment> <comment>channelName</comment> <comment>--b64data SGVsbG8gd29ybGQ=</comment>
or
<info>%command.full_name%</info> <comment>'{"foo":"bar"}'</comment> <comment>channelName</comment> <comment>--b SGVsbG8gd29ybGQ=</comment>

Where <comment>SGVsbG8gd29ybGQ=</comment> is base64 encoded version of <comment>Hello world</comment>

Read more at https://centrifugal.dev/docs/server/server_api#publish
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

        $this->initializeDataArgument($input);
        $this->initializeChannelArgument($input);
        $this->initializeTagsArgument($input);
        $this->initializeSkipHistoryOption($input);
        $this->initializeB64DataOption($input);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->centrifugo->publish(
                data: $this->data,
                channel: $this->channel,
                skipHistory: $this->skipHistory,
                tags: $this->tags,
                b64data: $this->b64data,
            );
            $io->success('DONE');
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
