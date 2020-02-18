<?php
namespace TheAkademy\ProductManipulationBundle\Command\Product;

use Akeneo\Tool\Bundle\ElasticsearchBundle\Cursor\Cursor;
use Akeneo\Pim\Enrichment\Component\Product\Model\Product;
use Akeneo\Pim\Enrichment\Component\Product\Query\Filter\Operators;
use Akeneo\Pim\Enrichment\Component\Product\Query\ProductQueryBuilderInterface;
use Akeneo\Pim\Enrichment\Component\Product\Query\Sorter\Directions;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class ProductManipulationCommand extends ContainerAwareCommand
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $products = $this->getProducts($input);

        if ($input->getOption('apply-modifications') !== false) {
            $this->updateProducts($products, $output);
        }

        $this->displayProducts($products, $output);
    }

    protected function configure()
    {
        $this
            ->setName('pim:products:manipulate')
            // the short description shown while running "php bin/console list"
            ->setDescription('Product manipulation command workshop (2)')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create a user...')
            ->addOption('apply-modifications', false, InputOption::VALUE_OPTIONAL, 'apply modifications', false)
        ;
    }

    private function getProducts(InputInterface $input)
    {
        $pqbFactory = $this->getContainer()->get('pim_catalog.query.product_query_builder_factory');
        /** @var ProductQueryBuilderInterface $queryBuilder */
        $queryBuilder = $pqbFactory->create(['default_locale' => 'en_US', 'default_scope' => 'ecommerce']);

        $queryBuilder
            ->addFilter('categories', Operators::IN_CHILDREN_LIST, ['cameras'])
            ->addFilter('family', Operators::IN_LIST, ['camcorders'])
            ->addFilter('name', Operators::CONTAINS, 'Canon')
            ->addFilter('price', Operators::GREATER_THAN, ['amount' => 110, 'currency' => 'EUR'])
            ->addSorter('name', Directions::ASCENDING, ['scope' => 'mobile']);
            // ->addSorter('price', Directions::ASCENDING);

        return $queryBuilder->execute();
    }

    private function displayProducts($products, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders([
            'skus',
            'name',
            'price EUR',
            'price USD',
            'description ecommerce',
            'description mobile',
            'family',
            'key features',
            'categories'
        ]);
        foreach ($products as $product) {
            /** @var Product $product */
            $table->addRow([
                $product->getIdentifier(),
                $product->getValue('name'),
                $product->getValue('price')->getPrice('EUR')->getData(),
                $product->getValue('price')->getPrice('USD'),
                $product->getValue('description', 'en_US', 'ecommerce'),
                $product->getValue('description', 'en_US', 'mobile'),
                $product->getFamily()->getCode(),
                $product->getValue('key_features'),
                $product->getCategories()->current(),
            ]);
        }

        $table->render();
    }

    private function updateProducts(Cursor $products, OutputInterface $output)
    {
        $validValues = [
            'values' => [
                'key_features' => [
                    [
                        'locale' => null,
                        'scope' => null,
                        'data' => 'valid data',
                    ]
                ],
            ]
        ];
        $invalidValues = [
            'values' => [
                'key_features' => [
                    [
                        'locale' => null,
                        'scope' => null,
                        'data' => 'invalid data more than 25 characters',
                    ]
                ],
            ]
        ];

        $updater = $this->getContainer()->get('pim_catalog.updater.product');
        $validator = $this->getContainer()->get('pim_catalog.validator.product');
        $validCount = ceil($products->count() / 2);
        foreach ($products as $key => $product) {
            /** @var Product */
            $data = ($key <= $validCount) ? $validValues : $invalidValues;

            $updater->update($product, $data);
            $errors = $validator->validate($product);
            if ($errors->count()) {
                $output->writeln("Error: " . $product->getIdentifier() . ' ' . $errors);
                continue;
            }

            $validProducts[] = $product;
        }

        if (!empty($validProducts ?? [])) {
            $saver = $this->getContainer()->get('pim_catalog.saver.product');
            $output->writeln('Updating ' . count($validProducts) . ' products');
            $saver->saveAll($validProducts);
        }
    }
}
