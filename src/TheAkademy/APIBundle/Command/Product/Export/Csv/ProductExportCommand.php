<?php
namespace TheAkademy\APIBundle\Command\Product\Export\Csv;

use Akeneo\Pim\ApiClient\Search\SearchBuilder;
use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientBuilder;
use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;

class ProductExportCommand extends Command
{

    const FILE_NAME = 'products.csv';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $writer = WriterFactory::create(Type::CSV);
        $writer->openToFile(self::FILE_NAME);

        $writer->addRow(['sku', 'categories', 'family', 'description-en_US-ecommerce']);
        foreach ($this->getProducts() as $product) {
            $writer->addRow($this->parseProduct($product));
        }

        $writer->close();
        $output->writeln('done');
    }

    private function parseProduct($product)
    {
        $result = [
            'sku' => $product['identifier'],
            'categories' => implode(',', $product['categories'] ?? []),
            'family' => $product['family'],
            'description-en_US-ecommerce' => '',
        ];

        if (isset($product['values']['description'])) {
            foreach ($product['values']['description'] as $value) {
                if ($value['locale'] == 'en_US' && $value['scope'] == 'ecommerce') {
                    $result['description-en_US-ecommerce'] = $value['data'];
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @return \Akeneo\Pim\ApiClient\Pagination\ResourceCursorInterface
     */
    private function getProducts()
    {
        /** @var AkeneoPimEnterpriseClientInterface $client */
        $client = $this->getClient();
        $searchBuilder = new SearchBuilder();
        $searchBuilder->addFilter('enabled', '=', true);

        return $client->getProductApi()->all(100, ['search' => $searchBuilder->getFilters()]);
    }

    private function getClient()
    {
        $clientBuilder = new AkeneoPimEnterpriseClientBuilder('http://httpd:80');

        return $clientBuilder->buildAuthenticatedByPassword(
            '1_4sroh3920so4g0woc8wkw8kos0cook8so8gsk4oo840wkwoo08',
            '4z411u1pqa8s8gcwwkcggogoogs8go0s80cso40ss08sgko8co',
            'admin',
            'admin'
        );    }

    protected function configure()
    {
        $this
            ->setName('pim:product:export-csv')
            // the short description shown while running "php bin/console list"
            ->setDescription('Export products to csv file')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command exports products to csv');
    }
}
