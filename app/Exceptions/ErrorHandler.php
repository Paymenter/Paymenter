<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ErrorHandler extends Handler
{
    // We are overriding this method to change the view namespace separator from '::' to '.' (so themes can override error views)
    /**
     * Get the view used to render HTTP exceptions.
     *
     * @return string|null
     */
    protected function getHttpExceptionView(HttpExceptionInterface $e)
    {
        $view = 'errors.' . $e->getStatusCode();

        if (view()->exists($view)) {
            return $view;
        }

        $view = substr($view, 0, -2) . 'xx';

        if (view()->exists($view)) {
            return $view;
        }

        return null;
    }
}
