<?php

namespace FOS\UserBundle\Tests;

use FOS\UserBundle\Model\User;

class TestUser extends User
{
    public function setId($id)
    {
        $this->id = $id;
    }
        

}
