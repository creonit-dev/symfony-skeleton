<?php

namespace AppBundle\Service;

use Creonit\PageBundle\Model\PageQuery;
use Symfony\Component\DependencyInjection\ContainerInterface;


class PageService
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Получить страницу по URL
     * @param string $url
     * @return array
     */
    public function getPage($url)
    {
        try {
            $route = $this->container->get('router')->match($url);
        } catch (\Exception $exception) {
            return null;
        }

        if (preg_match('/^_page_(\d+)$/', $route['_route'], $match)) {
            $page = PageQuery::create()->findPk($match[1]);
        } else {
            $page = PageQuery::create()->findOneByName($route['_route']);
        }

        return $page;
    }

    /**
     * Получить страницу по имени
     * @param string $name
     * @return \Creonit\PageBundle\Model\Page
     */
    public function getPageByName($name)
    {
        return PageQuery::create()->findOneByName($name);
    }

}
