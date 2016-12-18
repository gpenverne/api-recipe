<?php

namespace ApiRecipe\Collector;

use GraphAware\Neo4j\Client\ClientBuilder;
use GraphAware\Neo4j\Client\Client;
use ApiRecipe\Provider\HydratorTrait;
use ApiRecipe\Manager\StateManager;
use ApiRecipe\Manager\RecipeManager;

class Neo4jCollector implements CollectorInterface
{
    use HydratorTrait;
    /**
     * @var Client
     */
    protected $client;

    protected $login = 'neo4j';
    protected $password = 'neo4j';
    protected $host = 'localhost';
    protected $port = 7474;
    protected $bolt = false;

    public function __construct($args = [])
    {
        $this->hydrate($args);

        $connectionAddress = sprintf(
            '%s://%s:%s@%s:%d',
            $this->bolt ? 'bolt' : 'http',
            $this->login,
            $this->password,
            $this->host,
            $this->port == 7474 && $this->bolt ? 7687 : $this->port
        );

        $this->client = ClientBuilder::create()
            ->addConnection($this->bolt ? 'bolt' : 'default', $connectionAddress)
            ->build();

        $this->stateManager = new StateManager();
    }

    /**
     * @return self
     */
    public function setup()
    {
        $this->generateDays()
            ->generateHours()
            ->generateMinutes()
            ->generateRecipes()
            ->generateActions();
    }

    /**
     * @param RecipeManager $recipe
     *
     * @return RecipeManager
     */
    public function collect($recipe)
    {
        $recipe = new RecipeManager($recipe);

        $now = new \DateTime();
        $minutes = (int) $now->format('i');
        $hours = (int) $now->format('H');
        $day = (int) $now->format('w');
        $startInterval = $minutes / 15;
        $start = $minutes - ($minutes % 15);
        $end = $start + 15;
        $state = $this->stateManager->getRecipeState($recipe->getFileName());
        $actions = $this->getActions($recipe->getTitle());

        $query =
            '
                MERGE (r:Recipe {title: {title}, state: {state}})
                MERGE (s:State {state: {state}})
                MERGE (m:MinutesInterval {start: {start}, end: {end}})
                MERGE (h:Hour {number: {hour}})
                MERGE (d:Day {number: {day}})
                MERGE (r)-[r1:ON]->(d)
                    ON CREATE SET r1.iterations = 0
                MERGE (r)-[r2:ON]->(h)
                    ON CREATE SET r2.iterations = 0
                MERGE (r)-[r3:ON]->(m)
                    ON CREATE SET r3.iterations = 0
                SET
                    r1.iterations = r1.iterations + 1,
                    r2.iterations = r2.iterations + 1,
                    r3.iterations = r3.iterations + 1
            '
        ;

        $args = [
            'title' => $recipe->getTitle(),
            'start' => $start,
            'state' => $state,
            'end' => $end,
            'hour' => $hours,
            'day' => $day,
        ];

        $this->client->run($query, $args);
        $query = '
            MERGE (a:Action {command: {command}})
            MERGE (r:Recipe {title: {recipe}})
            MERGE (r)-[:EXEC]->(a)
        ';
        foreach ($actions as $action) {
            $this->client->run($query, [
                'command' => $action,
                'recipe' => $recipe->getTitle(),
            ]);
        }

        return $recipe;
    }

    /**
     * @return $this
     */
    protected function generateActions()
    {
        $queries = [
            'CREATE CONSTRAINT ON (a:Action) ASSERT a.command IS UNIQUE',
        ];

        foreach ($queries as $query) {
            $this->client->run($query);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function generateRecipes()
    {
        $queries = [
            'CREATE INDEX ON :Recipe(title)',
            'CREATE INDEX ON :Recipe(state)',
        ];

        foreach ($queries as $query) {
            $this->client->run($query);
        }

        return $this;
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
            $dayNumber = (int) $datetime->format('w');
            $datetime->modify('+1 day');

            $query =
                'MERGE (d:Day {dayNumber: {dayNumber}}) ON CREATE SET
                    d.name = {dayName},
                    d.number = {dayNumber}
                ';

            $this->client->run($query, [
                'dayName' => $dayName,
                'dayNumber' => (int) $datetime->format('w'),
            ]);
            ++$i;
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

        for ($i = 15; $i <= 60; $i += 15) {
            $query = sprintf(
                'MERGE (m:MinutesInterval {start: %d, end: %d}) ON CREATE SET
                    m.start = {start},
                    m.end = {end}
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

    /**
     * @param string $recipe
     *
     * @return string[]
     */
    protected function getActions($recipe)
    {
        $recipeManager = new RecipeManager($recipe);
        $state = $this->stateManager->getRecipeState($recipe);
        if (StateManager::STATE_ON === $state) {
            $state = StateManager::STATE_OFF;
        } else {
            $state = StateManager::STATE_ON;
        }

        return $recipeManager->getActions($state);
    }
}
