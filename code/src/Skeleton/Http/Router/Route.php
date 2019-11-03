<?php


namespace Skeleton\Http\Router;


use Psr\Http\Message\ServerRequestInterface;
use Skeleton\Http\Router\Exception\RouteParameterNotPassedException;
use Skeleton\Http\Router\Route\RouteInterface;

class Route implements RouteInterface
{
    public $name;
    public $path;
    public $pattern;
    public $handler;
    public $tokens;
    public $methods;

    public function match(ServerRequestInterface $request): ?Result
    {
        if ($this->methods && !in_array($request->getMethod(), $this->methods)) return null;

        preg_match_all('~{([^}]+)}~', $this->pattern, $m);
        $matchedTemplates = $m[0] ?? [];
        $matchedParams = $m[1];

        $pattern = $this->pattern;
        foreach ($matchedTemplates as $key => $template) {
            $regexp = '~' . $template . '~';
            $pattern = preg_replace_callback($regexp, function ($matches) use ($matchedParams, $key) {
                $replace = $this->tokens[$matchedParams[$key]] ?? '[^}]+';
                return '(?<' . $matchedParams[$key] . '>' . $replace . ')';
            }, $pattern);
        }

        $path = $request->getUri()->getPath();

        if (preg_match('#' . $pattern . '#', $path, $matches)) {
            return new Result($this->name, $this->handler, array_filter(
                    $matches,
                    '\is_string',
                    ARRAY_FILTER_USE_KEY)
            );
        }

        return null;
    }

    public function generate(string $name, array $arguments = []): ?string
    {
        if ($name !== $this->name) return null;

        $search = '~{([^}]+)}~';
        preg_match_all($search, $this->pattern, $matches);

        $matchedTemplates = $matches[0] ?? [];
        $matchedParams = $matches[1];

        foreach ($matchedParams as $param) {
            if (!isset($arguments[$param])) throw new RouteParameterNotPassedException($this, $param);
        }

        $url = $this->path;

        foreach ($matchedTemplates as $key => $template) {
            $param = $matchedParams[$key];
            if (!isset($arguments[$param])) throw new \InvalidArgumentException(
                'Parameter ' . '\'' . $param . '\'' . 'is required for route ' . $this->name
            );
            $paramValue = $arguments[$param];

            if (isset($this->tokens[$param])) {
                $tokenPattern = $this->tokens[$param];
                if (!preg_match('~'. $tokenPattern .'~', $paramValue)) throw new \InvalidArgumentException(
                    'Invalid route generate parameter ' . '\'' . $paramValue . '\''
                    . 'passed, but ' . $tokenPattern . ' expected'
                );
            }

            $search = '~' . $template . '~';
            $url = preg_replace_callback($search, function ($matches) use ($key, $matchedParams, $arguments) {
                return $arguments[$matchedParams[$key]];
            }, $url);
        }

        return $url;
    }

    public function __construct($name, $path, $handler, array $methods, array $tokens = [])
    {
        $this->name = $name;
        $this->path = $path;
        $this->pattern = '^' . $path . '$';
        $this->handler = $handler;
        $this->tokens = $tokens;
        $this->methods = $methods;
    }
}