<?php

require_once __DIR__ . '/../basic_spam_check.php';

$cases = [
    'detects a JSON-encoded React2Shell payload' => [
        ['message' => '{"then":"$1:__proto__:then","status":"resolved_model","reason":-1,"value":"{\\"then\\":\\"$B1337\\"}","_response":{"_prefix":"probe"}}'],
        true,
    ],
    'finds a probe in any POST field' => [
        ['first' => 'ordinary text', 'second' => '{"status":"resolved_model"}'],
        true,
    ],
    'ignores non-JSON text containing the probe marker' => [
        ['message' => 'resolved_model'],
        false,
    ],
    'ignores JSON with a different status even if it mentions the marker' => [
        ['message' => '{"status":"ok","note":"resolved_model"}'],
        false,
    ],
    'ignores non-string POST values' => [
        ['message' => ['status' => 'resolved_model']],
        false,
    ],
    'ignores invalid JSON data' => [
        ['message' => "not JSON \xB1"],
        false,
    ],
];

foreach ($cases as $name => [$post, $expected]) {
    $actual = is_possible_spam($post);
    if ($actual !== $expected) {
        fwrite(STDERR, "FAIL: {$name}; expected " . var_export($expected, true) . ', got ' . var_export($actual, true) . "\n");
        exit(1);
    }
    echo "PASS: {$name}\n";
}
