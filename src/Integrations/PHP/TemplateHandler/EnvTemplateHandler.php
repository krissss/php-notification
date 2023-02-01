<?php

namespace Kriss\Notification\Integrations\PHP\TemplateHandler;

use Closure;
use Kriss\Notification\Helper\BackTrace;
use Kriss\Notification\Templates\BaseTemplate;
use Kriss\Notification\Templates\ExceptionTemplate;
use Kriss\Notification\Templates\InfosTemplate;

class EnvTemplateHandler
{
    protected BaseTemplate $template;
    protected Closure $toString;
    protected array $config = [
        'back_trace_root_level' => 7,
        'back_trace_ignore_paths' => [],
        'env' => null,
        'uid' => null,
    ];

    public function __construct(BaseTemplate $template, Closure $toString, array $config = [])
    {
        $this->template = $template;
        $this->toString = $toString;
        $this->config = array_merge($this->config, $config);
    }

    public function __invoke()
    {
        $backTrace = $this->getBackTrace();
        if ($this->template instanceof ExceptionTemplate) {
            $exception = $this->template->exception;
            if ($exception) {
                $infos = [
                    '异常' => mb_substr($exception->getMessage(), 0, 500),
                    '来源' => $backTrace->getTriggerFileFromException($exception),
                ];
                $this->template->infos = array_filter(array_merge(
                    $infos,
                    $this->template->infos
                ));
                $this->template->exception = null;
            }
        }
        if ($this->template instanceof InfosTemplate) {
            $infos = [
                '环境' => $this->getEnv(),
                '触发' => $backTrace->getTriggerFile(),
                'uid' => $this->getUid(),
            ];
            $this->template->infos = array_filter(array_merge(
                $infos,
                $this->template->infos
            ));
        }

        return true;
    }

    protected function getBackTrace(): BackTrace
    {
        return new BackTrace(
            dirname(__DIR__, $this->config['back_trace_root_level']) . DIRECTORY_SEPARATOR,
            $this->config['back_trace_ignore_paths']
        );
    }

    protected function getEnv(): string
    {
        if ($this->config['env']) {
            if ($this->config['env'] instanceof Closure) {
                return $this->config['env']();
            }
            return $this->config['env'];
        }
        return $_SERVER['HTTP_HOST'];
    }

    protected function getUid(): string
    {
        if ($this->config['uid']) {
            if ($this->config['uid'] instanceof Closure) {
                return $this->config['uid']();
            }
            return $this->config['uid'];
        }
        return '';
    }
}