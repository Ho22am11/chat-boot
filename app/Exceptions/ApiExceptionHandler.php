<?php

namespace App\Exceptions;

use App\Http\Response\ApiResponse;
use Exception;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Throwable;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class ApiExceptionHandler
{
      public static function register($exceptions): void
    {

        $exceptions->render(function (ValidationException $e, $request) {

            $field   = array_key_first($e->errors());
            $message = $e->errors()[$field][0];

            return ApiResponse::error(
                $message,
                $field,
                422
            );
        });



        $exceptions->render(function (ModelNotFoundException $e, $request) {
            return ApiResponse::error(
                __('errors.not_found'),
                null,
                404
            );
        });



        $exceptions->render(function (NotFoundHttpException $e, $request) {
            return ApiResponse::error(
                __('errors.not_found'),
                null,
                404
            );
        });



        $exceptions->render(function (AuthenticationException $e, $request) {
            return ApiResponse::error(
                __('auth.unauthorized'),
                null,
                401
            );
        });



        $exceptions->render(function (AuthorizationException $e, $request) {
            return ApiResponse::error(
                __('auth.forbidden'),
                null,
                403
            );
        });


        $exceptions->render(function (HttpExceptionInterface $e, $request) {
            return ApiResponse::error(
                $e->getMessage() ?: __('errors.http_error'),
                null,
                $e->getStatusCode()
            );
        });

        $exceptions->render(function (Throwable $e, $request) {

            return ApiResponse::error(
                app()->isProduction()
                    ? __('errors.server_error')
                    : $e->getMessage(),
                null,
                500
            );
        });
    }
}
