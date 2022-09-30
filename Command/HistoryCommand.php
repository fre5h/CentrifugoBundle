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

use App\Kernel;
use Fresh\CentrifugoBundle\Command\Argument\ArgumentChannelTrait;
use Fresh\CentrifugoBundle\Command\Option\OptionEpochTrait;
use Fresh\CentrifugoBundle\Command\Option\OptionLimitTrait;
use Fresh\CentrifugoBundle\Command\Option\OptionOffsetTrait;
use Fresh\CentrifugoBundle\Command\Option\OptionReverseTrait;
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

/**
 * HistoryCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
#[AsCommand(name: 'centrifugo:history', description: 'Get channel history information (list of last messages published into channel)')]
final class HistoryCommand extends AbstractCommand
{
    use ArgumentChannelTrait;
    use OptionEpochTrait;
    use OptionLimitTrait;
    use OptionOffsetTrait;
    use OptionReverseTrait;

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
        if (Kernel::MAJOR_VERSION >= 6) {
            $channelArgument = new InputArgument('channel', InputArgument::REQUIRED, 'Channel name', null, $this->getChannelsForAutocompletion());
        } else {
            $channelArgument = new InputArgument('channel', InputArgument::REQUIRED, 'Channel name');
        }

        $this
            ->setDefinition(
                new InputDefinition([
                    $channelArgument,
                    new InputOption('limit', null, InputOption::VALUE_OPTIONAL, 'Limit number of returned publications, if not set in request then only current stream position information will present in result (without any publications)', 10),
                    new InputOption('offset', null, InputOption::VALUE_OPTIONAL, 'Offset in a stream'),
                    new InputOption('epoch', null, InputOption::VALUE_OPTIONAL, 'Stream epoch'),
                    new InputOption('reverse', null, InputOption::VALUE_NONE, 'Iterate in reversed order (from latest to earliest)'),
                ])
            )
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command allows getting channel history information (list of last messages published into the channel):

<info>%command.full_name%</info> <comment>channelName</comment>

You can limit number of returned publications:

<info>%command.full_name%</info> <comment>channelName</comment> <comment>--limit=10</comment>

You can iterate publications in reversed order:

<info>%command.full_name%</info> <comment>channelName</comment> <comment>--reverse</comment>

You can set specific offset and epoch to iterate over publications:

<info>%command.full_name%</info> <comment>channelName</comment> <comment>--offset=10 --epoch=ABCD</comment>

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

        $this->initializeChannelArgument($input);
        $this->initializeEpochOption($input);
        $this->initializeLimitOption($input);
        $this->initializeOffsetOption($input);
        $this->initializeReverseOption($input);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $data = $this->centrifugo->history(
                channel: $this->channel,
                reverse: $this->reverse,
                limit: $this->limit,
                streamPosition: new StreamPosition($this->offset, $this->epoch),
            );

            if (!empty($data['publications'])) {
                $io->title('Publications');

                foreach ($data['publications'] as $info) {
                    $io->writeln(\json_encode($info['data'], \JSON_PRETTY_PRINT | \JSON_THROW_ON_ERROR));
                    $io->newLine();
                    $io->writeln('<info>------------</info>');
                    $io->newLine();
                }

                $io->text(\sprintf('<info>Limit</info>: %d', $this->limit));
                $io->text(\sprintf('<info>Offset</info>: %d', $data['offset']));
                $io->text(\sprintf('<info>Epoch</info>: %s', $data['epoch']));

                $io->newLine();
            } else {
                $io->success('NO DATA');
            }
        } catch (\Throwable $e) {
            echo $e->getMessage();
            $io->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
