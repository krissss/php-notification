<?php

namespace Kriss\Notification\Channels;

use Kriss\Notification\Services\HttpClient;

/**
 * 企业微信群机器人
 * @link https://developer.work.weixin.qq.com/document/path/91770
 */
class WeWorkBotChannel extends BaseChannel
{
    protected array $config = [
        'key' => '',
    ];
    private HttpClient $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function sendText(string $content, array $config = []): bool
    {
        $config = array_merge([
            'mentioned_list' => [],
            'mentioned_mobile_list' => [],
        ], $config);

        $params = [
            'msgtype' => 'text',
            'text' => [
                'content' => $content,
            ],
        ];
        if ($config['mentioned_list']) {
            $params['text']['mentioned_list'] = $config['mentioned_list'];
        }
        if ($config['mentioned_mobile_list']) {
            $params['text']['mentioned_mobile_list'] = $config['mentioned_mobile_list'];
        }

        return $this->send($params);
    }

    public function sendMarkdown(string $content): bool
    {
        return $this->send([
            'msgtype' => 'markdown',
            'markdown' => [
                'content' => $content,
            ],
        ]);
    }

    private function send(array $params): bool
    {
        $data = $this->httpClient->requestPostJson("https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key={$this->config['key']}", $params);
        return $data['errcode'] === 0;
    }
}