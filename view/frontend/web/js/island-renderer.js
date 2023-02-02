define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    $.widget('mage.islandRenderer', {
        options: {
            renderMethod: 'load',
            renderUrl: ''
        },

        /**
         * @private
         */
        _create: function () {
            const [renderMethod, ...renderOptions] = this.options.renderMethod.split('|');

            switch (renderMethod) {
                case 'load':
                    this._onLoad();
                    break;
                case 'idle':
                    this._onIdle();
                    break;
                case 'visible':
                    this._onVisible();
                    break;
                case 'media':
                    this._onMedia(renderOptions);
                    break;
                default:
                    throw new Error(`Unknown render method: "${renderMethod}"`);
            }
        },

        /**
         * Load and hydrate the component JavaScript immediately on page load.
         * @private
         */
        _onLoad: function () {
            this.render();
        },

        /**
         * Load and hydrate the component JavaScript once the page is done with its initial load and the requestIdleCallback event has fired.
         * If you are in a browser that doesn't support requestIdleCallback, then the document load event is used.
         * @private
         */
        _onIdle: function () {
            const self = this;

            if (window.requestIdleCallback) {
                requestIdleCallback(function () {
                    self.render();
                });
            } else {
                $(window).on('load', function () {
                    self.render();
                });
            }
        },

        /**
         * Load and hydrate the component JavaScript once the component has entered the user’s viewport.
         * This uses an IntersectionObserver internally to keep track of visibility.
         * @private
         */
        _onVisible: function () {
            const self = this;

            if (window.IntersectionObserver) {
                const observer = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            self.render();
                            observer.unobserve(self.element[0]);
                        }
                    });
                });

                observer.observe(this.element[0]);
            } else {
                this._onIdle();
            }
        },

        /**
         * Loads and hydrates the component JavaScript once a certain CSS media query is met.
         * @param options
         * @private
         */
        _onMedia: function (options) {
            const [mediaQuery] = options;
            const self = this;

            if (window.matchMedia) {
                const mediaQueryList = window.matchMedia(mediaQuery);

                if (mediaQueryList.matches) {
                    this.render();
                } else {
                    mediaQueryList.addListener(function () {
                        if (mediaQueryList.matches) {
                            self.render();
                        }
                    });
                }
            } else {
                this._onIdle();
            }
        },

        /**
         * Send ajax request and render response in the container
         * @private
         */
        render: function () {
            const self = this;

            $.ajax({
                url: this.options.renderUrl,
                success: function (response) {
                    self.element.html(response);
                    self.element.trigger('contentUpdated');
                },
            });
        },
    });

    return $.mage.islandRenderer;
})
