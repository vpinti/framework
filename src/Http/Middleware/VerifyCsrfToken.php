<?php

declare(strict_types=1);

namespace Pulsar\Framework\Http\Middleware;

use Pulsar\Framework\Http\Request;
use Pulsar\Framework\Http\Response;
use Pulsar\Framework\Http\TokenMismatchException;

class VerifyCsrfToken implements MiddlewareInterface
{
    public function process(Request $request, RequestHandlerInterface $requestHandler): Response
    {
        if(!in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $requestHandler->handle($request);
        }

        $tokenFromSession = $request->getSession()->get('crsf_token');
        $tokenFromRequest = $request->input('_token');

        if(!hash_equals($tokenFromSession, $tokenFromRequest)) {
            $exception = new TokenMismatchException('Your request could not be validated. Please try again.');
            $exception->setStatusCode(Response::HTTP_FORBIDDEN);
            throw $exception;
        }

        return $requestHandler->handle($request);
    }
}
