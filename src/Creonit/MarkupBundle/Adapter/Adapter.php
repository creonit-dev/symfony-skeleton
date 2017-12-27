<?php

namespace Creonit\MarkupBundle\Adapter;

use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class Adapter
{

    /** @var ContainerInterface */
    protected $container;

    /** @var mixed|object|\Symfony\Bundle\FrameworkBundle\Routing\Router */
    protected $router;

    /** @var null|\Symfony\Component\Routing\RouteCollection */
    protected $routeCollection;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->router = $container->get('router');
        $this->routeCollection = $this->router->getRouteCollection();
    }

    public function render(Request $request)
    {
        $loader = new \Twig_Loader_Filesystem($this->container->getParameter('kernel.project_dir') . '/markup/src/templates');
        $twig = new \Twig_Environment($loader, [
            'cache' => $this->container->getParameter('kernel.cache_dir') . '/markup',
            'auto_reload' => $this->container->getParameter('kernel.debug'),
            'strict_variables' => false
        ]);

        $loadedResources = [];

        $twig->addGlobal('app', [
            ''
        ]);

        $twig->addGlobal('request', [
            'path' => $request->getPathInfo(),
            'uri' => $request->getRequestUri(),
            'query' => $request->query->all(),
            'params' => $request->attributes->get('_route_params'),
            'cookies' => $request->cookies->all()
        ]);

        $twig->addExtension(new AssetExtension($this->container->get('assets.packages')));
        $twig->addExtension(new RoutingExtension($this->container->get('router')));

        $twig->addFunction(new \Twig_SimpleFunction('load', function($url, $query = []) use ($request, &$loadedResources) {
            $uri = '/api/' . $url;
            if ($query) {
                $uri .= '?' . http_build_query($query);
            }

            $this->container->get('debug.stopwatch')->start($uri);

            if (isset($loadedResources[$uri])) {
                $this->container->get('debug.stopwatch')->stop($uri);
                return $loadedResources[$uri];
            }

            //$subRequest = Request::create($uri, 'GET', [], $request->cookies->all(), [], $request->server->all());
            $subRequest = Request::create($uri, 'GET', [], [], [], $request->server->all());

            $kernel = $this->container->get('kernel');
            $kernel = new \AppCache($kernel);


            try {
                $content = $kernel->handle($subRequest, HttpKernelInterface::MASTER_REQUEST, false)->getContent();
                $this->container->get('debug.stopwatch')->stop($uri);
                return $loadedResources[$uri] = json_decode($content, true);

            } catch (NotFoundHttpException $exception) {
                return $loadedResources[$uri] = [];
            }
        }));

        $twig->addFunction(new \Twig_SimpleFunction('dump', function($data) {
            return '<pre>' . print_r($data, true) . '</pre>';
        }, ['is_safe' => ['html']]));


        if ($request->attributes->has('page')) {
            $template = $twig->load('static.twig');
        } else {
            $template = $twig->load($request->attributes->get('template') . '.twig');
        }


        try {
            return $this->response($request, $template->render());

        } catch (NotFoundHttpException $exception) {
            return $this->response($request, $twig->load('404.twig'));

        } catch (\Exception $exception) {
            throw $exception;
        }

    }

    public function response($request, $content)
    {
        $response = new Response($content);
        $response->setEtag(md5($content));
        $response->setPublic();
        $response->isNotModified($request);
        return $response;
    }

}

