<?php
namespace TheAkademy\APIBundle\Command\Product\Export\Csv;

use Akeneo\Pim\ApiClient\Search\SearchBuilder;
use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientBuilder;
use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

class ProductImportCommand extends ContainerAwareCommand
{
    const FILE_NAME = 'products.csv';
    const PRODUCT_LIMIT = 100;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $products = $this->getProducts();
        $api = $this->getClient()->getProductApi();

        $progressBar = new ProgressBar($output, count($products));
        $progressBar->start();
        foreach (array_chunk($products, self::PRODUCT_LIMIT) as $chunk) {
            $responseIterator = $api->upsertList($chunk);
            $progressBar->advance(count($chunk));
            foreach ($responseIterator as $responseLine) {
                if ($responseLine['status_code'] >= 400) {
                    $errors[] = $responseLine;
                }
            }
        };

        $progressBar->finish();

        foreach ($errors ?? [] as $error) {
            $output->writeln($error['status_code'] . ' error for product ' . $error['identifier'] . ' due to: ' . $error['message']);
        }
    }

    private function getProducts()
    {
        $converter = $this->getContainer()->get('pim_connector.array_converter.flat_to_standard.product_delocalized');

        $reader = ReaderFactory::create(Type::CSV);
        $reader->open(self::FILE_NAME);

        $header = [];
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                if (empty($header)) {
                    $header = $row;
                    continue;
                }

                $flatProduct = array_combine($header, $row);

                $products[] = $converter->convert($flatProduct);
            }
        }

        $reader->close();

        return $products ?? [];
    }

    private function getClient()
    {
        $clientBuilder = new AkeneoPimEnterpriseClientBuilder('http://httpd:80');

        return $clientBuilder->buildAuthenticatedByPassword('1_4v7kwbrrrxmo4sk0kw040gggokkg8kgocg0o0kg08oc0cks80o', '55jz4mi07400088gg008kkgg84044k848k80c88ssg0owoko0w', 'admin', 'admin');
    }

    protected function configure()
    {
        $this
            ->setName('pim:product:import-csv')
            // the short description shown while running "php bin/console list"
            ->setDescription('Import products from csv file')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command import products from csv');
    }
}
