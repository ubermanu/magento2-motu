<?php

namespace Ubermanu\Motu\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Ubermanu\Motu\Helper\Island as IslandHelper;
use Ubermanu\Motu\View\Element\AbstractIsland;

class IslandServerSideHtml implements ObserverInterface
{
    public function __construct(
        protected RequestInterface $request,
        protected IslandHelper $islandHelper
    ) {
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        $block = $observer->getData('block');
        $html = $observer->getData('transport')->getHtml();

        if (!$block instanceof AbstractIsland) {
            return;
        }

        // When rendering the block on the server side, we wrap it in a div
        // that automatically loads the block on the client side, using the
        // island name and the client method.
        if ($this->islandHelper->isServerSideRendering()) {
            if (!$block->getClientMethod()) {
                return;
            }

            $jsParams = $this->islandHelper->getJsParams($block);

            $html = <<<HTML
<div data-island="{$block->getNameInLayout()}" data-mage-init='{"islandRenderer":{$jsParams}}'>{$html}</div>
HTML;

            $observer->getData('transport')->setHtml($html);
        }
    }
}
