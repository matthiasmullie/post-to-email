<?php

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

require __DIR__ . '/vendor/autoload.php';

$allowedOrigin = getenv('ALLOW_ORIGIN') ?: '*';
$dsn = getenv('SMTP_DSN') ?: $_GET['SMTP_DSN'] ?? '';
$subject = getenv('SUBJECT') ?: $_GET['SUBJECT'] ?? 'Form to email';
$sender = Address::create(getenv('SENDER') ?: $_GET['SENDER'] ?? '');
$recipient = Address::create(getenv('RECIPIENT') ?: $_GET['RECIPIENT'] ?? '');
$replyTo = Address::create(getenv('REPLY_TO') ?: $_GET['REPLY_TO'] ?? $sender);

header("Access-Control-Allow-Origin: {$allowedOrigin}");

if (!$_POST) {
    return;
}

$plain = implode("\n\n", array_map(static function ($key) {
    $text = is_array($_POST[$key]) ? implode(', ', $_POST[$key]) : $_POST[$key];
    return "{$key}: {$text}";
}, array_keys($_POST)));
$html = implode('', array_map(static function ($key) {
    $text = is_array($_POST[$key]) ? '<ul><li>' . implode('</li><li>', $_POST[$key]) . '</li>' : $_POST[$key];
    $text = str_replace("\n", '<br>', $text);
    return "<p><strong>{$key}</strong><br>{$text}</p>";
}, array_keys($_POST)));

$email = (new Email())
    ->from($sender)
    ->to($recipient)
    ->replyTo($replyTo)
    ->subject($subject)
    ->text($plain)
    ->html($html);

$transport = Transport::fromDsn($dsn);
$mailer = new Mailer($transport);
$mailer->send($email);
