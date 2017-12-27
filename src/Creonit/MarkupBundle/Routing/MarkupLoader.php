<?php

namespace Creonit\MarkupBundle\Routing;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Yaml\Yaml;

class MarkupLoader implements LoaderInterface
{
    private $loaded = false;
    private $controller;

    public function __construct()
    {
    }

    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "markup" loader twice');
        }

        $routes = new RouteCollection();


        $markupRoutes = Yaml::parse(file_get_contents(__DIR__ . '/../../../../markup/routes.yaml'));

        foreach($markupRoutes as $key => $markupRoute){
            $routes->add($key, new Route($markupRoute['path'], [
                '_controller' => 'creonit_markup.adapter:render',
                'template' => $markupRoute['template']
            ]));
        }

        $this->loaded = true;

        return $routes;
    }

    public function supports($resource, $type = null)
    {
        return 'markup' === $type;
    }

    public function getResolver()
    {
    }

    public function setResolver(LoaderResolverInterface $resolver)
    {
    }
}