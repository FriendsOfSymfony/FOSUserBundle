<?php

namespace FOS\UserBundle\Tests\Form\Type;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Test\TypeTestCase as BaseTypeTestCase;

/**
 * Class TypeTestCase
 *
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 *
 * Could be removed for using directly base class since PR: https://github.com/symfony/symfony/pull/14506
 */
abstract class TypeTestCase extends BaseTypeTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->factory = Forms::createFormFactoryBuilder()
            ->addTypes($this->getTypes())
            ->addExtensions($this->getExtensions())
            ->addTypeExtensions($this->getTypeExtensions())
            ->getFormFactory();

        $this->builder = new FormBuilder(null, null, $this->dispatcher, $this->factory);
    }

    protected function getTypeExtensions()
    {
        return array();
    }

    protected function getTypes()
    {
        return array();
    }
}
