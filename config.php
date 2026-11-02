<?php
$envFile = __DIR__ . '/.env';

if (!file_exists($envFile)) {
    die("Environment file not found at: " . $envFile);
}

$env = parse_ini_file($envFile);

define('OPENAI_API_KEY', $env['OPENAI_API_KEY'] ?? '');
define('OPENAI_MODEL', $env['OPENAI_MODEL'] ?? 'gpt-4o-mini');

if (empty(OPENAI_API_KEY)) {
    die("OpenAI API key is missing");
}