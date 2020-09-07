<?php

namespace App\Exceptions;

use App\S\CorpMsgService;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
        WechatException::class,
        CurlException::class,
        CommonException::class
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof WechatException) {
            $content = array('status' => 0, 'info' => $exception->getMessage() ?: '操作失败', 'data' => []);
            return response()->json($content)->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        }

        if ($exception instanceof CurlException || $exception instanceof CommonException) {
            $content = [
                'status' => 0,
                'info' => $exception->getMessage(),
                'data' => $exception->data,
                'url' => $exception->url
            ];
            if (app('request')->expectsJson()) {
                return response()->json($content)->setEncodingOptions(JSON_UNESCAPED_UNICODE);
            } else {
                return response()->view('errors.hint', $content)->send();
            }
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Auth\AuthenticationException $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }
}
