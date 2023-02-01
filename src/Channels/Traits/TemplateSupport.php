<?php

namespace Kriss\Notification\Channels\Traits;

use Kriss\Notification\Templates\BaseTemplate;
use Kriss\Notification\Templates\ExceptionTemplate;
use Kriss\Notification\Templates\InfosTemplate;
use Throwable;

trait TemplateSupport
{
    abstract public function sendTemplate(BaseTemplate $template);

    public function sendInfos(array $infos, string $title = '')
    {
        /** @var InfosTemplate $template */
        $template = $this->make(InfosTemplate::class, [
            'attributes' => [
                'title' => $title,
                'infos' => $infos,
            ],
        ]);
        return $this->sendTemplate($template->withFactory($this->factory));
    }

    public function sendException(Throwable $e, string $title = '', array $infos = [])
    {
        /** @var ExceptionTemplate $template */
        $template = $this->make(ExceptionTemplate::class, [
            'attributes' => [
                'exception' => $e,
                'title' => $title,
                'infos' => $infos,
            ],
        ]);
        return $this->sendTemplate($template->withFactory($this->factory));
    }
}