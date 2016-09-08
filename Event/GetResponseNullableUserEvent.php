<?php
/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Response user event that allows null user
 *
 * @author Konstantinos Christofilos <kostas.christofilos@gmail.com>
 */
class GetResponseNullableUserEvent extends GetResponseUserEvent
{
    /** @var Request */
    private $request;

    /** @var UserInterface */
    private $user;

    /**
     * GetResponseNullableUserEvent constructor.
     *
     * @param Request $request
     * @param UserInterface|null $user
     */
    public function __construct(Request $request, UserInterface $user = null)
    {
        $this->user = $user;
        $this->request = $request;
    }
}
