<?php

declare(strict_types=1);

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\Resources;

use Doctrine\DBAL\Types\Type;
use FOS\UserBundle\FOSUserBundle;
use PHPUnit\Framework\TestCase;

final class DoctrineMappingTest extends TestCase
{
    private const DOCTRINE_NAMESPACE = 'http://doctrine-project.org/schemas/orm/doctrine-mapping';
    private const FIELD_XPATH = '//doctrine:field';
    private const MAPPING_FILE = __DIR__.'/../../src/Resources/config/doctrine-mapping/User.orm.xml';

    public function testOrmMappingUsesAvailableDbalTypes(): void
    {
        (new FOSUserBundle())->boot();

        $contents = file_get_contents(self::MAPPING_FILE);

        self::assertNotFalse($contents);

        $mapping = new \SimpleXMLElement($contents);
        $mapping->registerXPathNamespace('doctrine', self::DOCTRINE_NAMESPACE);
        $fields = $mapping->xpath(self::FIELD_XPATH);

        self::assertNotFalse($fields);

        foreach ($fields as $field) {
            $type = (string) $field['type'];

            self::assertInstanceOf(Type::class, Type::getType($type));
        }
    }
}
