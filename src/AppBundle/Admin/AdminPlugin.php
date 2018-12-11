<?php

namespace AppBundle\Admin;

use Creonit\AdminBundle\Plugin as BasePlugin;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AdminPlugin extends BasePlugin
{

    protected $templating;
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->templating = $container->get('templating');
    }

    public function configure()
    {
        //$this->addInjection('sidebar_before_menu', $this->templating->render('admin/sidebar_before_menu.html.twig'));
        //$this->addInjection('sidebar_after_menu', $this->templating->render('admin/sidebar_after_menu.html.twig'));
        // $this->addJavascript('/js/admin.js');
        // $this->addStylesheet('/css/admin.css');
        // $this->addInjection('head_script', 'delete CreonitContentTypes.text;');

    }
}