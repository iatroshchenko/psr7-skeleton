<?php


namespace Skeleton\Http\Router;


class Result
{
    private $name;
    private $handler;
    private $attributes;

    public function getName()
    {
        return $this->name;
    }

    public function getHandler()
    {
        return $this->handler;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function __construct($name, $handler, array $attributes)
    {
        $this->name = $name;
        $this->handler = $handler;
        $this->attributes = $attributes;
    }
}