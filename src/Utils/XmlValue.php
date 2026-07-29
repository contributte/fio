<?php declare(strict_types = 1);

namespace Contributte\Fio\Utils;

use DateTimeImmutable;
use SimpleXMLElement;

/**
 * XmlValue
 */
class XmlValue
{

	public static function toStringOrNull(SimpleXMLElement $parent, string $child): ?string
	{
		if (!isset($parent->{$child})) {
			return null;
		}

		return (string) $parent->{$child};
	}

	public static function toFloatOrNull(SimpleXMLElement $parent, string $child): ?float
	{
		$value = self::toStringOrNull($parent, $child);

		if ($value === null || !is_numeric($value)) {
			return null;
		}

		return (float) $value;
	}

	public static function toIntOrNull(SimpleXMLElement $parent, string $child): ?int
	{
		$value = self::toStringOrNull($parent, $child);

		if ($value === null || !is_numeric($value)) {
			return null;
		}

		return (int) $value;
	}

	public static function toDateOrNull(SimpleXMLElement $parent, string $child): ?DateTimeImmutable
	{
		$value = self::toStringOrNull($parent, $child);

		if ($value === null) {
			return null;
		}

		// Date with timezone offset, e.g. 2012-07-27+02:00 or 2012-07-27+0200
		foreach (['!Y-m-dP', '!Y-m-dO', '!Y-m-d'] as $format) {
			$date = DateTimeImmutable::createFromFormat($format, $value);

			if ($date !== false) {
				return $date;
			}
		}

		return null;
	}

}
