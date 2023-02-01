<?php

namespace Kriss\Notification\Templates;

class InfosTemplate extends BaseTemplate
{
    public string $title = '';
    public array $infos = [];

    protected function toString(): string
    {
        $this->infos = array_filter($this->infos);
        if ($this->useMarkdown) {
            $texts = [];
            if ($this->title) {
                $texts[] = '### ' . $this->title;
            }
            foreach ($this->infos as $key => $value) {
                $texts[] = "- **{$key}**: {$value}";
            }

            return implode("\n", $texts);
        }

        return implode("\n", array_filter(array_merge(
            [
                'title' => $this->title,
            ],
            $this->infos,
        )));
    }
}
