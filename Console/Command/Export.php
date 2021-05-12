<?php
namespace AHT\ConfigProduct\Console\Command;
 
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AHT\ConfigProduct\Helper\CreateConfig;
use AHT\ConfigProduct\Helper\ExportFile;
 
class Export extends Command
{
    protected $createConfig;

    protected $exportFile;
 
    const NAME_ARGUMENT = "csv_name";

    public function __construct(
        CreateConfig $createConfig,
        ExportFile $exportFile
    ) {
        $this->createConfig = $createConfig;
        $this->exportFile = $exportFile;
        parent::__construct();
    }
 
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("demo:export");
        $this->setDescription("Export data from csv file");
        $this->setDefinition([
            new InputArgument(self::NAME_ARGUMENT, InputArgument::OPTIONAL, "Csv Name")
        ]);
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output) 
    {
        $name = $input->getArgument(self::NAME_ARGUMENT);
        $output->writeln("Exporting CSV " . $name);
        return $this->createConfig->exportFileToCSV();
    }
}