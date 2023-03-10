<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Symfony\Bridge\Twig;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Exposes some Symfony parameters and services as an "app" global variable.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class AppVariable
{
    private $tokenStorage;
    private $requestStack;
    private $environment;
    private $debug;

    public function setTokenStorage(TokenStorageInterface $tokenStorage): void
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function setRequestStack(RequestStack $requestStack): void
    {
        $this->requestStack = $requestStack;
    }

    public function setEnvironment($environment): void
    {
        $this->environment = $environment;
    }

    public function setDebug($debug): void
    {
        $this->debug = (bool) $debug;
    }

    /**
     * Returns the current token.
     *
     * @return TokenInterface|null
     *
     * @throws \RuntimeException When the TokenStorage is not available
     */
    public function getToken(): ?TokenInterface
    {
        if (null === $tokenStorage = $this->tokenStorage) {
            throw new \RuntimeException('The "app.token" variable is not available.');
        }

        return $tokenStorage->getToken();
    }

    /**
     * Returns the current user.
     *
     * @return mixed
     *
     * @see TokenInterface::getUser()
     */
    public function getUser()
    {
        if (null === $tokenStorage = $this->tokenStorage) {
            throw new \RuntimeException('The "app.user" variable is not available.');
        }

        if (!$token = $tokenStorage->getToken()) {
            return;
        }

        $user = $token->getUser();
        if (is_object($user)) {
            return $user;
        }
    }

    /**
     * Returns the current request.
     *
     * @return Request|null The HTTP request object
     */
    public function getRequest(): ?Request
    {
        if (null === $this->requestStack) {
            throw new \RuntimeException('The "app.request" variable is not available.');
        }

        return $this->requestStack->getCurrentRequest();
    }

    /**
     * Returns the current session.
     *
     * @return Session|null The session
     */
    public function getSession(): ?Session
    {
        if (null === $this->requestStack) {
            throw new \RuntimeException('The "app.session" variable is not available.');
        }

        if ($request = $this->getRequest()) {
            return $request->getSession();
        }
    }

    /**
     * Returns the current app environment.
     *
     * @return string The current environment string (e.g 'dev')
     */
    public function getEnvironment(): string
    {
        if (null === $this->environment) {
            throw new \RuntimeException('The "app.environment" variable is not available.');
        }

        return $this->environment;
    }

    /**
     * Returns the current app debug mode.
     *
     * @return bool The current debug mode
     */
    public function getDebug(): bool
    {
        if (null === $this->debug) {
            throw new \RuntimeException('The "app.debug" variable is not available.');
        }

        return $this->debug;
    }

    /**
     * Returns some or all the existing flash messages:
     *  * getFlashes() returns all the flash messages
     *  * getFlashes('notice') returns a simple array with flash messages of that type
     *  * getFlashes(array('notice', 'error')) returns a nested array of type => messages.
     *
     * @return array
     */
    public function getFlashes($types = null): array
    {
        // needed to avoid starting the session automatically when looking for flash messages
        try {
            $session = $this->getSession();
            if (null === $session || !$session->isStarted()) {
                return array();
            }
        } catch (\RuntimeException $e) {
            return array();
        }

        if (null === $types || '' === $types || array() === $types) {
            return $session->getFlashBag()->all();
        }

        if (is_string($types)) {
            return $session->getFlashBag()->get($types);
        }

        $result = array();
        foreach ($types as $type) {
            $result[$type] = $session->getFlashBag()->get($type);
        }

        return $result;
    }
}