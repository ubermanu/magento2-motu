<?php

namespace Ubermanu\Motu\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Ubermanu\Motu\View\Element\IslandInterface;

/**
 * Get the "island" parameter from the request and render the associated block
 * in the layout.
 */
class ControllerIslandRenderer implements ObserverInterface
{
    public function __construct(
        protected \Magento\Framework\View\LayoutInterface $layout,
        protected \Ubermanu\Motu\Helper\Island $islandHelper,
        protected \Ubermanu\Motu\Filter\RemoveWhitespaces $removeWhitespaces,
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

        if (!$block instanceof IslandInterface) {
            $response->setBody('');
        } else {
            $response->setBody($this->removeWhitespaces->filter($block->toHtml()));
        }

        $response->sendResponse();
    }
}
