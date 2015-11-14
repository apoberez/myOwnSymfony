<?php

namespace Calendar\EventListener;


use Simplex\Events\ResponseEvent;
use Symfony\Component\HttpFoundation\Response;

class FrameworkResponseListener
{
    public function onResponse(ResponseEvent $event)
    {
        $response = $event->getResponse();

        if ($response->isRedirection()
            || $this->isNotHtmlResponse($response)
            || 'html' !== $event->getRequest()->getRequestFormat()
        ) {
            return;
        }

        $response->setContent($response->getContent().'GA CODE');
    }

    /**
     * @param Response $response
     * @return bool
     */
    private function isNotHtmlResponse(Response $response)
    {
        return ($response->headers->has('Content-Type')
            && false === strpos($response->headers->get('Content-Type'), 'html'));
    }
}