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
use Symfony\Component\Console\Command\Command;
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
final class DisconnectCommand extends Command
{
    protected static $defaultName = 'centrifugo:disconnect';

    /** @var Centrifugo */
    private $centrifugo;

    /** @var string */
    private $user;

    /**
     * @param Centrifugo $centrifugo
     */
    public function __construct(Centrifugo $centrifugo)
    {
        $this->centrifugo = $centrifugo;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Disconnect user by ID')
            ->setDefinition(
                new InputDefinition([
                    new InputArgument('user', InputArgument::REQUIRED, 'User ID'),
                ])
            )
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command allows to disconnect user by ID:

<info>%command.full_name%</info> <comment>user123</comment>

Read more at https://centrifugal.github.io/centrifugo/server/http_api/#disconnect
EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        parent::initialize($input, $output);

        $this->user = (string) $input->getArgument('user');
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
        } catch (\Exception $e) {
            $io->error($e->getMessage());
        }

        return 0;
    }
}
