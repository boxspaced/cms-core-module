<?php
namespace Boxspaced\CmsCoreModule\Model;

use Boxspaced\EntityManager\Entity\AbstractEntity;
use Boxspaced\EntityManager\Collection\Collection;
use Boxspaced\CmsSlugModule\Model\Route;

class Module extends AbstractEntity
{

    // @todo Name constants e.g. item, container, block, digital-gallery etc.

    /**
     * @return int
     */
    public function getId()
    {
        return $this->get('id');
    }

    /**
     * @param int $id
     * @return Module
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->get('name');
    }

    /**
     * @param string $name
     * @return Module
     */
    public function setName($name)
    {
        $this->set('name', $name);
		return $this;
    }

    /**
     * @return bool
     */
    public function getEnabled()
    {
        return $this->get('enabled');
    }

    /**
     * @param bool $enabled
     * @return Module
     */
    public function setEnabled($enabled)
    {
        $this->set('enabled', $enabled);
		return $this;
    }

    /**
     * @return string
     */
    public function getRouteController()
    {
        return $this->get('route_controller');
    }

    /**
     * @param string $routeController
     * @return Module
     */
    public function setRouteController($routeController)
    {
        $this->set('route_controller', $routeController);
		return $this;
    }

    /**
     * @return string
     */
    public function getRouteAction()
    {
        return $this->get('route_action');
    }

    /**
     * @param string $routeAction
     * @return Module
     */
    public function setRouteAction($routeAction)
    {
        $this->set('route_action', $routeAction);
		return $this;
    }

    /**
     * @return Collection
     */
    public function getRoutes()
    {
        return $this->get('routes');
    }

    /**
     * @param Route $route
     * @return Module
     */
    public function addRoute(Route $route)
    {
        $route->setModule($this);
        $this->getRoutes()->add($route);
		return $this;
    }

    /**
     * @param Route $route
     * @return Module
     */
    public function deleteRoute(Route $route)
    {
        $this->getRoutes()->delete($route);
		return $this;
    }

    /**
     * @return Module
     */
    public function deleteAllRoutes()
    {
        foreach ($this->getRoutes() as $route) {
            $this->deleteRoute($route);
        }
		return $this;
    }

    /**
     * @return Collection
     */
    public function getPages()
    {
        return $this->get('pages');
    }

    /**
     * @param ModulePage $page
     * @return Module
     */
    public function addPage(ModulePage $page)
    {
        $page->setParentModule($this);
        $this->getPages()->add($page);
		return $this;
    }

    /**
     * @param ModulePage $page
     * @return Module
     */
    public function deletePage(ModulePage $page)
    {
        $this->getPages()->delete($page);
		return $this;
    }

    /**
     * @return Module
     */
    public function deleteAllPages()
    {
        foreach ($this->getPages() as $page) {
            $this->deletePage($page);
        }
		return $this;
    }

}
