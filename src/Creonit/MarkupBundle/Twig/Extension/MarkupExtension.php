<?php

namespace Creonit\MarkupBundle\Twig\Extension;

use Creonit\MarkupBundle\Markup;

class MarkupExtension extends \Twig_Extension
{
    /**
     * @var Markup
     */
    protected $markup;

    public function __construct(Markup $markup)
    {
        $this->markup = $markup;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('shuffle', [$this, 'shuffle'])
        ];
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('load', [$this->markup, 'load']),
        ];
    }

    public function shuffle($array)
    {
        if ($array instanceof \Traversable) {
            $array = iterator_to_array($array, false);
        }
        shuffle($array);
        return $array;
    }
}