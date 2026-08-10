<?php
/**
 * Copyright © Bazaarvoice, Inc. All rights reserved.
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Bazaarvoice\Connector\Logger;

use Bazaarvoice\Connector\Api\ConfigProviderInterface;
use Exception;
use Magento\Framework\App\State;

/**
 * Class Logger
 *
 * @package Bazaarvoice\Connector\Logger
 */
class Logger extends \Monolog\Logger
{
    /**
     * @var bool
     */
    protected $admin = false;
    /**
     * @var ConfigProviderInterface
     */
    private $configProvider;

    /**
     * Logger constructor.
     *
     * @param string                                    $name
     * @param ConfigProviderInterface                   $configProvider
     * @param State                                     $state
     *
     * @param array|\Monolog\Handler\HandlerInterface[] $handlers
     *
     * @codingStandardsIgnoreStart
     */
    public function __construct(
        $name,
        ConfigProviderInterface $configProvider,
        State $state,
        array $handlers = array()
    ) {
        try {
            $this->admin = $state->getAreaCode() === 'adminhtml';
        } catch (Exception $e) {
        }
        parent::__construct($name, $handlers);
        $this->configProvider = $configProvider;
        /** @codingStandardsIgnoreEnd */
    }

    /**
     * Gate debug output on the module's own debug flag.
     *
     * The parameter is left untyped deliberately. Monolog 3 narrows this to
     * `string|\Stringable`, but callers in this module pass arrays, and PHP
     * permits a subclass to WIDEN a parameter type. Narrowing here would turn
     * existing array call sites into TypeErrors. The `void` return is required:
     * Monolog 3 declares it, and a subclass must match.
     *
     * @param string|array|\Stringable $message
     * @param array                    $context
     *
     * @return void
     */
    public function debug($message, array $context = []): void
    {
        if ($this->configProvider->isDebugEnabled()) {
            $this->addRecord(static::DEBUG, $message, $context);
        }
    }

    /**
     * Stringify array messages and echo to stdout under CLI or adminhtml.
     *
     * Signature tracks Monolog 3: the fourth `$datetime` argument must be
     * accepted and forwarded, and the return type must be `bool`. Parameters
     * are widened rather than narrowed so array messages still reach the
     * `print_r` branch below instead of failing a type check on entry.
     *
     * @param int|\Monolog\Level                              $level
     * @param string|array|\Stringable                        $message
     * @param array                                           $context
     * @param \Monolog\JsonSerializableDateTimeImmutable|null $datetime
     *
     * @return bool
     */
    public function addRecord($level, $message, array $context = [], $datetime = null): bool
    {
        if (is_array($message)) {
            $message = print_r($message, $return = true);
        }

        if (php_sapi_name() == "cli" || $this->admin) {
            print_r($message."\n");
        }

        return parent::addRecord($level, (string)$message, $context, $datetime);
    }
}
