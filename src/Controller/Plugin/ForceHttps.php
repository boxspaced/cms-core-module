<?php
namespace Boxspaced\CmsCoreModule\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Http\Response;
use Zend\Uri\Http as HttpUri;
use Boxspaced\CmsCoreModule\Exception;

class ForceHttps extends AbstractPlugin
{

    /**
     * @return Response|null
     */
    public function __invoke()
    {
        $request = $this->getController()->getRequest();

        if ('https' === $request->getUri()->getScheme()) {
            return null;
        }

        if ('GET' === $request->getMethod()) {

            $uri = new HttpUri($request->getUri());
            $uri->setScheme('https');
            $uri->setPort('443');

            return $this->getController()->redirect()->toUrl($uri);
        }

        throw new Exception\RuntimeException('Scheme must be https');
    }

}
