<?php
namespace Nitronet\DevTools\Plugins\Debug;

use Fwk\Core\Events\EndEvent;
use Fwk\Core\Events\ErrorEvent;

class DebugListener
{
    public function onEnd(EndEvent $event)
    {
        if ($event->getContext()->hasParent()) {
            return;
        }

        $stock = array(
            'request' => $event->getContext()->getRequest(),
            'contextState' => $event->getContext()->getState(),
            'action' => $event->getContext()->getActionName(),
            'contextError' => $event->getContext()->getError()
        );
        /*
        $event->stop();
        echo '<pre>';
        echo var_dump($stock, true);
        echo '</pre>';
*/
    }

    public function onError(ErrorEvent $event)
    {
        if ($event->getContext()->hasParent()) {
            return;
        }

        $stock = array(
            'request' => $event->getContext()->getRequest(),
            'contextState' => $event->getContext()->getState(),
            'action' => $event->getContext()->getActionName(),
            'contextError' => $event->getContext()->getError(),
            'exception' => $event->getException()
        );
        /*
        $event->stop();
        echo '<pre>';
        echo var_dump($stock, true);
        echo '</pre>';
*/
    }
}