<?php

namespace Kriss\Notification\Templates;

use Throwable;

class ExceptionTemplate extends InfosTemplate
{
    public ?Throwable $exception = null;

    protected function toString(): string
    {
        if ($this->exception) {
            if (!$this->title) {
                $this->title = $this->exception->getMessage();
            } else {
                $this->infos['message'] = $this->exception->getMessage();
            }
            $this->infos['file'] = str_replace(DIRECTORY_SEPARATOR, '/', $this->exception->getFile());
            $this->infos['line'] = $this->exception->getLine();
            $this->infos['trace'] = $this->exception->getTraceAsString();
        }

        return parent::toString();
    }
}