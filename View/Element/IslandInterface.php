<?php

namespace Ubermanu\Motu\View\Element;

use Magento\Framework\View\Element\BlockInterface;

interface IslandInterface extends BlockInterface
{
    /**
     * Can be used to get the client method to call on the client side.
     * Allowed values are: "load", "idle", "visible", "media" and null.
     *
     * If the method is "load", the island will be shipped with a load event listener
     * that will call the client render method when the page is loaded.
     *
     * If the method is "idle", the island will be shipped with a requestIdleCallback
     * that will call the client render method when the browser is idle.
     *
     * If the method is "visible", the island will be shipped with an intersection observer
     * that will call the client render method when the island is visible.
     *
     * If the method is "media", the island will be shipped with a media query listener
     * that will call the client render method when the media query matches.
     * The media query must be passed as pipe separated string in the client directive.
     * Example: "media|screen and (min-width: 768px)"
     *
     * If the method is null, the island will be shipped normally (SSR).
     *
     * @return string|null
     */
    public function getClientMethod();

    /**
     * @return string
     */
    public function getNameInLayout();
}
