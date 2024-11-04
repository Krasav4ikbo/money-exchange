<?php

namespace App\Command;

use App\Exception\CurrencyProviderException;
use App\Service\CurrencyRateUpdateService;
use App\Utility\CurrencyUpdateSource;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:currency-rate-update',
    description: 'Update currency rates',
    aliases: ['app:update-currency-rates'],
    hidden: false,
)]
class CurrencyRateUpdateCommand extends Command
{
    public function __construct(
        private readonly CurrencyRateUpdateService $currencyRateUpdateService,
        ?string                                    $name = null
    )
    {
        parent::__construct($name);
    }

    /**
     * @throws CurrencyProviderException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            '====================================',
            'Check source',
            '====================================',
            ''
        ]);

        $source = $input->getArgument('source');

        if (!in_array($source, CurrencyUpdateSource::SOURCES)) {
            $output->writeln([
                '====================================',
                'Incorrect source type',
                '====================================',
                ''
            ]);

            return Command::INVALID;
        }

        $this->currencyRateUpdateService->update($source);

        $output->writeln([
            '====================================',
            'Rates updated successfully',
            '====================================',
        ]);

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('source', InputArgument::REQUIRED, 'Source of data for updating currency rates');
    }
}