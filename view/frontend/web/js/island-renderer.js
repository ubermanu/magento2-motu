define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    $.widget('mage.islandRenderer', {
        options: {
            islandName: '',
            renderMethod: 'load',
            renderUrl: ''
        },

        /**
         * @private
         */
        _create: function () {
            const [renderMethod, ...renderOptions] = this.options.renderMethod.split('|');

            /** @type {(() => void | unknown)} */
            let unsubscribe;

            switch (renderMethod) {
                case 'load':
                    this._onLoad();
                    break;
                case 'idle':
                    unsubscribe = this._onIdle();
                    break;
                case 'visible':
                    unsubscribe = this._onVisible();
                    break;
                case 'media':
                    unsubscribe = this._onMedia(renderOptions);
                    break;
                default:
                    throw new Error(`Unknown render method: "${renderMethod}"`);
            }

            // This will be called when the widget is destroyed
            this._onDestroy = unsubscribe || $.noop;
        },

        /**
         * Load and hydrate the component JavaScript immediately on page load.
         *
         * @returns {void}
         * @private
         */
        _onLoad: function () {
            this.render();
        },

        /**
         * Load and hydrate the component JavaScript once the page is done with its initial load and the
         * requestIdleCallback event has fired.
         * If you are in a browser that doesn't support requestIdleCallback, then the document load event is used.
         *
         * @returns {() => void}
         * @private
         */
        _onIdle: function () {
            const self = this;

            if (window.requestIdleCallback) {
                const id = requestIdleCallback(self.render);
                return () => cancelIdleCallback(id);
            } else {
                $(window).on('load', self.render);
                return () => $(window).off('load', self.render);
            }
        },

        /**
         * Load and hydrate the component once it has entered the user’s viewport.
         * If `IntersectionObserver` is not supported, then the document load event is used.
         *
         * @returns {() => void}
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

                return () => observer.disconnect();
            } else {
                return this._onIdle();
            }
        },

        /**
         * Loads and hydrates the component JavaScript once a certain CSS media query is met.
         *
         * @param {string[]} options
         * @returns {() => void | unknown}
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
                    function _render() {
                        if (mediaQueryList.matches) {
                            self.render();
                        }
                    }

                    mediaQueryList.addListener(_render);

                    return () => mediaQueryList.removeListener(_render);
                }
            } else {
                return this._onIdle();
            }
        },

        /**
         * Send ajax request and render response in the container.
         * Once the result is rendered, the widget is destroyed, and subscriptions are removed.
         *
         * @returns {void}
         */
        render: function () {
            const self = this;

            $.ajax({
                url: this.options.renderUrl,
                headers: {
                    'X-Island-Name': this.options.islandName
                },
                success: function (response) {
                    self.element.html(response);
                    self.element.trigger('contentUpdated');

                    // Fire an event to let other components know that this island has been rendered
                    $(document).trigger('islandRendered', {
                        element: self.element,
                        method: self.options.renderMethod
                    });

                    self.destroy();
                },
            });
        },

        /**
         * @private
         */
        destroy: function () {
            this._onDestroy();
        }
    });

    return $.mage.islandRenderer;
})
