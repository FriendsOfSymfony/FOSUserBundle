<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class HelperAwareCommand extends ContainerAwareCommand
{
    /**
     * Ask user
     *
     * @param InputInterface     $input      Input
     * @param OutputInterface    $output     Output
     * @param string             $label      Label
     * @param string             $emptyError Exception message if empty input
     *
     * @return string
     */
    protected function ask(InputInterface $input, OutputInterface $output,$label, $emptyError)
    {
        if ($this->getHelperSet()->has("question")) {
            /** @var QuestionHelper $helper */
            $helper = $this->getHelper('question');

            $usernameQuestion = new Question($label);
            $usernameQuestion->setValidator(function ($value) use ($emptyError) {
                if (empty($value)) {
                    throw new \Exception($emptyError);
                }

                return $value;
            });

            $value = $helper->ask($input, $output, $usernameQuestion);
        } else {
            /** @var DialogHelper $helper */
            $helper = $this->getHelper('dialog');
            $value = $helper->askAndValidate(
                $output,
                $label,
                function($value) use ($emptyError) {
                    if (empty($value)) {
                        throw new \Exception($emptyError);
                    }
                    return $value;
                }
            );
        }

        return $value;
    }
}
