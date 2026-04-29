<?php

/**
 * SessionGuard — interface for authentication guards.
 * Implemented by AdminGuard and CustomerGuard (polymorphism).
 */
interface SessionGuard
{
    public function isLoggedIn(): bool;
    public function getId(): ?int;
    public function login(int $id): void;
    public function logout(): void;
    public function requireAuth(): void;
}
