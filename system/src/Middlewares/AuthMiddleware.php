<?php

/**
 * This file is part of JohnCMS Content Management System.
 *
 * @copyright JohnCMS Community
 * @license   https://opensource.org/licenses/GPL-3.0 GPL-3.0
 * @link      https://johncms.com JohnCMS Project
 */

declare(strict_types=1);

namespace Johncms\Middlewares;

use Johncms\Users\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @deprecated Use AuthorizedUserMiddleware or HasRoleMiddleware
 */
class AuthMiddleware implements MiddlewareInterface
{
    protected array $roles;

    public function __construct(array $roles = [])
    {
        $this->roles = $roles;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = di(User::class);
        if ((empty($this->roles) && $user !== null) || (! empty($this->roles) && $user?->hasRole($this->roles))) {
            return $handler->handle($request);
        }
        return status_page(403);
    }
}
