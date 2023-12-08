<?php
declare(strict_types=1);

namespace RoutePlus\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use RoutePlus\RoutesExtractor;

/**
 * RoutesExtractDump command.
 */
class RoutesExtractDumpCommand extends Command
{
    /**
     * Hook method for defining this command's option parser.
     *
     * @see https://book.cakephp.org/4/en/console-commands/commands.html#defining-arguments-and-options
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser)
            ->addOption('withApp', [
                'help' => __('Include app controllers as source to extract'),
            ])
            ->addOption('withPlugins', [
                'help' => __('Include plugin\'s controllers as source to extract, name separated by space, ex: --withPlugins "MyPlugin AnottherPlugin Some/Plugin"'),
            ])
            ->addOption('mode', [
                'choices' => ['from-application', 'custom'],
                'default' => 'from-application',
                'required' => true,
                'help' => __('Use static method \App\Application::createRouteExtractor to create extractor'),
            ])
            ->addOption('output', [
                'default' => CONFIG . 'routes_extracted_list.php',
                'help' => __('The path of the file to output the routes array, ex: /my/app/path/config/routes_list.php'),
            ]);

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io)
    {
        $withApp = $args->hasOption('withApp');
        $withPlugins = $args->getOption('withPlugins');
        $io->out(__('Route extract dump mode: {0}', $args->getOption('mode')));
        if ($args->getOption('mode') === 'from-application') {
            assert($withApp === false && !$withPlugins, __(
               'Can\'t use withApp or withPlugins options with mode "from-application", use --mode custom',
            ));
            $extractor = $this->createExtractorFromApplication();
            $io->info(__('Using extractor from application'));
        } else {
            $extractor = $this->createExtractorFromOptions($io, $withApp, $withPlugins);
        }

        $routesList = $extractor->extract();
        $path = $args->getOption('output');
        $io->info(__('Found {0} routes', count($routesList)));
        $io->info(__('Output path: {0}', $path));
        file_put_contents(
            $path,
            '<?php return ' . var_export($routesList, true) . ';'
        );
        if (!file_exists($path)) {
            $io->err(__('Could not find the output file after trying to dump routes'));
        }
    }

    /**
     * @return \RoutePlus\RoutesExtractor
     */
    protected function createExtractorFromApplication(): RoutesExtractor
    {
        return \App\Application::createRouteExtractor();
    }

    /**
     * @param bool $withApp
     * @param \Cake\Console\ConsoleIo $io
     * @param string|null $withPlugins
     * @return \RoutePlus\RoutesExtractor
     */
    protected function createExtractorFromOptions(ConsoleIo $io, bool $withApp, string|null $withPlugins): RoutesExtractor
    {
        $extractor = new RoutesExtractor();
        if ($withApp) {
            $io->info(__('Requested to add app controllers as source'));
            $extractor->withApp();
        } else {
            $io->warning(__('Not requested app controllers as source'));
        }

        if ($withPlugins) {
            $plugins = explode(' ', $withPlugins);
            $io->info(__('Requested to add plugins as source: {0}', $withPlugins));
            $extractor->withPlugins($plugins);
        } else {
            $io->out(__('Not requested plugins controllers as source'));
        }
        return $extractor;
    }
}
