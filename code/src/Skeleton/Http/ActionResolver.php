<?php


namespace Skeleton\Http;


class ActionResolver
{
    public function resolve($handler): callable
    {
        if (is_callable($handler)) {
            return $handler;
        }
        if (is_string($handler)) {
            if (class_exists($handler)) {
                return new $handler();
            }
        }
        return null;
    }
}