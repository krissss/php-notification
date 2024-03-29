<?php

namespace Kriss\Notification\Channels;

use Kriss\Notification\Services\HttpClient;
use Kriss\Notification\Templates\BaseTemplate;

/**
 * 企业微信群机器人.
 *
 * @see https://developer.work.weixin.qq.com/document/path/91770
 */
class WeWorkBotChannel extends BaseChannel
{
    protected array $config = [
        'key' => '', // webhook 的 key
        'mentioned_list' => [], // @userid
        'mentioned_mobile_list' => [], // @mobile
    ];
    private HttpClient $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        parent::__construct();

        $this->httpClient = $httpClient;
    }

    public function sendText(string $content, array $config = [])
    {
        $config = array_merge([
            'mentioned_list' => (array) $this->config['mentioned_list'],
            'mentioned_mobile_list' => (array) $this->config['mentioned_mobile_list'],
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

    public function sendMarkdown(string $content, array $config = [])
    {
        $config = array_merge([
            'mentioned_list' => (array) $this->config['mentioned_list'],
        ], $config);

        if ($config['mentioned_list']) {
            $content = implode('', array_map(fn (string $userid) => "<@{$userid}>", $config['mentioned_list']))
                ."\n"
                .$content;
        }

        $params = [
            'msgtype' => 'markdown',
            'markdown' => [
                'content' => $content,
            ],
        ];

        return $this->send($params);
    }

    public function sendTemplate(BaseTemplate $template)
    {
        if ($template->useMarkdown) {
            return $this->sendMarkdown($template);
        }

        return $this->sendText($template);
    }

    private function send(array $params)
    {
        return $this->wrapSendCallback(function () use ($params) {
            return $this->httpClient->requestPostJson(
                "https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key={$this->config['key']}",
                array_filter($params)
            );
        });
    }
}
