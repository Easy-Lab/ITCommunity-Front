<?php

namespace App\Utils;

use Symfony\Component\DependencyInjection\ContainerInterface;

class Features
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function has($name)
    {
        return $this->container->hasParameter("features.$name");
    }

    public function get($name)
    {
        return $this->container->getParameter("features.$name");
    }
}