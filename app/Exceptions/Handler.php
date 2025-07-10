<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Mail;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $e
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $e)
    {
        // Check if the exception should be reported
        if ($this->shouldReport($e)) {
            $this->sendErrorEmail($e);
        }

        parent::report($e);
    }

    /**
     * Send an email with the exception details.
     *
     * @param  \Throwable  $e
     * @return void
     */
    protected function sendErrorEmail(Throwable $e)
    {
        try {
            Mail::send(
                'email.exception',
                [
                    'exceptionMessage' => $e->getMessage(),
                    'exceptionFile' => $e->getFile(),
                    'exceptionLine' => $e->getLine(),
                ],
                function ($message) {
                    $message->from('support@dragonautomart.com', 'Dragon Auto Mart');
                    $message->to('khanhash1994@gmail.com'); // Send to support email
                    $message->subject('Dragon Exception');
                }
            );
        } catch (Throwable $mailException) {
            // Log the exception if the email sending fails
            // Here you might want to log the $mailException to a different log
        }
    }
}
