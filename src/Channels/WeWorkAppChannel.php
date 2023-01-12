<?php

namespace Kriss\Notification\Channels;

use Kriss\Notification\Exceptions\AccessTokenGetException;
use Kriss\Notification\Services\Cache;
use Kriss\Notification\Services\HttpClient;

/**
 * 企业微信内部应用
 * @link https://developer.work.weixin.qq.com/document/path/90236
 */
final class WeWorkAppChannel extends BaseChannel
{
    protected array $config = [
        'corpid' => '',
        'corpsecret' => '',
        'agentid' => '',
        'access_token_cache_key' => '',
    ];
    private HttpClient $httpClient;
    private Cache $cache;

    public function __construct(HttpClient $httpClient, Cache $cache)
    {
        $this->httpClient = $httpClient;
        $this->cache = $cache;
    }

    public function sendText(string $content, ?string $toUser, array $params = []): bool
    {
        $params = array_replace_recursive(
            [
                'touser' => $toUser,
                'toparty' => null,
                'totag' => null,
                'safe' => null,
                'enable_id_trans' => null,
                'enable_duplicate_check' => null,
                'duplicate_check_interval' => null,
            ],
            $params,
            [
                'msgtype' => 'text',
                'agentid' => $this->config['agentid'],
                'text' => [
                    'content' => $content,
                ],
            ]
        );

        return $this->send($params);
    }

    public function sendMarkdown(string $content, ?string $toUser, array $params = []): bool
    {
        $params = array_replace_recursive(
            [
                'touser' => $toUser,
                'toparty' => null,
                'totag' => null,
                'enable_duplicate_check' => null,
                'duplicate_check_interval' => null,
            ],
            $params,
            [
                'msgtype' => 'markdown',
                'agentid' => $this->config['agentid'],
                'markdown' => [
                    'content' => $content,
                ],
            ]
        );

        return $this->send($params);
    }

    private function send(array $params): bool
    {
        $data = $this->httpClient->requestPostJson("https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token={$this->getAccessToken()}", $params);
        return $data['errcode'] === 0;
    }

    /**
     * @link https://developer.work.weixin.qq.com/document/path/91039
     * @return string
     */
    private function getAccessToken(): string
    {
        $cache = $this->cache;
        $cacheKey = ($this->config['access_token_cache_key'] ?: self::class);
        $cacheKey = md5(serialize([$cacheKey, $this->config['corpid'], $this->config['corpsecret'], 'v1']));

        $accessToken = $cache->get($cacheKey);
        if (!$accessToken) {
            $data = $this->httpClient->requestGet("https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid={$this->config['corpid']}&corpsecret={$this->config['corpsecret']}");
            if ($data['errcode'] !== 0) {
                throw new AccessTokenGetException($data['errmsg']);
            }
            $accessToken = $data['access_token'];
            $cache->set($cacheKey, $accessToken, $data['expires_in'] - 10);
        }

        return $accessToken;
    }
}