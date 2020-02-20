<?php


namespace TheAkademy\APIBundle\Command\Product\Rules;

use Akeneo\Pim\ApiClient\Search\SearchBuilder;
use Akeneo\Pim\Enrichment\Component\Product\Model\Product;
use Akeneo\Pim\Enrichment\Component\Product\Query\Filter\Operators;
use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientBuilder;
use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class ProductGetLastUpdatedCommand extends ContainerAwareCommand
{
    const PRODUCT_LIMIT = 100;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $products = $this->getProducts();
        $this->displayProducts($products, $output);
        $output->writeln('done');
    }

    protected function configure()
    {
        $this
            ->setName('pim:product:latest-updated')
            // the short description shown while running "php bin/console list"
            ->setDescription('Get last 5m updated products')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Last 5 min updated products');
    }

    private function getProducts()
    {
        $date = date('Y-m-d H:i:s', strtotime('-55 minutes'));
        /** @var AkeneoPimEnterpriseClientInterface $client */
        $client = $this->getClient();
        $searchBuilder = new SearchBuilder();
        $searchBuilder->addFilter('enabled', Operators::EQUALS, true)
            ->addFilter('categories', Operators::IN_CHILDREN_LIST, ['cameras'])
            ->addFilter('family', Operators::IN_LIST, ['camcorders'])
            ->addFilter('name', Operators::CONTAINS, 'Canon')
            ->addFilter('updated', Operators::GREATER_THAN, $date);

        return $client->getProductApi()->all(100, ['search' => $searchBuilder->getFilters()]);
    }

    private function displayProducts($products, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders([
            'skus',
            'name'
        ]);
        
        foreach ($products as $product) {
            /** @var Product $product */
            $table->addRow([
                $product['identifier'],
                $product['values']['name'][0]['data'] ?? 'No name provided'
            ]);
        }

        $table->render();
    }

    private function getClient()
    {
        $clientBuilder = new AkeneoPimEnterpriseClientBuilder('http://httpd:80');

        return $clientBuilder->buildAuthenticatedByPassword(
            '1_4sroh3920so4g0woc8wkw8kos0cook8so8gsk4oo840wkwoo08',
            '4z411u1pqa8s8gcwwkcggogoogs8go0s80cso40ss08sgko8co',
            'admin',
            'admin'
        );
    }
}