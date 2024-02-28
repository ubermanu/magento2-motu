<?php

namespace Ubermanu\Motu\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Ubermanu\Motu\View\Element\IslandInterface;

class Island extends AbstractHelper
{
    /**
     * Header to set the render mode.
     * @var string
     */
    const HEADER_ISLAND_CODE = 'X-Island-Code';

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        protected \Magento\Framework\App\Request\Http $request,
        protected \Magento\Framework\Serialize\Serializer\Json $json,
        protected \Magento\Framework\Encryption\EncryptorInterface $encryptor,
    ) {
        parent::__construct($context);
    }

    /**
     * @return string|null
     */
    protected function getIslandCode(): ?string
    {
        return $this->request->getHeader(self::HEADER_ISLAND_CODE) ?: null;
    }

    /**
     * @return string|null
     */
    public function getIslandName(): ?string
    {
        return $this->encryptor->decrypt($this->getIslandCode()) ?: null;
    }

    /**
     * If the island code is not set, it means we are rendering the page on the server side.
     * This is the default behavior.
     *
     * @return bool
     */
    public function isServerSideRendering(): bool
    {
        return empty($this->getIslandCode());
    }

    /**
     * If the island code is set, it means we are rendering the page on the client side.
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
        $currentUrl = $this->_urlBuilder->getUrl('*/*/*', [
            '_current' => true,
            '_use_rewrite' => true,
        ]);

        $params = [
            'islandCode' => $this->encryptor->encrypt($block->getNameInLayout()),
            'renderMethod' => $block->getClientMethod(),
            'renderUrl' => $currentUrl,
        ];

        return $this->json->serialize($params);
    }
}
