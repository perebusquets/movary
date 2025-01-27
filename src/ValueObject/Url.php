<?php declare(strict_types=1);

namespace Movary\ValueObject;

use RuntimeException;

class Url
{
    private function __construct(private readonly string $url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new RuntimeException('Invalid url: ' . $url);
        }
    }

    public static function createFromString(string $url) : self
    {
        return new self($url);
    }

    public function __toString() : string
    {
        return $this->url;
    }

    public function getPath() : ?string
    {
        $path = parse_url($this->url, PHP_URL_PATH);

        if ($path === false) {
            throw new RuntimeException(sprintf('Could not parse path from url "%s"', $this->url));
        }

        return $path;
    }
}
