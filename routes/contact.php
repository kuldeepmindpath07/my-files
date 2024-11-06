<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function ($app, $tracer) {
    $app->get('/contact', function (Request $request, Response $response) use ($tracer) {
        $span = $tracer->spanBuilder('/mycontact')->startSpan();
        $span->setAttribute('http.method', $request->getMethod());
        $span->setAttribute('http.url', (string)$request->getUri()->getPath());

        $html = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Contact</title></head><body>';
        $html .= '<div>This is the contact page</div><button onclick="location.href=\'/contact/child\'">Contact Us</button></body></html>';

        $response->getBody()->write($html);
        $span->end();

        return $response;
    });
};
