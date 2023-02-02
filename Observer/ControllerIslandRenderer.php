<?php

namespace Ubermanu\Motu\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\LayoutInterface;
use Ubermanu\Motu\Helper\Island as IslandHelper;

/**
 * Get the "island" parameter from the request and render the associated block
 * in the layout.
 */
class ControllerIslandRenderer implements ObserverInterface
{
    public function __construct(
        protected LayoutInterface $layout,
        protected IslandHelper $islandHelper
    ) {
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        if ($this->islandHelper->isServerSideRendering()) {
            return;
        }

        $controller = $observer->getControllerAction();
        $response = $controller->getResponse();

        $islandName = $this->islandHelper->getIslandName();

        // Find the block in the layout and render it
        // The block must implement the IslandInterface
        $block = $this->layout->getBlock($islandName);

        if (!$block instanceof \Ubermanu\Motu\View\Element\IslandInterface) {
            $response->setBody('');
        } else {
            $response->setBody($block->toHtml());
        }

        $response->sendResponse();
    }
}
