<?php

declare(strict_types=1);

namespace App\Adapter\Persistence\Doctrine\Type;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeImmutableType;
use InvalidArgumentException;
use Krixon\DateTime\DateTime as KrixonDatetime;

class DatetimeType extends DateTimeImmutableType
{
    private static $utc;

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof KrixonDatetime) {
            $value = $value->toInternalDateTime();
        }

        if ($value instanceof DateTime) {
            $value = DateTimeImmutable::createFromMutable($value);
        }

        if ($value instanceof DateTimeImmutable) {
            $value = $value->setTimezone(self::utc());
        }

        return parent::convertToDatabaseValue($value, $platform);
    }


    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value instanceof DateTimeImmutable) {
            return $value;
        }

        $format = $platform->getDateTimeFormatString();

        try {
            $converted = DateTimeImmutable::createFromFormat($format, $value, self::utc());
        } catch (InvalidArgumentException $exception) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), $format, $exception);
        }

        return $converted;
    }


    private static function utc() : DateTimeZone
    {
        if (!self::$utc) {
            self::$utc = new DateTimeZone('UTC');
        }

        return self::$utc;
    }
}
