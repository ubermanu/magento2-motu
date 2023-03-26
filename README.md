# Magento 2 - Motu

An implementation of the island pattern for Magento 2.

> The islands architecture encourages small, focused chunks of interactivity within server-rendered web pages. The output of islands is progressively enhanced HTML, with more specificity around how the enhancement occurs. Rather than a single application being in control of full-page rendering, there are multiple entry points. The script for these "islands" of interactivity can be delivered and hydrated independently, allowing the rest of the page to be just static HTML.

From [Islands Architecture](https://www.patterns.dev/posts/islands-architecture/)

## Installation

```bash
composer require ubermanu/magento2-motu
```

## Quick start

Create a new island block:

```php
<?php

namespace Vendor\Module\Island;

class MyIsland extends \Ubermanu\Motu\View\Element\AbstractIsland
{
    public function getClientMethod(): string
    {
        return 'load';
    }
}
```

Add the block to your layout:

```xml
<?xml version="1.0"?>
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:View/Layout/etc/page_configuration.xsd">
    <body>
        <referenceContainer name="content">
            <block class="Vendor\Module\Island\MyIsland"
                   name="my_island"
                   template="Vendor_Module::island.phtml" />
        </referenceContainer>
    </body>
</page>
```

That's it! The island will be rendered in the page and hydrated by the client-side script.
