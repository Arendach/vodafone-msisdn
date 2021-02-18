<?php

declare(strict_types=1);

return [
    /**
     * Enable and disable logging and debug mode
     */
    'debug-mode'      => env('MSISDN_DEBUG_MODE', false),

    /**
     * Secret key for HMAC testing
     */
    'hmac-secret'     => env('MSISDN_HMAC_SECRET'),

    /**
     * HMAC testing algorithm
     */
    'hmac-algo'       => env('MSISDN_HMAC_ALGO', 'sha256'),

    /**
     * Decryption key
     */
    'decrypt-secret'  => env('MSISDN_DECRYPT_SECRET'),

    /**
     * Decrypt IV parameter
     */
    'decrypt-iv'      => env('MSISDN_DECRYPT_IV'),

    /**
     * Decrypt algorithm
     */
    'decrypt-algo'    => env('MSISDN_DECRYPT_ALGO', 'aes-256-cbc'),

    /**
     * Throw special exception if fail
     */
    'throw-exception' => env('APP_DEBUG', false),

    /**
     * Emulation working proxy enrichment
     */
    'emulation-phone' => env('MSISDN_EMULATION_PHONE', null)
];
