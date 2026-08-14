<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use FOS\UserBundle\Doctrine\SerializedArrayType;
use PHPUnit\Framework\TestCase;

final class SerializedArrayTypeTest extends TestCase
{
    private const ROLE = 'ROLE_ADMIN';

    public function testConvertsSerializedArrays(): void
    {
        $platform = $this->createMock(AbstractPlatform::class);
        $type = new SerializedArrayType();
        $roles = [self::ROLE];

        $databaseValue = $type->convertToDatabaseValue($roles, $platform);

        self::assertSame($roles, $type->convertToPHPValue($databaseValue, $platform));
    }

    public function testConvertsStreamValues(): void
    {
        $platform = $this->createMock(AbstractPlatform::class);
        $type = new SerializedArrayType();
        $stream = tmpfile();

        self::assertNotFalse($stream);

        try {
            $serializedRoles = serialize([self::ROLE]);

            self::assertSame(strlen($serializedRoles), fwrite($stream, $serializedRoles));
            self::assertTrue(rewind($stream));

            self::assertSame([self::ROLE], $type->convertToPHPValue($stream, $platform));
        } finally {
            fclose($stream);
        }
    }

    public function testKeepsNullValues(): void
    {
        $platform = $this->createMock(AbstractPlatform::class);
        $type = new SerializedArrayType();

        self::assertNull($type->convertToDatabaseValue(null, $platform));
        self::assertNull($type->convertToPHPValue(null, $platform));
    }

    public function testRejectsEmptyStreamValues(): void
    {
        $platform = $this->createMock(AbstractPlatform::class);
        $type = new SerializedArrayType();
        $stream = tmpfile();

        self::assertNotFalse($stream);

        try {
            $this->expectException(\UnexpectedValueException::class);
            $type->convertToPHPValue($stream, $platform);
        } finally {
            fclose($stream);
        }
    }

    public function testDeclaresClobStorage(): void
    {
        $column = [];
        $declaration = 'CLOB';
        $platform = $this->createMock(AbstractPlatform::class);
        $platform->expects(self::once())->method('getClobTypeDeclarationSQL')->with($column)->willReturn($declaration);
        $type = new SerializedArrayType();

        self::assertSame($declaration, $type->getSQLDeclaration($column, $platform));
        self::assertSame(SerializedArrayType::NAME, $type->getName());
        self::assertTrue($type->requiresSQLCommentHint($platform));
    }
}
