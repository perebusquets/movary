<?php declare(strict_types=1);

namespace Movary\Api\Trakt\ValueObject\User\Movie\Watched;

use Movary\Api\Trakt\ValueObject\TraktMovie;
use Movary\ValueObject\DateTime;

class Dto
{
    private function __construct(
        private readonly TraktMovie $movie,
        private readonly int $plays,
        private readonly DateTime $lastWatched,
        private readonly DateTime $lastUpdated,
    ) {
    }

    public static function createFromArray(array $data) : self
    {
        return new self(
            TraktMovie::createFromArray($data['movie']),
            $data['plays'],
            DateTime::createFromString($data['last_watched_at']),
            DateTime::createFromString($data['last_watched_at']),
        );
    }

    public function getLastUpdated() : DateTime
    {
        return $this->lastUpdated;
    }

    public function getLastWatched() : DateTime
    {
        return $this->lastWatched;
    }

    public function getMovie() : TraktMovie
    {
        return $this->movie;
    }

    public function getPlays() : int
    {
        return $this->plays;
    }
}
