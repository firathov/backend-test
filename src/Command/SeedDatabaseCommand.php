<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Coupon;
use App\Entity\Product;
use App\Enum\CouponTypeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:seed-database')]
class SeedDatabaseCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $conn = $this->em->getConnection();
        $conn->executeStatement('TRUNCATE TABLE product RESTART IDENTITY CASCADE');
        $conn->executeStatement('TRUNCATE TABLE coupon RESTART IDENTITY CASCADE');

        $products = [
            ['Iphone', 100.0],
            ['Наушники', 20.0],
            ['Чехол', 10.0],
        ];

        foreach ($products as [$name, $price]) {
            $product = new Product();
            $product->setName($name);
            $product->setPrice($price);
            $this->em->persist($product);
        }

        $coupon1 = new Coupon();
        $coupon1->setCode('SUMMER10');
        $coupon1->setType(CouponTypeEnum::PERCENT);
        $coupon1->setValue(10.0);

        $coupon2 = new Coupon();
        $coupon2->setCode('WELCOME5');
        $coupon2->setType(CouponTypeEnum::FIXED);
        $coupon2->setValue(5.0);

        $this->em->persist($coupon1);
        $this->em->persist($coupon2);

        $this->em->flush();

        $output->writeln('Seeded 3 products and 2 coupons.');
        return Command::SUCCESS;
    }
}
