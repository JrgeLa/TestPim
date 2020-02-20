<?php


namespace TheAkademy\APIBundle\Command\Product\Rules;

use Akeneo\Pim\ApiClient\Search\SearchBuilder;
use Symfony\Component\Console\Helper\ProgressBar;
use Akeneo\Pim\Enrichment\Component\Product\Query\Filter\Operators;
use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientBuilder;
use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use Akeneo\Tool\Bundle\ElasticsearchBundle\Cursor\Cursor;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProductRulesCommand extends ContainerAwareCommand
{
    const PRODUCT_LIMIT = 100;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $products = $this->getProducts();
        $products = $this->getProductsToUpsert($products);
        $this->updateProducts($products, $output);

        $output->writeln('done');
    }

    protected function configure()
    {
        $this
            ->setName('pim:product:rule')
            // the short description shown while running "php bin/console list"
            ->setDescription('Create a new product rule')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('rule');
    }

    private function getProducts()
    {
        /** @var AkeneoPimEnterpriseClientInterface $client */
        $client = $this->getClient();
        $searchBuilder = new SearchBuilder();
        $searchBuilder->addFilter('enabled', Operators::EQUALS, true)
            ->addFilter('categories', Operators::IN_CHILDREN_LIST, ['cameras'])
            ->addFilter('family', Operators::IN_LIST, ['camcorders'])
            ->addFilter('name', Operators::CONTAINS, 'Canon');

        return $client->getProductApi()->all(100, ['search' => $searchBuilder->getFilters()]);
    }

    private function getProductsToUpsert($products)
    {
        foreach ($products as $product) {
            if (isset($product['values']['name']) && isset($product['values']['description'])) {
                $product['values'] = [
                    'description' => [
                        [
                            'locale' => 'en_US',
                            'scope' => 'ecommerce',
                            'data' => sprintf('This  is an awesome %s', $product['values']['name'][0]['data'])
                        ]
                    ]
                ];

                $toUpsert[] = $product;
            }
        }

        return $toUpsert ?? [];
    }

    private function updateProducts(array $products, OutputInterface $output)
    {
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