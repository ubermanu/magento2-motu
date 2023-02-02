<?php

namespace Ubermanu\Motu\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\LayoutInterface;

/**
 * Get the "island" parameter from the request and render the associated block
 * in the layout.
 */
class ControllerIslandRenderer implements ObserverInterface
{
    public function __construct(
        protected LayoutInterface $layout,
        protected RequestInterface $request,
        protected ResponseInterface $response
    ) {
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        $island = $this->request->getParam('island');

        // Find the block in the layout and render it
        // The block must implement the IslandInterface
        $block = $this->layout->getBlock($island);

        if (!$block) {
            $this->response->setBody('');
            return;
        }

        if (!$block instanceof \Ubermanu\Motu\View\Element\IslandInterface) {
            $this->response->setBody('');
            return;
        }

        $html = $block->toHtml();

        // Set the response body to the rendered block
        $this->response->setBody($html);
    }
}
