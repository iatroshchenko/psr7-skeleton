<?php


namespace Skeleton\Http\Router\Exception;

class GenerateUnknownRoute extends \LogicException
{
    private $name;
    private $params;

    public function getName()
    {
        return $this->name;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function __construct(string $name, array $params)
    {
        parent::__construct('Route not found!');
        $this->name = $name;
        $this->params = $params;
    }
}