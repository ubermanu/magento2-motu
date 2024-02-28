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
        protected \Magento\Framework\App\Response\Http $response,
        protected \Psr\Log\LoggerInterface $logger,
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

        $islandName = $this->islandHelper->getIslandName();

        // Find the block in the layout and render it
        // The block must implement the IslandInterface
        $block = $this->layout->getBlock($islandName);

        // If the block does not exist or does not implement IslandInterface, we return an empty response
        if (!$block instanceof IslandInterface) {
            $html = '';
        } else {
            try {
                $html = $this->removeWhitespaces->filter($block->toHtml());
            } catch (\Exception $e) {
                $html = '';
                $this->logger->error($e->getMessage());
            }
        }

        // Override the response, return the HTML of the block
        $this->response->getHeaders()->clearHeaders();
        $this->response->setHeader('Content-Type', 'text/html');
        $this->response->setHeader('Content-Length', strlen($html));
        $this->response->setHttpResponseCode(200);
        $this->response->setBody($html);

        $this->response->sendResponse();
    }
}
