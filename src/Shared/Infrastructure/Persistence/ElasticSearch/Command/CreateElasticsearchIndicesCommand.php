<?php

namespace App\Shared\Infrastructure\Persistence\ElasticSearch\Command;

use Elastic\Elasticsearch\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

#[AsCommand(
    name: 'app:elasticsearch:create-indices',
    description: 'Creates all Elasticsearch indices defined in config/elasticsearch/*.yaml',
)]
final class CreateElasticsearchIndicesCommand extends Command
{
    public function __construct(
        private readonly Client $client,
        private readonly string $projectDir,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $directory = $this->projectDir . '/config/elasticsearch';

        if ( ! is_dir($directory)) {
            $output->writeln('<error>Directory config/elasticsearch does not exist.</error>');

            return Command::FAILURE;
        }

        $finder = new Finder();
        $finder->files()->in($directory)->name('*.yaml');

        foreach ($finder as $file) {
            $definition = Yaml::parseFile($file->getRealPath());

            $index = $definition['index'] ?? null;
            $alias = $definition['alias'] ?? null;

            if ( ! is_string($index) || $index === '') {
                $output->writeln(sprintf(
                    '<error>File %s does not define a valid "index".</error>',
                    $file->getFilename(),
                ));

                return Command::FAILURE;
            }

            $exists = $this->client->indices()->exists([
                'index' => $index,
            ])->asBool();

            if ($exists) {
                $output->writeln(sprintf(
                    '<comment>Index "%s" already exists. Skipping.</comment>',
                    $index,
                ));

                continue;
            }

            $body = [
                'settings' => $definition['settings'] ?? new \stdClass(),
                'mappings' => $definition['mappings'] ?? new \stdClass(),
            ];

            $this->client->indices()->create([
                'index' => $index,
                'body' => $body,
            ]);

            $output->writeln(sprintf(
                '<info>Created index "%s".</info>',
                $index,
            ));

            if (is_string($alias) && $alias !== '') {
                $this->client->indices()->putAlias([
                    'index' => $index,
                    'name' => $alias,
                ]);

                $output->writeln(sprintf(
                    '<info>Alias "%s" now points to "%s".</info>',
                    $alias,
                    $index,
                ));
            }
        }

        return Command::SUCCESS;
    }
}
