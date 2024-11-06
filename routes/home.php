<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function ($app, $tracer) {
    $app->get('/', function (Request $request, Response $response) use ($tracer) {
        // Start a span for the home page request
        $span = $tracer->spanBuilder('/myHome')->startSpan();
        $span->setAttribute('http.method', $request->getMethod());
        $span->setAttribute('http.url', (string)$request->getUri()->getPath());

        // Start a nested span for the external API call
        $apiSpan = $tracer->spanBuilder('/fetchApiData')->startSpan();
        $apiSpan->setAttribute('http.url', 'https://fakestoreapi.com/products');
        $apiSpan->setAttribute('http.method', 'GET');

        // Fetch data from external API
        $apiUrl = 'https://fakestoreapi.com/products';
        $data = json_decode(file_get_contents($apiUrl), true);

        // End the API span after the API call is completed
        $apiSpan->end();

        // HTML for Home page with a navigation button to the Contact page
        $html = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Home</title></head><body>';
        $html .= '<h1>Data from API</h1>';
        $html .= is_array($data) && count($data) > 0 ? '<ul>' . implode('', array_map(fn($item) => "<li>{$item['title']} - {$item['price']}</li>", $data)) . '</ul>' : '<p>No data available.</p>';
        $html .= '<button onclick="location.href=\'/contact\'">Go to Contact Page</button></body></html>';

        // Write the HTML response
        $response->getBody()->write($html);

        // End the home page span after all processing is done
        $span->end();

        return $response;
    });
};
