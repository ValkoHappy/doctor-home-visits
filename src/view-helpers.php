<?php
function e(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
function go(string $url): never { header('Location: ' . $url); exit; }
function user(): ?array { return $_SESSION['user'] ?? null; }
function isAdmin(): bool { return (int) ($_SESSION['user']['is_admin'] ?? 0) === 1; }
