<?php

namespace Ubermanu\Motu\Helper;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Ubermanu\Motu\View\Element\IslandInterface;

class Island implements ArgumentInterface
{
    /**
     * Island parameter name in the request.
     * @var string
     */
    const PARAM_ISLAND_NAME = 'island_name';

    public function __construct(
        protected RequestInterface $request
    ) {
    }

    /**
     * @return string|null
     */
    public function getIslandName(): ?string
    {
        return $this->request->getParam(self::PARAM_ISLAND_NAME) ?? null;
    }

    /**
     * If the island name is not set, it means we are rendering the page on the server side.
     * This is the default behavior.
     * @return bool
     */
    public function isServerSideRendering(): bool
    {
        return empty($this->getIslandName());
    }

    /**
     * If the island name is set, it means we are rendering the page on the client side.
     * A controller observer will render the island block only.
     * @return bool
     */
    public function isClientSideRendering(): bool
    {
        return !$this->isServerSideRendering();
    }

    /**
     * @param AbstractBlock|IslandInterface $block
     * @return string
     */
    public function getJsParams(AbstractBlock|IslandInterface $block): string
    {
        $renderUrl = $block->getUrl('*/*/*', [
            '_current' => true,
            '_use_rewrite' => true,
            '_query' => [
                self::PARAM_ISLAND_NAME => $block->getNameInLayout(),
            ],
        ]);

        $params = [
            'islandName' => $block->getNameInLayout(),
            'renderMethod' => $block->getClientMethod(),
            'renderUrl' => $renderUrl,
        ];

        return json_encode($params);
    }
}
