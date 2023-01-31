<?php

namespace Kriss\Notification\Templates;

use Throwable;

class ExceptionTemplate extends InfosTemplate
{
    protected ?Throwable $exception = null;

    public function __toString()
    {
        if (!$this->exception) {
            return '';
        }
        if (!$this->title) {
            $this->title = $this->exception->getMessage();
        } else {
            $this->infos['message'] = $this->exception->getMessage();
        }
        $this->infos['file'] = str_replace(DIRECTORY_SEPARATOR, '/', $this->exception->getFile());
        $this->infos['line'] = $this->exception->getLine();
        $this->infos['trace'] = $this->exception->getTraceAsString();

        return parent::__toString();
    }
}