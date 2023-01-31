<?php

namespace Kriss\Notification\Channels;

use Kriss\Notification\Channels\Traits\TemplateSupport;
use Kriss\Notification\Templates\BaseTemplate;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

/**
 * 邮件
 * https://symfony.com/doc/current/mailer.html
 */
final class MailerChannel extends BaseChannel
{
    use TemplateSupport;

    protected array $config = [
        'dsn' => '', // smtp://username:password@smtp.xxx.com:465
        'from' => '', // 发送人：xxx@xxx.com 或 [xxx@xxx.com, xxx@xxx.com]
        'to' => '',
        'cc' => '',
        'bcc' => '',
        'subject' => '', // 默认的标题
    ];

    public function __construct()
    {
        parent::__construct();

        if (!class_exists(Mailer::class)) {
            throw new \InvalidArgumentException('请先安装 `symfony/mailer`');
        }
    }

    public function sendText(string $text, string $subject = '')
    {
        $email = (new Email())
            ->subject($subject)
            ->text($text);
        return $this->send($email);
    }

    public function sendHtml(string $html, string $subject = '')
    {
        $email = (new Email())
            ->subject($subject)
            ->html($html);
        return $this->send($email);
    }

    public function send(Email $email)
    {
        if (!$email->getFrom() && $this->config['from']) {
            $email->from($this->config['from']);
        }
        if (!$email->getTo() && $this->config['to']) {
            $email->to($this->config['to']);
        }
        if (!$email->getCc() && $this->config['cc']) {
            $email->cc($this->config['cc']);
        }
        if (!$email->getBcc() && $this->config['bcc']) {
            $email->bcc($this->config['bcc']);
        }
        if (!$email->getSubject() && $this->config['subject']) {
            $email->subject($this->config['subject']);
        }

        return $this->wrapSendCallback(function () use ($email) {
            $mailer = new Mailer(Transport::fromDsn($this->config['dsn']));
            $mailer->send($email);
        });
    }

    public function sendTemplate(BaseTemplate $template)
    {
        $template->setUseMarkdown(false);
        return $this->sendText($template);
    }
}