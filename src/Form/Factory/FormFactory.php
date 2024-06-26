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
use Symfony\Component\Form\FormInterface;

class FormFactory implements FactoryInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string[]|null
     */
    private $validationGroups;

    /**
     * FormFactory constructor.
     *
     * @param string        $name
     * @param string        $type
     * @param string[]|null $validationGroups
     */
    public function __construct(FormFactoryInterface $formFactory, $name, $type, ?array $validationGroups = null)
    {
        $this->formFactory = $formFactory;
        $this->name = $name;
        $this->type = $type;
        $this->validationGroups = $validationGroups;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function createForm(array $options = []): FormInterface
    {
        $options = array_merge(['validation_groups' => $this->validationGroups], $options);

        return $this->formFactory->createNamed($this->name, $this->type, null, $options);
    }
}
