<?php

namespace Ubermanu\Motu\View\Element;

use Magento\Framework\View\Element\AbstractBlock;

/**
 * @method $this setClientMethod(string $clientMethod)
 */
abstract class AbstractIsland extends AbstractBlock implements IslandInterface
{
    /**
     * @inheritDoc
     */
    public function getClientMethod()
    {
        return $this->getData('client_method') ?? 'load';
    }
}
