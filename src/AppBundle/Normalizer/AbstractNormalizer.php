<?php

namespace AppBundle\Normalizer;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;

abstract class AbstractNormalizer implements NormalizerInterface
{
    /** @var ContainerInterface */
    protected $container;

    /** @var Serializer */
    protected $serializer;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function setSerializer(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function hasGroup($context, $group)
    {
        return isset($context['groups']) and in_array($group, $context['groups']);
    }

}