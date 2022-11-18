<?php

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

require __DIR__ . '/vendor/autoload.php';

$config = [
    'DSN' => getenv('DSN') ?: $_POST['DSN'] ?? $_GET['DSN'] ?? '',
    'SENDER' => getenv('SENDER') ?: $_POST['SENDER'] ?? $_GET['SENDER'] ?? '',
    'RECIPIENT' => getenv('RECIPIENT') ?: $_POST['RECIPIENT'] ?? $_GET['RECIPIENT'] ?? '',
    'REPLY_TO' => getenv('REPLY_TO') ?: $_POST['REPLY_TO'] ?? $_GET['REPLY_TO'] ?? '',
    'SUBJECT' => getenv('SUBJECT') ?: $_POST['SUBJECT'] ?? $_GET['SUBJECT'] ?? 'Form to email',
    'REDIRECT' => getenv('REDIRECT') ?: $_POST['REDIRECT'] ?? $_GET['REDIRECT'] ?? $_SERVER['HTTP_REFERER'] ?? '',
    'ALLOW_ORIGIN' => getenv('ALLOW_ORIGIN') ?: '*',
];

header("Access-Control-Allow-Origin: {$config['ALLOW_ORIGIN']}");

$required = ['DSN', 'SENDER', 'RECIPIENT'];
foreach ($required as $key) {
    if (!$config[$key]) {
        http_response_code(400);
        exit("Missing config for '{$key}'");
    }
}

if ($config['REDIRECT']) {
    header("Location: {$config['REDIRECT']}", true, 302);
}

$data = array_diff_key($_POST, $config);

ob_start();
include __DIR__.'/templates/plain.php';
$plain = ob_get_clean();

ob_start();
include __DIR__.'/templates/html.php';
$html = ob_get_clean();

$email = (new Email())
    ->from(Address::create($config['SENDER']))
    ->to(Address::create($config['RECIPIENT']))
    ->replyTo(Address::create($config['REPLY_TO'] ?: $config['SENDER']))
    ->subject($config['SUBJECT'])
    ->text($plain)
    ->html($html);

$transport = Transport::fromDsn($config['DSN']);
$mailer = new Mailer($transport);
$mailer->send($email);
