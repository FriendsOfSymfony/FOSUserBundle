<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Mailer;

/**
 * Interface TemplateInterface.
 */
interface TemplateInterface
{
    /**
     * Renders a template.
     *
     * @param string $name The template name
     */
    public function render($name, array $context = []): string;
}
