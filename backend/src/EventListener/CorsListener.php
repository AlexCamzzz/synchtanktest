<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CorsListener
{
    /**
     * Intercepta la petición ANTES de que llegue al controlador.
     * Si es una petición OPTIONS (pre-flight), devuelve respuesta inmediata.
     */
    #[AsEventListener(event: KernelEvents::REQUEST, priority: 9999)]
    public function onKernelRequest(RequestEvent $event): void
    {
        // Not apply on internal requests
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $method = $request->getRealMethod();

        // If OPTIONS request, return OK
        if ($method === 'OPTIONS') {
            $response = new Response();
            $event->setResponse($response);
        }
    }

    /**
     * Intercepta la respuesta ANTES de enviarla al navegador
     * y le pega las cabeceras de permisos.
     */
    #[AsEventListener(event: KernelEvents::RESPONSE)]
    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $response = $event->getResponse();

        // Allow from any origin
        $response->headers->set('Access-Control-Allow-Origin', '*');

        // Allow API Calls
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');

        // Allow common headers
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
    }
}