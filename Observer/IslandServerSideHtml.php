<?php

namespace Ubermanu\Motu\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Ubermanu\Motu\Helper\Island as IslandHelper;
use Ubermanu\Motu\View\Element\IslandInterface;

class IslandServerSideHtml implements ObserverInterface
{
    public function __construct(
        protected RequestInterface $request,
        protected IslandHelper $islandHelper
    ) {
    }

    /**
     * When rendering the block on the server side, we wrap it in a div
     * that automatically loads the block on the client side, using the
     * island name and the client method.
     *
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        $block = $observer->getData('block');
        $html = $observer->getData('transport')->getHtml();

        // Must implement IslandInterface and AbstractBlock
        if (!$block instanceof IslandInterface || !$block instanceof AbstractBlock) {
            return;
        }

        // Skip if the client method is not set or if we are rendering the page on the client side
        if ($this->islandHelper->isClientSideRendering() || !$block->getClientMethod()) {
            return;
        }

        $jsParams = $this->islandHelper->getJsParams($block);

        $html = <<<HTML
<div data-island="{$block->getNameInLayout()}" data-mage-init='{"islandRenderer":{$jsParams}}'>{$html}</div>
HTML;

        $observer->getData('transport')->setHtml($html);
    }
}
