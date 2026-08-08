<?php
/**
 * Copyright © Bazaarvoice, Inc. All rights reserved.
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Bazaarvoice\Connector\Model;

/**
 * Class XMLWriter
 *
 * @package Bazaarvoice\Connector\Model
 */
class XMLWriter extends \XMLWriter
{
    /**
     * PHP 8.1 gave \XMLWriter::writeElement() a tentative `bool` return type.
     * This override returns null on the cdata path and discards the parent's
     * result on the other, so it is not `bool`-compatible.
     *
     * #[\ReturnTypeWillChange] is used rather than declaring `: bool` because it
     * keeps runtime behaviour byte-for-byte identical. Declaring a return type
     * would change what all 28 call sites receive, and the parent's own result
     * is currently discarded. Declaring the type properly - and returning the
     * parent's result - is the right follow-up, but it is a behavioural change
     * that wants its own verification pass rather than riding a cutover.
     *
     * @param string $name
     * @param null   $content
     * @param bool   $cdata
     *
     * @return bool|void
     */
    #[\ReturnTypeWillChange]
    public function writeElement($name, $content = null, $cdata = false)
    {
        $content = trim((string)$content);
        if ($cdata) {
            $this->startElement($name);
            $this->writeCdata($content);
            $this->endElement();
        } else {
            parent::writeElement($name, $content);
        }
    }

    /**
     * Same tentative-return-type situation as writeElement() above.
     *
     * @param null $content
     * @param bool $cdata
     *
     * @return bool|void
     */
    #[\ReturnTypeWillChange]
    public function writeRaw($content = null, $cdata = false)
    {
        $content = trim((string)$content);
        if ($cdata) {
            $this->writeCdata($content);
        } else {
            parent::writeRaw($content);
        }
    }
}
