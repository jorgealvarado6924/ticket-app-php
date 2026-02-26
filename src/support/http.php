<?php
declare(strict_types=1);

function redirect(string $url): void {
    header("Location: {$url}");
    exit;
}