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

use Fresh\CentrifugoBundle\DataCollector\CentrifugoCollector;
use Fresh\CentrifugoBundle\Logger\CommandHistoryLogger;
use Fresh\CentrifugoBundle\Service\CentrifugoChecker;
use Fresh\CentrifugoBundle\Service\ResponseProcessor;
use Fresh\DateTime\DateTimeHelper;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->bind('$centrifugoChannelMaxLength', '%centrifugo.channel_max_length%')
        ->bind('$centrifugoJwtTtl', '%centrifugo.jwt.ttl%')
        ->bind('$centrifugoSecret', '%centrifugo.secret%')
        ->bind('iterable $channelAuthenticators', new TaggedIteratorArgument('centrifugo.channel_authenticator'))
    ;

    $services->set(DateTimeHelper::class, DateTimeHelper::class);

    $services
        ->load('Fresh\CentrifugoBundle\\', __DIR__.'/../../{Command,Logger,Service}/')
        ->exclude([__DIR__.'/../../Service/Centrifugo.php'])
    ;

    $services
        ->set(CentrifugoCollector::class)
        ->tag('data_collector', ['id' => 'centrifugo', 'template' => '@FreshCentrifugo/data_collector/centrifugo.html.twig'])
    ;

    $services
        ->set(ResponseProcessor::class, ResponseProcessor::class)
        ->args([
            new ReferenceConfigurator(CentrifugoChecker::class),
            new ReferenceConfigurator(CommandHistoryLogger::class),
            (new ReferenceConfigurator('profiler'))->nullOnInvalid(),
        ])
    ;
};
