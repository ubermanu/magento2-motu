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

    /**
     * @inheritDoc
     */
    public function toHtml()
    {
        $html = parent::toHtml();

        // If we are rendering the island, return the html as is.
        $island = $this->_request->getParam('island');
        if ($island === $this->getNameInLayout()) {
            return $html;
        }

        // If the block is an island, wrap it in a div with the data-island attribute.
        // Autoload the island client render method on the client side with the data-mage-init attribute.
        $clientMethod = $this->getClientMethod();

        if (!$clientMethod) {
            return $html;
        }

        $jsParams = [
            'renderMethod' => $clientMethod,
            'blockName' => $this->getNameInLayout(),
        ];

        return <<<HTML
        <div data-island="{$this->getNameInLayout()}" data-mage-init='{"islandRenderer":{$jsParams}}'>{$html}</div>
HTML;
    }
}
