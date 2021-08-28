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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * DisconnectCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class DisconnectCommand extends AbstractCommand
{
    use ArgumentUserTrait;

    protected static $defaultName = 'centrifugo:disconnect';

    /** @var string */
    protected static $defaultDescription = 'Disconnect user by ID';

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->setDefinition(
                new InputDefinition([
                    new InputArgument('user', InputArgument::REQUIRED, 'User ID'),
                ])
            )
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command allows to disconnect user by ID:

<info>%command.full_name%</info> <comment>user123</comment>

Read more at https://centrifugal.github.io/centrifugo/server/http_api/#disconnect
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
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->centrifugo->disconnect($this->user);
            $io->success('DONE');
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return $e->getCode();
        }

        return self::SUCCESS;
    }
}
