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
    ];

    public function sendText(string $subject, string $text): bool
    {
        $email = (new Email())
            ->subject($subject)
            ->text($text);
        return $this->send($email);
    }

    public function sendHtml(string $subject, string $html): bool
    {
        $email = (new Email())
            ->subject($subject)
            ->html($html);
        return $this->send($email);
    }

    public function send(Email $email): bool
    {
        if (!$email->getFrom()) {
            $email->from($this->config['from']);
        }
        if (!$email->getTo()) {
            $email->to($this->config['to']);
        }
        if ($email->getCc()) {
            $email->cc($this->config['cc']);
        }
        if ($email->getBcc()) {
            $email->bcc($this->config['bcc']);
        }

        $mailer = new Mailer(Transport::fromDsn($this->config['dsn']));
        $mailer->send($email);
        return true;
    }
}