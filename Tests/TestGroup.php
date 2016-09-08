<?php

namespace FOS\UserBundle\Tests;

use FOS\UserBundle\Model\Group;

class TestGroup extends Group
{
    public function setId($id)
    {
        $this->id = $id;
    }
}
