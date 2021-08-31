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

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\Event as LegacyEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

if (class_exists(LegacyEvent::class)) {
    class UserEvent extends LegacyEvent
    {
        /**
         * @var Request|null
         */
        protected $request;

        /**
         * @var UserInterface
         */
        protected $user;

        /**
         * UserEvent constructor.
         */
        public function __construct(UserInterface $user, Request $request = null)
        {
            $this->user = $user;
            $this->request = $request;
        }

        /**
         * @return UserInterface
         */
        public function getUser()
        {
            return $this->user;
        }

        /**
         * @return Request
         */
        public function getRequest()
        {
            return $this->request;
        }
    }
} else {
    class UserEvent extends Event
    {
        /**
         * @var Request|null
         */
        protected $request;

        /**
         * @var UserInterface
         */
        protected $user;

        /**
         * UserEvent constructor.
         */
        public function __construct(UserInterface $user, Request $request = null)
        {
            $this->user = $user;
            $this->request = $request;
        }

        /**
         * @return UserInterface
         */
        public function getUser()
        {
            return $this->user;
        }

        /**
         * @return Request
         */
        public function getRequest()
        {
            return $this->request;
        }
    }
}
