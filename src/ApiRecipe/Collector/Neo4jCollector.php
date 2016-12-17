<?php

namespace ApiRecipe\Collector;

use GraphAware\Neo4j\Client\ClientBuilder;
use GraphAware\Neo4j\Client\Client;
use ApiRecipe\Manager\ActionManager;

class Neo4jCollector implements CollectorInterface
{
    /**
     * @var Client
     */
    protected $client;

    public function __construct($login = 'neo4j', $password = 'neo4j', $host = 'localhost', $port = 7474, $bolt = false)
    {
        $connectionAddress = sprintf(
            '%s://%s:%s@%s:%d',
            $bolt ? 'bolt' : 'http',
            $login,
            $password,
            $host,
            $port == 7474 && $bolt ? 7687 : $port
        );

        $this->client = ClientBuilder::create()
            ->addConnection($bolt ? 'bolt' : 'default', $connectionAddress)
            ->build();
    }

    /**
     * @return self
     */
    public function setup()
    {
        $this->generateDays()
            ->generateHours()
            ->generateMinutes()
        ;
    }

    /**
     * @param ActionManager $action
     *
     * @return $this
     */
    public function onAction($action)
    {
        $now = new \DateTime();
        $minutes = $now->format('i');
        $hours = $now->format('h');
        $day = $now->format('w');

        $provider = $action->getProviderName();
        $method = $action->getMethod();
        $arg = $action->getArgument();
    }

    /**
     * @return $this
     */
    protected function generateDays()
    {
        $indexes = [
            'name',
            'number',
        ];
        foreach ($indexes as $index) {
            $query = sprintf(
                'CREATE CONSTRAINT ON (d:Day) ASSERT d.%s IS UNIQUE',
                $index
            );
        }

        $datetime = new \DateTime();
        $i = 0;
        while ($i <= 7) {
            $dayName = $datetime->format('l');
            $dayNumber = $dateTime->format('w');
            $dateTime->modify('+1 day');

            $query = sprintf(
                'MERGE (d:Day {dayNumber: %d}) ON CREATE SET
                    name = {dayName},
                    number = {dayNumber}
                ',
                $dayNumber
            );

            $this->client->run($query, [
                'dayName' => $dayName,
                'dayNumber' => $dateTime->format('w'),
            ]);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function generateHours()
    {
        $indexQuery = 'CREATE CONSTRAINT ON (h:Hour) ASSERT h.number IS UNIQUE';
        $this->client->run($indexQuery);

        for ($i = 0; $i < 24; ++$i) {
            $query = sprintf(
                'MERGE (h:Hour {number: %d}) ON CREATE SET h.number = {hourInt}',
                $i
            );

            $this->client->run($query, [
                'hourInt' => $i,
            ]);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function generateMinutes()
    {
        $indexQueries = [
            'CREATE CONSTRAINT ON (m:MinutesInterval) ASSERT m.start IS UNIQUE',
            'CREATE CONSTRAINT ON (m:MinutesInterval) ASSERT m.end IS UNIQUE',
        ];

        foreach ($indexQueries as $indexQuery) {
            $this->client->run($indexQuery);
        }

        $start = 0;

        for ($i = 0; $i < 60; $i + 15) {
            $query = sprintf(
                'MERGE (m:MinutesInterval {start: %d, end %d}) ON CREATE SET
                    m.start = {start},
                    m.end = {end},
                ',
                $start,
                $i
            );

            $this->client->run($query, [
                'start' => $start,
                'end' => $i,
            ]);

            $start = $i;
        }

        return $this;
    }
}
