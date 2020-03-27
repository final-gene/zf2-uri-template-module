<?php
/**
 * Uri template service file
 *
 * @copyright Copyright (c) 2016, final gene <info@final-gene.de>
 * @author    Frank Giesecke <frank.giesecke@final-gene.de>
 */

namespace FinalGene\UriTemplateModule\Service;

use BadMethodCallException;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Part;
use Zend\Router\Http\RouteInterface;
use Zend\Router\Http\Segment;
use Zend\Router\SimpleRouteStack;

/**
 * UriTemplateService
 *
 * @package FinalGene\UriTemplateModule\Service
 */
class UriTemplateService
{
    /**
     * @var SimpleRouteStack
     */
    protected $router;

    /**
     * @var array
     */
    protected $zfRestConfig;

    /**
     * Get $zfRestConfig
     *
     * @return array
     */
    public function getZfRestConfig()
    {
        if (!is_array($this->zfRestConfig)) {
            throw new BadMethodCallException('ZF rest config not set');
        }
        return $this->zfRestConfig;
    }

    /**
     * Set $zfRestConfig
     *
     * @param array $zfRestConfig
     *
     * @return $this
     */
    public function setZfRestConfig($zfRestConfig)
    {
        $this->zfRestConfig = $zfRestConfig;
        return $this;
    }

    /**
     * Get $router
     *
     * @return SimpleRouteStack
     */
    public function getRouter()
    {
        if (!$this->router instanceof SimpleRouteStack) {
            throw new BadMethodCallException('Router not set');
        }
        return $this->router;
    }

    /**
     * Set $router
     *
     * @param SimpleRouteStack $router
     *
     * @return $this
     */
    public function setRouter(SimpleRouteStack $router)
    {
        $this->router = $router;
        return $this;
    }

    /**
     * Get an URI Template from a ZF-Rest resource-name
     *
     * @param $resourceName
     *
     * @return string
     * @throws \BadMethodCallException
     */
    public function getFromResource($resourceName)
    {
        $zfRestConfig = $this->getZfRestConfig();

        if (!isset($zfRestConfig[$resourceName])) {
            throw new \BadMethodCallException('Resource not found');
        }

        $resourceConfig = $zfRestConfig[$resourceName];

        if (empty($resourceConfig['route_name'])) {
            throw new \BadMethodCallException('No route_name configured');
        }

        return $this->getFromRoute($resourceConfig['route_name']);
    }

    /**
     * Get an URI Template from a ZF2 route
     *
     * @param $routeName
     *
     * @return string
     */
    public function getFromRoute($routeName)
    {
        $uri = $this->getUriTemplateForRouteAndSubroutes($routeName, $this->getRouter());

        return $uri;
    }

    /**
     * Recursive function to build an URI for a route and it's sub-routes
     *
     * @param                               $routeName
     * @param \Zend\Router\SimpleRouteStack $router
     * @param string                        $uri
     *
     * @return string
     */
    protected function getUriTemplateForRouteAndSubroutes($routeName, SimpleRouteStack $router, $uri = '')
    {
        $names = explode('/', $routeName, 2);
        $childRouteName = $names[0];

        if ($router instanceof Part) {
            $this->addChildRoutes($router);
        }
        $childRoute = $router->getRoute($childRouteName);

        if ($childRoute instanceof Part) {
            $childRouteRealRoute = $this->extractRouteFromPartRoute($childRoute);
        } else {
            $childRouteRealRoute = $childRoute;
        }

        if ($childRouteRealRoute instanceof Literal) {
            $uri .= $this->getUriTemplateForLiteral($childRouteRealRoute);
        } elseif ($childRouteRealRoute instanceof Segment) {
            $uri .= $this->getUriTemplateForSegment($childRouteRealRoute);
        }

        if (isset($names[1])) {
            // we have more children
            return $this->getUriTemplateForRouteAndSubroutes($names[1], $childRoute, $uri);
        } else {
            // we've hit the last route
            $uri .= $this->getZfRestQueryParamsAsUriTemplate($childRouteRealRoute);
            return $uri;
        }
    }

    /**
     * Get the URI template for a literal route
     *
     * @param \Zend\Router\Http\Literal $route
     *
     * @return mixed
     */
    protected function getUriTemplateForLiteral(Literal $route)
    {
        $reflectionProp = new \ReflectionProperty(get_class($route), 'route');
        $reflectionProp->setAccessible(true);
        return $reflectionProp->getValue($route);
    }

    /**
     * Get a URI template for a segment route
     *
     * @param \Zend\Router\Http\Segment $route
     *
     * @return string
     */
    protected function getUriTemplateForSegment(Segment $route)
    {
        $reflectionProp = new \ReflectionProperty(get_class($route), 'parts');
        $reflectionProp->setAccessible(true);
        $parts = $reflectionProp->getValue($route);

        $uri = '';
        foreach ($parts as $part) {
            $uri = $this->convertSegmentPartsToUriTemplate($part, $uri);
        }

        return $uri;
    }

