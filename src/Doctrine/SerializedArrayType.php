<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Restores the legacy DBAL array type used by the bundle's ORM mapping.
 *
 * @internal
 */
final class SerializedArrayType extends Type
{
    public const NAME = 'array';

    private const ALLOWED_CLASSES_OPTION = 'allowed_classes';
    private const EMPTY_VALUE = '';
    private const STREAM_READ_ERROR = 'Could not read the serialized array value.';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getClobTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return null === $value ? null : serialize($value);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        if (null === $value) {
            return null;
        }

        if (is_resource($value)) {
            $value = stream_get_contents($value);

            if (false === $value || self::EMPTY_VALUE === $value) {
                throw new \UnexpectedValueException(self::STREAM_READ_ERROR);
            }
        }

        return unserialize($value, [self::ALLOWED_CLASSES_OPTION => false]);
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
