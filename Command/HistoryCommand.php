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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * HistoryCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class HistoryCommand extends AbstractCommand
{
    use ArgumentChannelTrait;

    protected static $defaultName = 'centrifugo:history';

    /** @var string */
    protected static $defaultDescription = 'Get channel history information (list of last messages published into channel)';

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
                    new InputArgument('channel', InputArgument::REQUIRED, 'Channel name'),
                ])
            )
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command allows to get channel history information (list of last messages published into channel):

<info>%command.full_name%</info> <comment>channelAbc</comment>

Read more at https://centrifugal.github.io/centrifugo/server/http_api/#history
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
            $data = $this->centrifugo->history($this->channel);

            if (!empty($data['publications'])) {
                $io->title('Publications');

                foreach ($data['publications'] as $info) {
                    $io->writeln(\json_encode($info['data'], \JSON_PRETTY_PRINT | \JSON_THROW_ON_ERROR));
                    $io->newLine();
                    $io->writeln('<info>------------</info>');
                    $io->newLine();
                }

                $io->newLine();
            } else {
                $io->success('NO DATA');
            }
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return $e->getCode();
        }

        return 0;
    }
}