    /**
     * Convert the parts of a segment route into URI templates
     *
     * @param $part
     * @param $uri
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function convertSegmentPartsToUriTemplate(array $part, $uri)
    {
        switch ($part[0]) {
            case 'literal':
                $uri .= $part[1];
                break;
            case 'parameter':
                $uri = rtrim($uri, '/');
                $uri .= sprintf('{/%s}', $part[1]);
                break;
            case 'optional':
                foreach ($part[1] as $optionalParts) {
                    $uri = $this->convertSegmentPartsToUriTemplate($optionalParts, $uri);
                }
                break;
            default:
                throw new \InvalidArgumentException('Unsupported SegmentRoute-Part');
        }
        return $uri;
    }

    /**
     * Get the route from a part route
     *
     * @param \Zend\Router\Http\Part $route
     *
     * @return mixed
     */
    protected function extractRouteFromPartRoute(Part $route)
    {
        $reflectionProp = new \ReflectionProperty(get_class($route), 'route');
        $reflectionProp->setAccessible(true);
        return $reflectionProp->getValue($route);
    }

    /**
     * Add a Part-routes childroutes as routes
     *
     * @param \Zend\Router\Http\Part $route
     */
    protected function addChildRoutes(Part $route)
    {
        $reflectionProp = new \ReflectionProperty(get_class($route), 'childRoutes');
        $reflectionProp->setAccessible(true);
        $childRoutes = $reflectionProp->getValue($route);

        if (is_array($childRoutes)) {
            $route->addRoutes($childRoutes);
            $reflectionProp->setValue($route, null);
        }
    }

    /**
     * Get ZF-Rest whitelisted queryparams for a route (based on the routes controller)
     *
     * @param \Zend\Router\Http\RouteInterface $childRouteRealRoute
     *
     * @return string an URI template for queryparams
     */
    protected function getZfRestQueryParamsAsUriTemplate(RouteInterface $childRouteRealRoute)
    {
        $refObj = new \ReflectionObject($childRouteRealRoute);
        if (!$refObj->hasProperty('defaults')) {
            return '';
        }

        $refDefaultProperty = $refObj->getProperty('defaults');
        $refDefaultProperty->setAccessible(true);
        $defaults = $refDefaultProperty->getValue($childRouteRealRoute);

        if (!isset($defaults['controller'])) {
            return '';
        }

        $zfRestConfig = $this->getZfRestConfig();
        $controller = $defaults['controller'];

        if (!isset($zfRestConfig[$controller]['collection_query_whitelist'])) {
            return '';
        }
        $queryWhiteList = $zfRestConfig[$controller]['collection_query_whitelist'];

        return sprintf('{?%s}', implode(',', $queryWhiteList));
    }

    /**
     * Get ZF-Rest collection name for a route (based on the routes controller)
     *
     * @param \Zend\Router\Http\RouteInterface $childRouteRealRoute
     *
     * @return string
     */
    protected function getZfRestCollectionName(RouteInterface $childRouteRealRoute)
    {
        $refObj = new \ReflectionObject($childRouteRealRoute);
        if (!$refObj->hasProperty('defaults')) {
            return '';
        }

        $refDefaultProperty = $refObj->getProperty('defaults');
        $refDefaultProperty->setAccessible(true);
        $defaults = $refDefaultProperty->getValue($childRouteRealRoute);

        if (!isset($defaults['controller'])) {
            return '';
        }

        $zfRestConfig = $this->getZfRestConfig();
        $controller = $defaults['controller'];

        if (!isset($zfRestConfig[$controller]['collection_name'])) {
            return '';
        }
        return $zfRestConfig[$controller]['collection_name'];
    }

    /**
     * Get the collection name from a ZF-Rest resource-name
     *
     * @param $resourceName
     *
     * @return string
     * @throws \BadMethodCallException
     */
    public function getCollectionNameFromResource($resourceName)
    {
        $zfRestConfig = $this->getZfRestConfig();

        if (!isset($zfRestConfig[$resourceName])) {
            throw new \BadMethodCallException('Resource not found');
        }

        $resourceConfig = $zfRestConfig[$resourceName];

        if (empty($resourceConfig['collection_name'])) {
            throw new \BadMethodCallException('No collection_name configured');
        }

        return $resourceConfig['collection_name'];
    }

    /**
     * Get the collection name from a ZF2 route
     *
     * @param $routeName
     *
     * @return string
     * @throws \BadMethodCallException
     */
    public function getCollectionNameFromRoute($routeName)
    {
        $router = $this->getRouter();
        foreach (explode('/', $routeName) as $route) {
            if ($router instanceof Part) {
                $this->addChildRoutes($router);
            }
            $router = $router->getRoute($route);
        }

        $controllerName = $this->extractDefaultControllerFromRoute($router);
        if (null !== $controllerName) {
            return $this->getCollectionNameFromResource($controllerName);
        }

        return '';
    }

    /**
     * Get the default controller name from a route
     *
     * @param object $route
     *
     * @return mixed
     */
    protected function extractDefaultControllerFromRoute($route)
    {
        if (!is_object($route)) {
            throw new \BadMethodCallException('Object expected');
        }

        try {
            $reflectionProp = new \ReflectionProperty(get_class($route), 'defaults');
            $reflectionProp->setAccessible(true);
        } catch (\ReflectionException $e) {
            return null;
        }

        $defaults = $reflectionProp->getValue($route);
        return isset($defaults['controller']) ? $defaults['controller'] : null;
    }
}
