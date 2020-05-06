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

namespace Fresh\CentrifugoBundle\Service;

use Fresh\CentrifugoBundle\Exception\CentrifugoErrorException;
use Fresh\CentrifugoBundle\Exception\CentrifugoException;
use Fresh\CentrifugoBundle\Model\BatchRequest;
use Fresh\CentrifugoBundle\Model\CommandInterface;
use Fresh\CentrifugoBundle\Model\ResultableCommandInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * ResponseProcessor.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class ResponseProcessor
{
    /** @var CentrifugoChecker */
    private $centrifugoChecker;

    /**
     * @param CentrifugoChecker $centrifugoChecker
     */
    public function __construct(CentrifugoChecker $centrifugoChecker)
    {
        $this->centrifugoChecker = $centrifugoChecker;
    }

    /**
     * @param CommandInterface  $command
     * @param ResponseInterface $response
     *
     * @return array|null
     */
    public function processResponse(CommandInterface $command, ResponseInterface $response): ?array
    {
        $this->centrifugoChecker->assertValidResponseStatusCode($response);
        $this->centrifugoChecker->assertValidResponseHeaders($response);
        $this->centrifugoChecker->assertValidResponseContentType($response);

        $content = $response->getContent();

        if ($command instanceof BatchRequest) {
            $contents = \explode("\n", $content);
            $result = [];
            $commands = $command->getCommands();

            foreach ($contents as $innerContent) {
                $result[] = $this->decodeAndProcessResponseResult($commands->current(), $innerContent);
                $commands->next();
            }

            return $result;
        }

        return $this->decodeAndProcessResponseResult($command, $content);
    }

    /**
     * @param CommandInterface $command
     * @param string           $content
     *
     * @throws CentrifugoException
     * @throws CentrifugoErrorException
     *
     * @return array|null
     */
    private function decodeAndProcessResponseResult(CommandInterface $command, string $content): ?array
    {
        try {
            $data = \json_decode($content, true, 512, \JSON_THROW_ON_ERROR);
        } catch (\Exception $e) {
            throw new CentrifugoException('Centrifugo response payload is not a valid JSON');
        }

        if (isset($data['error'])) {
            throw new CentrifugoErrorException($data['error']['message'], $data['error']['code']);
        }

        if ($command instanceof ResultableCommandInterface) {
            return $data['result'];
        }

        return null;
    }
}
