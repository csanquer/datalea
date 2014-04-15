<?php

namespace CSanquer\FakeryGenerator\Dump;

use CSanquer\FakeryGenerator\Config\ConfigSerializer;
use CSanquer\FakeryGenerator\Model\Config;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * ConsoleDumpManager
 *
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 */
class ConsoleDumpManager extends DumpManager
{
    /**
     *
     * @var Stopwatch 
     */
    protected $stopwatch;
    
    /**
     *
     * @var OutputInterface 
     */
    protected $output;
    
    /**
     *
     * @var ProgressHelper 
     */
    protected $progress;
    
    /**
     * 
     * @param \Symfony\Component\Stopwatch\Stopwatch $stopwatch
     */
    public function setStopwatch(Stopwatch $stopwatch)
    {
        $this->stopwatch = $stopwatch;
    }

    /**
     * 
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * 
     * @param \Symfony\Component\Console\Helper\ProgressHelper $progress
     */
    public function setProgress(ProgressHelper $progress)
    {
        $this->progress = $progress;
    }
        
    protected function createConfigFiles(Config $config, $outputDir, array $formats)
    {
        $this->stopwatch->start('dumping_config', 'generate_dumps');
        $files = parent::createConfigFiles($config, $outputDir, $formats);
        $this->stopwatch->stop('dumping_config');
        
        return $files;
    }
    
    protected function dumpConfigFile(Config $config, $outputDir, $format = 'json')
    {
        $this->output->writeln('Dumping Configuration as <comment>'.strtoupper($format).'</comment> ...');
        
        return parent::dumpConfigFile($config, $outputDir, $format);
    }
    
    protected function createDumpers(Config $config, $outputDir, $zipped)
    {
        $this->output->writeln('Initializing files ...');
        $this->stopwatch->start('initializing_files', 'generate_dumps');
        
        $dumpers = parent::createDumpers($config, $outputDir, $zipped);
        if (count($dumpers)) {
            $this->output->writeln(
                'Formats : <comment>'.
                implode('</comment>, <comment>', array_keys($dumpers)).
                '</comment>'
            );
        }
        
        $this->stopwatch->stop('initializing_files');
        
        return $dumpers;
    }
    
    protected function dumpRowsToAllFiles(Config $config, array $dumpers)
    {
        // generate random data and write row by row
        $this->stopwatch->start('generating_rows', 'generate_dumps');

        $this->progress->start($this->output, $config->getFakeNumber());
        $unit = floor($config->getFakeNumber()/100);
        $this->progress->setRedrawFrequency($unit < 1 ? 1 : $unit);
        $this->progress->setBarCharacter('<comment>=</comment>');

        $this->output->writeln('Generating <info>'.$config->getFakeNumber().'</info> rows ...');

        parent::dumpRowsToAllFiles($config, $dumpers);

        $this->progress->finish();

        $this->output->writeln('Finalizing files ...');

        $this->stopwatch->stop('generating_rows');
        $this->stopwatch->start('finalizing_files', 'generate_dumps');
    }

    protected function generateAndDumpRows(\Faker\Generator $faker, Config $config, array $dumpers)
    {
        parent::generateAndDumpRows($faker, $config, $dumpers);
        $this->progress->advance();
    }
    
    protected function finalizeAllDumps(array $dumpers)
    {
        // finalize and save dumped files
        $this->stopwatch->start('finalizing_files', 'generate_dumps');
        
        $this->progress->start($this->output, count($dumpers));
        $this->progress->setBarCharacter('<comment>=</comment>');

        $files = parent::finalizeAllDumps($dumpers);

        $this->progress->finish();

        $this->stopwatch->stop('finalizing_files');
        
        return $files;
    }
    
    protected function finalizeDump(DumperInterface $dumper)
    {
        $file = parent::finalizeDump($dumper);
        $this->progress->advance();
        return $file;
    }
    
    protected function compressFiles(Config $config, array $files, $outputDir, Filesystem $fs)
    {
        $this->stopwatch->start('compressing_files', 'generate_dumps');
        $this->output->writeln('Compressing files into zip ...');
        
        $files = parent::compressFiles($config, $files, $outputDir, $fs);

        $this->stopwatch->stop('compressing_files');
        
        return $files;
    }
}

