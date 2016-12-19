<?php

namespace ApiRecipe\Matcher;

use ApiRecipe\Provider\HydratorTrait;

class NightMatcher implements MatcherInterface
{
    use HydratorTrait;

    /**
     * @var float
     */
    protected $latitude;

    /**
     * @var float
     */
    protected $longitude;

    public function _construct($args = [])
    {
        $this->hydrate($args);
    }

    public function match()
    {
        return $this->isNight();
    }

    public function isNight()
    {
        $url = sprintf(
            'http://api.sunrise-sunset.org/json?lat=%s&lng=%s',
            $this->latitude,
            $this->longitude
        );

        $rawJson = file_get_contents($url);
        $dayInfo = json_decode($rawJson);

        $nightStartAt = $dayInfo->results->civil_twilight_begin;
        $dayStartAt = $dayInfo->results->sunrise;

        $nightStartDateTime = new \DateTime($nightStartAt);
        $nightStartDateTime->modify('-5 minutes');

        $dayStartDateTime = new \DateTime($dayStartAt);
        $dayStartDateTime->modify('-5 minutes');

        return
            $nightStartDateTime->getTimestamp() <= time() &&
            $dayStartDateTime->getTimestamp() > time()
        ;
    }
}
