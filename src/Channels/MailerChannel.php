<?php

namespace Kriss\Notification\Channels;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

/**
 * 邮件
 * https://symfony.com/doc/current/mailer.html
 */
class MailerChannel extends BaseChannel
{
    protected array $config = [
        'dsn' => '', // smtp://username:password@smtp.xxx.com:465
        'from' => '', // 发送人：xxx@xxx.com 或 [xxx@xxx.com, xxx@xxx.com]
        'to' => '',
        'cc' => '',
        'bcc' => '',
        'subject' => '', // 默认的标题
    ];

    public function sendText(string $text, string $subject = ''): bool
    {
        $email = (new Email())
            ->subject($subject)
            ->text($text);
        return $this->send($email);
    }

    public function sendHtml(string $html, string $subject = ''): bool
    {
        $email = (new Email())
            ->subject($subject)
            ->html($html);
        return $this->send($email);
    }

    public function send(Email $email): bool
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

        $mailer = new Mailer(Transport::fromDsn($this->config['dsn']));
        $mailer->send($email);
        return true;
    }
}