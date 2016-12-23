<?php

namespace ApiRecipe\Command;

use ApiAi\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use ApiRecipe\Manager\YmlParserTrait;

/**
 * Class QueryCommand.
 */
class DialogCommand extends Command
{
    use YmlParserTrait;

    /**
     * @var array
     */
    protected $config;

    protected function configure()
    {
        $this->setName('api:query')
            ->setDescription('The query requests return structured data in JSON format with an action and parameters for that action.')
            ->addArgument('access_token', InputArgument::OPTIONAL, 'Access token', $this->getAccessToken());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $access_token = $input->getArgument('access_token');
        $client = new Client($access_token);

        ask:

        $helper = $this->getHelper('question');
        $messagePrompt = new Question('>>> ');

        $message = $helper->ask($input, $output, $messagePrompt);
        $results = $client->query('/query', [
            'query' => $message,
            'lang' => $this->getLang(),
        ])['result'];
        $provider = $results['parameters']['provider'];
        $action = $results['parameters']['action'];
        $argument = isset($results['parameters']['argument']) && $results['parameters']['argument'] != '' ? $results['parameters']['argument'] : null;
        if (null !== $argument) {
            $fullQualifiedAction = sprintf('%s:%s:%s', $provider, $action, $argument);
        } else {
            $fullQualifiedAction = sprintf('%s:%s', $provider, $action);
        }
        var_dump($fullQualifiedAction);
        die();
        $recipe = $results['action'];
        $method = $results['parameters']['method'];
        $argument = isset($results['parameters']['argument']) ? $results['parameters']['argument'] : null;

        $method = $results['metadata']['intentName'];

        foreach ($results['parameters'] as $providerName) {
            var_dump($providerName.':'.$method);
        }
        die();
        if (null === $argument) {
            $action = sprintf('%s:%s', $provider, $method);
        } else {
            $action = sprintf('%s:%s', $provider, $argument);
        }
        var_dump($action);
        die();
        foreach ($results['result'] as $result) {
            var_dump($result);
        }
        die();
        $response = json_decode((string) $query->getBody(), true);

        $output->writeln('<info>+ Response body :</info>');
        $output->writeln('<comment>'.json_encode($response, JSON_PRETTY_PRINT).'</comment>');

        goto ask;
    }

    /**
     * @param string $category
     *
     * @return array
     */
    protected function getConfig($category = null)
    {
        if (null === $this->config) {
            $file = sprintf('%s/../../../app/config/config.yml', __DIR__);
            $this->config = $this->parseYmlFile($file);
        }
        if (null !== $category) {
            if (!isset($this->config[$category])) {
                return [];
            }

            return $this->config[$category];
        }

        return $this->config;
    }

    private function getAccessToken()
    {
        $config = $this->getConfig('bot');
        if (0 === count($config)) {
            return null;
        }

        return $config['access_token'];
    }

    private function getLang()
    {
        return $this->getConfig('bot')['lang'];
    }
}
