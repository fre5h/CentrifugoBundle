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
 * ChannelsCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class ChannelsCommand extends AbstractCommand
{
    use ArgumentPatternTrait;

    protected static $defaultName = 'centrifugo:channels';

    /** @var string */
    protected static $defaultDescription = 'Return active channels (with one or more active subscribers in it)';

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->setDefinition(
                new InputDefinition([
                    new InputArgument('pattern', InputArgument::OPTIONAL, 'Pattern'),
                ])
            )
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command returns active channels (with one or more active subscribers in it):

    <info>bin/console %command.name%</info>
    
You can optionally specify the <comment>pattern</comment> to filter channels by names:

    <info>bin/console %command.name% abc</info>    

Read more at https://centrifugal.dev/docs/server/server_api#channels
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

        $this->initializePatternArgument($input);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $data = $this->centrifugo->channels($this->pattern);

            if (!empty($data['channels'])) {
                $io->title('Channels');
                $io->listing($data['channels']);
                $io->text(\sprintf('<info>TOTAL</info>: %d', \count($data['channels'])));
            } else {
                $io->success('NO DATA');
            }
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return $e->getCode();
        }

        return self::SUCCESS;
    }
}
