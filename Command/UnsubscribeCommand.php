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

use Fresh\CentrifugoBundle\Service\Centrifugo;
use Fresh\CentrifugoBundle\Service\CentrifugoChecker;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * UnsubscribeCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class UnsubscribeCommand extends AbstractCommand
{
    use ArgumentChannelTrait;
    use ArgumentUserTrait;

    protected static $defaultName = 'centrifugo:unsubscribe';

    /**
     * @param Centrifugo        $centrifugo
     * @param CentrifugoChecker $centrifugoChecker
     */
    public function __construct(Centrifugo $centrifugo, CentrifugoChecker $centrifugoChecker)
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
            ->setDescription('Unsubscribe user from channel')
            ->setDefinition(
                new InputDefinition([
                    new InputArgument('user', InputArgument::REQUIRED, 'User ID'),
                    new InputArgument('channel', InputArgument::REQUIRED, 'Channel name'),
                ])
            )
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command allows to unsubscribe user from channel:

<info>%command.full_name%</info> <comment>user123</comment> <comment>channelAbc</comment>

Read more at https://centrifugal.github.io/centrifugo/server/http_api/#unsubscribe
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
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->centrifugo->unsubscribe($this->user, $this->channel);
            $io->success('DONE');
        } catch (\Throwable $e) {
            $io->error($e->getMessage());
        }

        return 0;
    }
}
