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

use Fresh\CentrifugoBundle\Command\Argument\ArgumentChannelsTrait;
use Fresh\CentrifugoBundle\Command\Argument\ArgumentDataTrait;
use Fresh\CentrifugoBundle\Command\Option\OptionBase64DataTrait;
use Fresh\CentrifugoBundle\Command\Option\OptionSkipHistoryTrait;
use Fresh\CentrifugoBundle\Command\Option\OptionTagsTrait;
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
 * BroadcastCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
#[AsCommand(name: 'centrifugo:broadcast', description: 'Publish same data into many channels')]
final class BroadcastCommand extends AbstractCommand
{
    use ArgumentChannelsTrait;
    use ArgumentDataTrait;
    use OptionBase64DataTrait;
    use OptionSkipHistoryTrait;
    use OptionTagsTrait;

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
            $channelsArgument = new InputArgument('channels', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'List of channels to publish data to', null, $this->getChannelsForAutocompletion());
        } else {
            $channelsArgument = new InputArgument('channels', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'List of channels to publish data to');
        }

        $this
            ->setDefinition(
                new InputDefinition([
                    new InputArgument('data', InputArgument::REQUIRED, 'Data in JSON format'),
                    $channelsArgument,
                    new InputOption('tags', null, InputOption::VALUE_OPTIONAL, 'Publication tags - map with arbitrary string keys and values which is attached to publication and will be delivered to clients'),
                    new InputOption('skipHistory', null, InputOption::VALUE_NONE, 'Skip adding publication to history for this request'),
                    new InputOption('base64data', null, InputOption::VALUE_OPTIONAL, 'Custom binary data to publish into a channel encoded to base64 so it\'s possible to use HTTP API to send binary to clients. Centrifugo will decode it from base64 before publishing.'),
                ])
            )
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command allows to publish same data into many channels:

<info>%command.full_name%</info> <comment>'{"foo":"bar"}'</comment> </comment>channelName1</comment> </comment>channelName2</comment>

You can skip adding publication to history for this request:

<info>%command.full_name%</info> <comment>'{"foo":"bar"}'</comment> </comment>channelName1</comment> </comment>channelName2</comment> <comment>--skipHistory</comment>

You can add tags which are attached to publication and will be delivered to clients:

<info>%command.full_name%</info> <comment>'{"foo":"bar"}'</comment> </comment>channelName1</comment> </comment>channelName2</comment> <comment>--tags='{"tag1":"value1","tag2":"value2"}'</comment>

You can add custom binary data to publish into a channel encoded to base64, so it's possible to use
HTTP API to send binary to clients. Centrifugo will decode it from base64 before publishing:

<info>%command.full_name%</info> <comment>'{"foo":"bar"}'</comment> </comment>channelName1</comment> </comment>channelName2</comment> <comment>--base64data=SGVsbG8gd29ybGQ=</comment>

Where <comment>SGVsbG8gd29ybGQ=</comment> is base64 encoded version of <comment>Hello world</comment>

Read more at https://centrifugal.dev/docs/server/server_api#broadcast
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

        $this->initializeChannelsArgument($input);
        $this->initializeDataArgument($input);
        $this->initializeTagsOption($input);
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
            $this->centrifugo->broadcast(
                data: $this->data,
                channels: $this->channels,
                skipHistory: $this->skipHistory,
                tags: $this->tags,
                base64data: $this->base64data,
            );
            $io->success('DONE');
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
