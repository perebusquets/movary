<?php declare(strict_types=1);

namespace Movary\Service\Tmdb;

use Movary\Api\Tmdb\Exception\TmdbResourceNotFound;
use Movary\Api\Tmdb\TmdbApi;
use Movary\Domain\Person\PersonApi;
use Movary\JobQueue\JobQueueScheduler;
use Movary\ValueObject\DateTime;
use Psr\Log\LoggerInterface;

class SyncPerson
{
    public function __construct(
        private readonly TmdbApi $tmdbApi,
        private readonly PersonApi $personApi,
        private readonly JobQueueScheduler $jobScheduler,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function syncPerson(int $tmdbId) : void
    {
        try {
            $tmdbPerson = $this->tmdbApi->fetchPersonDetails($tmdbId);
        } catch (TmdbResourceNotFound $e) {
            $this->logger->debug('No person existing on tmdb with id: ' . $tmdbId);

            return;
        }

        $person = $this->personApi->findByTmdbId($tmdbId);

        if ($person === null) {
            $person = $this->personApi->create(
                $tmdbPerson->getTmdbId(),
                $tmdbPerson->getName(),
                $tmdbPerson->getGender(),
                $tmdbPerson->getKnownForDepartment(),
                $tmdbPerson->getProfilePath(),
                $tmdbPerson->getBirthDate(),
                $tmdbPerson->getDeathDate(),
                $tmdbPerson->getPlaceOfBirth(),
                updatedAtTmdb: DateTime::create(),
            );

            $this->jobScheduler->storePersonIdForTmdbImageCacheJob($person->getId());

            return;
        }

        $originalTmdbPosterPath = $person->getTmdbPosterPath();

        $person = $this->personApi->update(
            $person->getId(),
            $tmdbPerson->getTmdbId(),
            $tmdbPerson->getName(),
            $tmdbPerson->getGender(),
            $tmdbPerson->getKnownForDepartment(),
            $tmdbPerson->getProfilePath(),
            $tmdbPerson->getBirthDate(),
            $tmdbPerson->getDeathDate(),
            $tmdbPerson->getPlaceOfBirth(),
            DateTime::create(),
        );

        if ($originalTmdbPosterPath !== $person->getTmdbPosterPath()) {
            $this->jobScheduler->storePersonIdForTmdbImageCacheJob($person->getId());
        }
    }
}
