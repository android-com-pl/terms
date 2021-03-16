<?php

namespace FoF\Terms\Middlewares;

use Flarum\Foundation\ErrorHandling\ExceptionHandler\IlluminateValidationExceptionHandler;
use Flarum\Foundation\ErrorHandling\JsonApiFormatter;
use FoF\Terms\Validators\RegisterPolicyValidator;
use Illuminate\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RegisterMiddleware implements MiddlewareInterface
{
    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     * @param Request $request
     * @param RequestHandlerInterface $handler
     * @return Response
     */
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        // Compare if the current path is the register endpoint
        if ($request->getUri()->getPath() === '/register') {
            /**
             * @var $validator RegisterPolicyValidator
             */
            $validator = resolve(RegisterPolicyValidator::class);

            try {
                $validator->assertValid($request->getParsedBody());
            } catch (ValidationException $exception) {
                /**
                 * @var $handler IlluminateValidationExceptionHandler
                 */
                $handler = resolve(IlluminateValidationExceptionHandler::class);

                // We need to handle/format the error ourselves because the front-end (forum) doesn't have that error handler
                // Only the API-end has it (that's where the remaining of the sign up / login logic is)
                $error = $handler->handle($exception);

                return (new JsonApiFormatter())->format($error, $request);
            }
        }

        return $handler->handle($request);
    }
}
