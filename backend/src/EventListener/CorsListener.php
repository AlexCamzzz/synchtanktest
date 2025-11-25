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
        // No aplicar en sub-peticiones internas
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $method = $request->getRealMethod();

        // Si es una petición OPTIONS (el navegador preguntando permisos), respondemos OK ya mismo
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

        // Permitir cualquier origen (para desarrollo es lo más fácil)
        $response->headers->set('Access-Control-Allow-Origin', '*');

        // Permitir los métodos que usa tu API
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');

        // Permitir cabeceras comunes (Content-Type para JSON)
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
    }
}