<?php

namespace Kriss\Notification\Channels;

use Kriss\Notification\Channels\Traits\ContentCutTrait;
use Kriss\Notification\Services\HttpClient;
use Kriss\Notification\Templates\BaseTemplate;

/**
 * 企业微信群机器人.
 *
 * @see https://developer.work.weixin.qq.com/document/path/91770
 */
class WeWorkBotChannel extends BaseChannel
{
    use ContentCutTrait;

    protected array $config = [
        'key' => '', // webhook 的 key
        'mentioned_list' => [], // @userid
        'mentioned_mobile_list' => [], // @mobile
        'disable_auto_cut_content' => false,// 关闭裁剪
        'text_content_limit' => 2048,// 文本消息内容限制长度,企业微信现在要求是2048
        'markdown_content_limit' => 4096,// markdown消息限制长度,企业微信目前要求是4096
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
                'content' => $this->cutContent($content, $this->config['text_content_limit']),
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
                'content' => $this->cutContent($content, $this->config['markdown_content_limit']),
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

    protected function disableContentCut(): bool
    {
        return $this->config['disable_auto_cut_content'];
    }
}
