<?php

namespace Creonit\MarkupBundle;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class Markup
{
    /**
     * @var RequestStack
     */
    protected $request;
    /**
     * @var Stopwatch
     */
    protected $stopwatch;
    /**
     * @var KernelInterface
     */
    protected $kernel;
    protected $debug;

    protected $resources = [];

    public function __construct(RequestStack $requestStack, Stopwatch $stopwatch, KernelInterface $kernel, $debug)
    {
        $this->request = $requestStack->getMasterRequest();
        $this->stopwatch = $stopwatch;
        $this->kernel = $kernel;
        $this->debug = $debug;
    }

    public function load($url, $query = [], $catchError = false)
    {
        $uri = '/api/' . $url;
        if ($query) {
            $uri .= '?' . http_build_query($query);
        }

        $this->stopwatch->start($uri);

        if (array_key_exists($uri, $this->resources)) {
            $this->stopwatch->stop($uri);
            return $this->resources[$uri];
        }

        $subRequest = Request::create($uri, 'GET', [], $this->request->cookies->all(), [], $this->request->server->all());

        $kernel = $this->kernel;
        $kernel = new \AppCache($kernel);

        $response = $kernel->handle($subRequest, HttpKernelInterface::MASTER_REQUEST, $catchError);

        $content = $response->getContent();
        $this->stopwatch->stop($uri);
        return $this->resources[$uri] = json_decode($content, true);
    }
}