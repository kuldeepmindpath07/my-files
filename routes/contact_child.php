<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function ($app, $tracer) {
    $app->get('/contact/child', function (Request $request, Response $response) use ($tracer) {
        $span = $tracer->spanBuilder('/mycontact/child')->startSpan();
        $span->setAttribute('http.method', $request->getMethod());
        $span->setAttribute('http.url', (string)$request->getUri()->getPath());

        $html = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Contact Child</title></head><body>';
        $html .= '<div>Hi, We will stay connected always</div></body></html>';

        $response->getBody()->write($html);
        $span->end();

        return $response;
    });
};
