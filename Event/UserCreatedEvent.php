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

use Symfony\Component\EventDispatcher\Event;
use FOS\UserBundle\Model\UserInterface;

/**
 * Event class used to notify listeners when user is created
 * @author Ryabenko Sergey <ryabenko.sergey@gmail.com>
 */
class UserCreatedEvent extends Event 
{
  
  /**
   * @var UserInterface
   */
  protected $user;
  
  public function __construct(UserInterface $user) 
  {
    $this->user = $user;
  }
  
  /**
   * @return UserInterface user object
   */
  public function getUser() 
  {
    return $this->user;
  }
}