<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Form\Factory;

use Symfony\Component\Form\FormFactoryInterface;

class FormFactory implements FactoryInterface
{
    private $formFactory;
    private $name;
    private $type;
    private $validationGroups;

    public function __construct(FormFactoryInterface $formFactory, $name, $type, array $validationGroups = null)
    {
        $this->formFactory = $formFactory;
        $this->name = $name;
        $this->type = $type;
        $this->validationGroups = $validationGroups;
    }

    public function createForm()
    {
        return $this->formFactory->createNamed($this->name, $this->type, null, array('validation_groups' => $this->validationGroups));
    }
}
