<?php

namespace ApiRecipe\Suggester;

use GraphAware\Neo4j\Client\ClientBuilder;
use GraphAware\Neo4j\Client\Client;
use ApiRecipe\Provider\HydratorTrait;
use ApiRecipe\Manager\RecipeManager;

class Neo4jSuggester implements SuggesterInterface
{
    use HydratorTrait;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $login = 'neo4j';

    /**
     * @var string
     */
    protected $password = 'neo4j';

    /**
     * @var string
     */
    protected $host = 'localhost';

    /**
     * @var string
     */
    protected $port = 7474;

    /**
     * @var string
     */
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
    }

    public function suggest()
    {
        $now = new \DateTime();
        $minutes = (int) $now->format('i');
        $hours = (int) $now->format('H');
        $day = (int) $now->format('w');
        $startInterval = $minutes / 15;
        $start = $minutes - ($minutes % 15);
        $end = $start + 15;

        $query = '
            MATCH
                (n:Recipe)-[r1:ON]->(d:Day {number: {day}}),
                (n)-[r2:ON]->(h:Hour {number: {hour}}),
                (n)-[r3:ON]->(m:MinutesInterval {start: {start}, end: {end}})
            RETURN
                n.title,
                (r1.iterations + r2.iterations + r3.iterations) / 3 AS iterations
            ORDER BY iterations DESC
            LIMIT 10
        ';

        $result = $this->client->run($query, [
            'day' => $day,
            'hour' => $hours,
            'start' => $start,
            'end' => $end,
        ]);

        $recipes = [];
        foreach ($result->getRecords() as $record) {
            $recipeTitle = $record->value('n.title');
            $recipeManager = new RecipeManager($recipeTitle);
            $recipeManager->setProbability((int) $record->value('iterations'));
            $recipes [] = $recipeManager;
        }

        return $recipes;
    }
}
