<?php

namespace Kriss\Notification\Helper;

class BackTrace
{
    protected string $rootPath;
    protected array $ignorePaths = [
        '/kriss/php-notification/',
        '/vendor/',
    ];

    public function __construct(string $rootPath, array $ignorePaths = [])
    {
        $this->rootPath = $rootPath;
        $this->ignorePaths = array_merge($this->ignorePaths, $ignorePaths);
    }

    public function getTriggerFile(): string
    {
        $trace = $this->getFirstProjectTrace(debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS));

        return $this->formatOriginFileLine($trace['file'], $trace['line']);
    }

    public function getTriggerFileFromException(\Throwable $e): string
    {
        $trace = $this->getFirstProjectTrace(array_merge(
            [
                ['file' => $e->getFile(), 'line' => $e->getLine()],
            ],
            $e->getTrace()
        ));

        return $this->formatOriginFileLine($trace['file'], $trace['line']);
    }

    protected function getFirstProjectTrace(array $trace = null): array
    {
        $value = [];
        foreach ($trace as $item) {
            if (!isset($item['file'])) {
                continue;
            }
            $file = str_replace(\DIRECTORY_SEPARATOR, '/', $item['file']);
            if (!$this->contains($file, $this->ignorePaths)) {
                $value = $item;
                break;
            }
        }

        return ['file' => $value['file'] ?? '[internal]', 'line' => $value['line'] ?? 0];
    }

    protected function formatOriginFileLine(string $filepath, int $line): string
    {
        $filepath = str_replace($this->rootPath, '', $filepath);

        return str_replace(\DIRECTORY_SEPARATOR, '/', $filepath).':'.$line;
    }

    private function contains($haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ('' !== $needle && false !== mb_strpos($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }
}
