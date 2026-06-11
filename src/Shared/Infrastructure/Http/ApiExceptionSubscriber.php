<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http;

use ApiPlatform\Validator\Exception\ValidationException;
use App\Shared\Domain\Exception\ApiExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Throwable;
use Webmozart\Assert\InvalidArgumentException;

final readonly class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private LoggerInterface $errorsLogger,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 100],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $this->errorsLogger->error($exception->getMessage(), [
            'exception' => $exception,
        ]);

        [$status, $error, $message] = $this->mapException($exception);

        $event->setResponse(
            new JsonResponse(
                [
                    'error' => $error,
                    'message' => $message,
                ],
                $status,
            )
        );
    }

    private function mapException(Throwable $exception): array
    {
        return match (true) {
            $exception instanceof ApiExceptionInterface => [
                $exception->statusCode(),
                $exception->errorCode(),
                $exception->getMessage(),
            ],

            $exception instanceof BadCredentialsException => [
                Response::HTTP_UNAUTHORIZED,
                'BAD_CREDENTIALS',
                $exception->getMessage() ?: 'Bad credentials',
            ],

            $exception instanceof AccessDeniedException, $exception instanceof AccessDeniedHttpException => [
                Response::HTTP_FORBIDDEN,
                'ACCESS_DENIED',
                $exception->getMessage() ?: 'Access denied',
            ],

            $exception instanceof NotFoundHttpException => [
                Response::HTTP_NOT_FOUND,
                'NOT_FOUND',
                $exception->getMessage() ?: 'Resource not found',
            ],

            $exception instanceof ValidationException, $exception instanceof BadRequestHttpException, $exception instanceof InvalidArgumentException => [
                Response::HTTP_BAD_REQUEST,
                'BAD_REQUEST',
                $exception->getMessage() ?: 'Bad request',
            ],

            $exception instanceof HttpExceptionInterface => [
                $exception->getStatusCode(),
                'HTTP_' . $exception->getStatusCode(),
                $exception->getMessage() ?: 'HTTP error',
            ],

            default => [
                Response::HTTP_INTERNAL_SERVER_ERROR,
                'INTERNAL_SERVER_ERROR',
                $exception->getMessage() ?: 'Internal server error',
            ],
        };
    }
}
