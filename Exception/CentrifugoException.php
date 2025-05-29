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

namespace Fresh\CentrifugoBundle\Exception;

use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * CentrifugoException.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class CentrifugoException extends \Exception implements ExceptionInterface
{
    /**
     * @param ResponseInterface $response
     * @param string            $message
     * @param int               $code
     * @param \Throwable|null   $previous
     */
    public function __construct(private readonly ResponseInterface $response, string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
