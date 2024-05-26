<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e): Response|JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        if ($e instanceof HttpExceptionInterface) {
            return match ($e->getStatusCode()) {
                400 => response()->json([
                    'status' => "error",
                    'message' => 'Bad Request'
                ], 400),
                401 => response()->json([
                    'status' => "error",
                    'message' => 'Unauthorized'
                ], 401),
                404 => response()->json([
                    'status' => "error",
                    'message' => 'Not Found'
                ], 404),
                405 => response()->json([
                    'status' => "error",
                    'message' => 'Method Not Allowed'
                ], 405),
                429 => response()->json([
                    'status' => "error",
                    'message' => 'Too Many Requests'
                ], 429),
                503 => response()->json([
                    'status' => "error",
                    'message' => 'Under Maintenance'
                ], 503),
                default => parent::render($request, $e),
            };
        }

        if (!$request->has('grant_type')) {
            $auth = (bool)$request->user()?->id;
            $userId = $request->user()?->id ?? "-";
            $errData = "### Request Error" .
                "\n```Method: " . json_encode($request->method()) .
                "\nURL: " . $request->fullUrl() . "```" .
                "\nParameters: \n```" . json_encode($request->all()) . "```" .
                "\n### Auth" .
                "\n```User: $userId" .
                "\nIs Auth: " . json_encode($auth) . "```" .
                "\n### Error Description" .
                "\n```File: " . $e->getFile() .
                "\nLine: " . $e->getLine() . "```" .
                "\n### Message: \n```" . $e->getMessage() . "```";

            // report error to discord
            $webhook = 'https://discord.com/api/webhooks/1205217152798298202/9bTU2CQ-4YTo11PG2gLkuV3_aWnSyjDf5I0ILRH7T_wRb2dz3oi93W5Sapp_SN2XRHmE';
            $env = config('app.env');
            $error = [
                "content" => "## Application Error Log ($env)",
                "embeds" => [
                    [
                        "description" => $errData,
                        "color" => 0x87CEEB
                    ]
                ]
            ];

            Http::post($webhook, $error)->json();


            return match (config('app.debug')) {
                true => response()->json([
                    'status' => "error",
                    'message' => [
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'message' => $e->getMessage()
                    ]
                ], 500),
                default => response()->json([
                    'status' => "error",
                    'message' => 'Internal Server Error'
                ], 500),
            };
        }

        return parent::render($request, $e);
    }
}
