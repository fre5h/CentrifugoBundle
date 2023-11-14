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
 * PublishCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
#[AsCommand(name: 'centrifugo:publish', description: 'Publish data into a channel')]
final class PublishCommand extends AbstractCommand
{
    use ArgumentChannelTrait;
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
        if (Kernel::MAJOR_VERSION >= 6) { // @phpstan-ignore-line
            $channelArgument = new InputArgument('channel', InputArgument::REQUIRED, 'Name of channel to publish', null, $this->getChannelsForAutocompletion());
        } else { // @phpstan-ignore-line
            $channelArgument = new InputArgument('channel', InputArgument::REQUIRED, 'Name of channel to publish');
        }

        $this
            ->setDefinition(
                new InputDefinition([
                    new InputArgument('data', InputArgument::REQUIRED, 'Custom JSON data to publish into each channel'),
                    $channelArgument,
                    new InputOption('tags', null, InputOption::VALUE_OPTIONAL, 'Publication tags - map with arbitrary string keys and values which is attached to publication and will be delivered to clients'),
                    new InputOption('skipHistory', null, InputOption::VALUE_NONE, 'Skip adding publications to channels\' history for this request'),
                    new InputOption('base64data', null, InputOption::VALUE_OPTIONAL, 'Custom binary data to publish into a channel encoded to base64 so it\'s possible to use HTTP API to send binary to clients. Centrifugo will decode it from base64 before publishing.'),
                ])
            )
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command allows to publish data into a channel:

<info>%command.full_name%</info> <comment>'{"foo":"bar"}'</comment> <comment>channelName</comment>

You can skip adding publication to history for this request:

<info>%command.full_name%</info> <comment>'{"foo":"bar"}'</comment> <comment>channelName</comment> <comment>--skipHistory</comment>

You can add tags which are attached to publication and will be delivered to clients:

<info>%command.full_name%</info> <comment>'{"foo":"bar"}'</comment> <comment>channelName</comment> <comment>--tags='{"tag1":"value1","tag2":"value2"}'</comment>

You can add custom binary data to publish into a channel encoded to base64, so it's possible to use
HTTP API to send binary to clients. Centrifugo will decode it from base64 before publishing:

<info>%command.full_name%</info> <comment>'{"foo":"bar"}'</comment> <comment>channelName</comment> <comment>--base64data=SGVsbG8gd29ybGQ=</comment>

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
            $this->centrifugo->publish(
                data: $this->data,
                channel: $this->channel,
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
