<?php

namespace Kriss\Notification\Channels;

use Kriss\Notification\Exceptions\AccessTokenGetException;
use Kriss\Notification\Helper\JsonHelper;
use Kriss\Notification\Services\Cache;
use Kriss\Notification\Services\HttpClient;
use Kriss\Notification\Templates\BaseTemplate;

/**
 * 企业微信内部应用.
 *
 * @see https://developer.work.weixin.qq.com/document/path/90236
 */
final class WeWorkAppChannel extends BaseChannel
{
    protected array $config = [
        'corpid' => '', // 企业 id
        'corpsecret' => '', // 应用 secret
        'agentid' => 0, // 企业应用的id，整型
        'touser' => null, // 指定接收消息的成员，成员ID列表（多个接收者用‘|’分隔，最多支持1000个）
        'toparty' => null, // 指定接收消息的部门，部门ID列表，多个接收者用‘|’分隔，最多支持100个
        'totag' => null, //	指定接收消息的标签，标签ID列表，多个接收者用‘|’分隔，最多支持100个
    ];
    private HttpClient $httpClient;
    private Cache $cache;

    public function __construct(HttpClient $httpClient, Cache $cache)
    {
        parent::__construct();

        $this->httpClient = $httpClient;
        $this->cache = $cache;
    }

    public function sendText(string $content, array $config = [])
    {
        $params = array_replace_recursive(
            [
                'touser' => $this->config['touser'],
                'toparty' => $this->config['toparty'],
                'totag' => $this->config['totag'],
                'safe' => null,
                'enable_id_trans' => null,
                'enable_duplicate_check' => null,
                'duplicate_check_interval' => null,
            ],
            $config,
            [
                'msgtype' => 'text',
                'agentid' => (int) $this->config['agentid'],
                'text' => [
                    'content' => $content,
                ],
            ]
        );

        return $this->send($params);
    }

    public function sendMarkdown(string $content, array $config = [])
    {
        $params = array_replace_recursive(
            [
                'touser' => $this->config['touser'],
                'toparty' => $this->config['toparty'],
                'totag' => $this->config['totag'],
                'enable_duplicate_check' => null,
                'duplicate_check_interval' => null,
            ],
            $config,
            [
                'msgtype' => 'markdown',
                'agentid' => (int) $this->config['agentid'],
                'markdown' => [
                    'content' => $content,
                ],
            ]
        );

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
                "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token={$this->getAccessToken()}",
                array_filter($params)
            );
        });
    }

    /**
     * @see https://developer.work.weixin.qq.com/document/path/91039
     */
    private function getAccessToken(): string
    {
        $cache = $this->cache;
        $cacheKey = md5(serialize([self::class, $this->config['corpid'], $this->config['corpsecret'], 'v1']));

        $accessToken = $cache->get($cacheKey);
        if (!$accessToken) {
            $response = $this->httpClient->requestGet("https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid={$this->config['corpid']}&corpsecret={$this->config['corpsecret']}");
            $data = JsonHelper::decode($response->getBody());
            if (0 !== $data['errcode']) {
                throw new AccessTokenGetException($data['errmsg']);
            }
            $accessToken = $data['access_token'];
            $cache->set($cacheKey, $accessToken, $data['expires_in'] - 10);
        }

        return $accessToken;
    }
}
