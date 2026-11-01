<?php

/**
 * This is a deliberately modest spam check, not a general-purpose security filter.
 *
 * At the moment, it only drops obvious React2Shell probes (see
 * [CVE-2025-55182](https://www.cve.org/CVERecord?id=CVE-2025-55182)).
 *
 * @param array $post The POST data to check.
 *
 * @return bool True if the POST data is considered spam, false otherwise.
 */
function is_possible_spam(array $post): bool {
    foreach ($post as $value) {
        if (!is_string($value)) {
            continue;
        }

        // we go looking for JSON-encoded strings, such as:
        //
        // {
        //     "then": "$1:__proto__:then",
        //     "status": "resolved_model",
        //     "reason": -1,
        //     "value": "{\"then\":\"$B1337\"}",
        //     etc, etc.
        // }
        //
        // using the "status: resolved_model" field as an indicator
        $decodedValue = json_decode($value);
        if (is_object($decodedValue) && ($decodedValue->status ?? null) === 'resolved_model') {
            return true;
        }
    }

    return false;
}
