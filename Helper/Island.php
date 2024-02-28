<?php

namespace Ubermanu\Motu\Helper;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Ubermanu\Motu\View\Element\IslandInterface;

class Island implements ArgumentInterface
{
    /**
     * Header to set the render mode.
     * @var string
     */
    const HEADER_ISLAND_NAME = 'X-Island-Name';

    public function __construct(
        protected \Magento\Framework\App\Request\Http $request,
    ) {
    }

    /**
     * @return string|null
     */
    public function getIslandName(): ?string
    {
        return $this->request->getHeader(self::HEADER_ISLAND_NAME) ?: null;
    }

    /**
     * If the island name is not set, it means we are rendering the page on the server side.
     * This is the default behavior.
     *
     * @return bool
     */
    public function isServerSideRendering(): bool
    {
        return empty($this->getIslandName());
    }

    /**
     * If the island name is set, it means we are rendering the page on the client side.
     * A controller observer will render the island block only.
     *
     * @return bool
     */
    public function isClientSideRendering(): bool
    {
        return !$this->isServerSideRendering();
    }

    /**
     * @param IslandInterface $block
     * @return string
     */
    public function getJsParams(IslandInterface $block): string
    {
        $currentUrl = $block->getUrl('*/*/*', [
            '_current' => true,
            '_use_rewrite' => true,
        ]);

        $params = [
            'islandName' => $block->getNameInLayout(),
            'renderMethod' => $block->getClientMethod(),
            'renderUrl' => $currentUrl,
        ];

        return json_encode($params);
    }
}
