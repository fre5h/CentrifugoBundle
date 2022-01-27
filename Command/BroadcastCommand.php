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

use Fresh\CentrifugoBundle\Service\CentrifugoChecker;
use Fresh\CentrifugoBundle\Service\CentrifugoInterface;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * BroadcastCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class BroadcastCommand extends AbstractCommand
{
    use ArgumentDataTrait;

    protected static $defaultName = 'centrifugo:broadcast';

    /** @var string */
    protected static $defaultDescription = 'Publish data into many channels';

    private readonly CentrifugoChecker $centrifugoChecker;

    /** @var string[] */
    private array $channels;

    /**
     * @param CentrifugoInterface $centrifugo
     * @param CentrifugoChecker   $centrifugoChecker
     */
    public function __construct(CentrifugoInterface $centrifugo, CentrifugoChecker $centrifugoChecker)
    {
        $this->centrifugoChecker = $centrifugoChecker;

        parent::__construct($centrifugo);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->setDefinition(
                new InputDefinition([
                    new InputArgument('data', InputArgument::REQUIRED, 'Data in JSON format'),
                    new InputArgument('channels', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Channel names'),
                ])
            )
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command allows to publish data into many channels:

<info>%command.full_name%</info> <comment>'{"foo":"bar"}'</comment> </comment>channelAbc</comment> </comment>channelDef</comment>

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

        $this->initializeDataArgument($input);

        try {
            /** @var array<string> $channels */
            $channels = (array) $input->getArgument('channels');
            foreach ($channels as $channel) {
                $this->centrifugoChecker->assertValidChannelName($channel);
            }
            $this->channels = $channels;
        } catch (\Throwable $e) {
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
            $this->centrifugo->broadcast($this->data, $this->channels);
            $io->success('DONE');
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
