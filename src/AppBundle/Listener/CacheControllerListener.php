<?php
namespace AppBundle\Listener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use AppBundle\Controller\Cacheable;

class CacheControllerListener
{
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        if (!is_array($controller)) {
            return;
        }

        $controllerObject = $controller[0];
        if ($controllerObject instanceof Cacheable) {
            if ($controllerObject->isNotModified($controllerObject->getLastModifiedDate(), $event->getRequest())) {
                $controller[1] = 'getLastModifiedResponse';
            }
            $event->setController($controller);
        }
    }
}
?>
