<?php

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

require __DIR__ . '/vendor/autoload.php';

$config = [
    'ALLOW_ORIGIN' => getenv('ALLOW_ORIGIN'),
    'DSN' => getenv('DSN') ?: $_POST['DSN'] ?? $_GET['DSN'] ?? '',
    'SENDER' => getenv('SENDER') ?: $_POST['SENDER'] ?? $_GET['SENDER'] ?? '',
    'RECIPIENT' => getenv('RECIPIENT') ?: $_POST['RECIPIENT'] ?? $_GET['RECIPIENT'] ?? '',
    'REPLY_TO' => getenv('REPLY_TO') ?: $_POST['REPLY_TO'] ?? $_GET['REPLY_TO'] ?? '',
    'SUBJECT' => getenv('SUBJECT') ?: $_POST['SUBJECT'] ?? $_GET['SUBJECT'] ?? 'Post to email',
    'REDIRECT' => getenv('REDIRECT') ?: $_POST['REDIRECT'] ?? $_GET['REDIRECT'] ?? $_SERVER['HTTP_REFERER'] ?? '',
    'HONEYPOT' => getenv('HONEYPOT') ?: $_POST['HONEYPOT'] ?? $_GET['HONEYPOT'] ?? '',
];

$required = ['ALLOW_ORIGIN', 'DSN', 'SENDER', 'RECIPIENT'];
foreach ($required as $key) {
    if (!$config[$key]) {
        http_response_code(400);
        exit("Missing config for '{$key}'");
    }
}

header("Access-Control-Allow-Origin: {$config['ALLOW_ORIGIN']}");

if ($config['REDIRECT']) {
    if (!filter_var($config['REDIRECT'], FILTER_VALIDATE_URL)) {
        http_response_code(400);
        exit("REDIRECT '{$config['REDIRECT']}' is not a valid URL");
    }

    header("Location: {$config['REDIRECT']}", true, 302);
}

if ($config['HONEYPOT'] && isset($_POST[$config['HONEYPOT']]) && $_POST[$config['HONEYPOT']] !== '') {
    http_response_code(400);
    exit('Spam detected');
}

try {
    $transport = Transport::fromDsn($config['DSN']);
} catch (Exception $e) {
    http_response_code(400);
    exit("DSN '{$config['DSN']}' is not a valid/supported DSN");
}

try {
    $sender = Address::create($config['SENDER']);
} catch (Exception $e) {
    http_response_code(400);
    exit("SENDER '{$config['SENDER']}' is not a valid/supported address");
}

try {
    $recipient = Address::create($config['RECIPIENT']);
} catch (Exception $e) {
    http_response_code(400);
    exit("RECIPIENT '{$config['RECIPIENT']}' is not a valid/supported address");
}

try {
    $replyTo = Address::create($config['REPLY_TO'] ?: $config['SENDER']);
} catch (Exception $e) {
    http_response_code(400);
    exit("REPLY_TO '{$config['REPLY_TO']}' is not a valid/supported address");
}

// request is valid
echo 'OK';

$data = array_diff_key($_POST, $config);
if (!$data) {
    // requests without (non-config/-honeypot) body are considered tests
    // and can be used to test or healthcheck;
    // no email will be sent
    exit;
}

ob_start();
include __DIR__.'/templates/plain.php';
$plain = ob_get_clean();

ob_start();
include __DIR__.'/templates/html.php';
$html = ob_get_clean();

$email = (new Email())
    ->from($sender)
    ->to($recipient)
    ->replyTo($replyTo)
    ->subject($config['SUBJECT'])
    ->text($plain)
    ->html($html);

$mailer = new Mailer($transport);
$mailer->send($email);
