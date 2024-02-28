<?php

namespace Ubermanu\Motu\Filter;

use Laminas\Filter\FilterInterface;

class RemoveWhitespaces implements FilterInterface
{
    /**
     * Remove whitespaces from the input HTML.
     *
     * @inheritDoc
     */
    public function filter($value)
    {
        $htmlMin = new \Abordage\HtmlMin\HtmlMin();
        $htmlMin->findDoctypeInDocument(false);

        return $htmlMin->minify($value);
    }
}
