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

/**
 * PresenceCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
#[AsCommand(
    name: 'centrifugo:presence',
    description: 'Get channel presence information (all clients currently subscribed on this channel)',
)]
final class PresenceCommand extends AbstractCommand
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
        $channelArgument = new InputArgument('channel', InputArgument::REQUIRED, 'Name of channel to call presence from', null, $this->getChannelsForAutocompletion());

        $this
            ->setDefinition(
                new InputDefinition([
                    $channelArgument,
                ])
            )
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command allows to get channel presence information (all clients currently subscribed on this channel):

<info>%command.full_name%</info> <comment>channelName</comment>

Read more at https://centrifugal.dev/docs/server/server_api#presence
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
            $data = $this->centrifugo->presence($this->channel);

            if (!empty($data['presence'])) {
                $io->title('Presence');

                foreach ($data['presence'] as $id => $info) {
                    $io->text(\sprintf('<info>%s</info>', $id));
                    $io->text(\sprintf('  ├ client: <comment>%s</comment>', $info['client']));
                    if (isset($info['conn_info'])) {
                        $io->text('  ├ conn_info:');
                        $io->write($this->formatInfo($info['conn_info']));
                    }
                    if (isset($info['chan_info'])) {
                        $io->text('  ├ chan_info:');
                        $io->write($this->formatInfo($info['chan_info']));
                    }
                    $io->text(\sprintf('  └ user: <comment>%s</comment>', $info['user']));
                }

                $io->newLine();
            } else {
                $io->success('NO DATA');
            }
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * @param array<mixed> $connInfo
     *
     * @return string
     */
    private function formatInfo(array $connInfo): string
    {
        $json = \json_encode($connInfo, \JSON_PRETTY_PRINT | \JSON_THROW_ON_ERROR);

        return array_reduce(
            \explode("\n", $json),
            static function (string $jsonWithPadding, string $line) {
                return \sprintf("%s   │ <comment>%s</comment>\n", $jsonWithPadding, $line);
            },
            ''
        );
    }
}
