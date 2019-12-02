<?php


namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;
use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function getFilterOf($filter){
        return $this->createQueryBuilder("p")->distinct("p.".$filter)
            ->select("p.".$filter)
            ->orderBy("p.".$filter)
            ->getQuery()
            ->getResult();

    }

    public function getByFilter($arr_type, $arr_brand, $arr_size, $arr_price){
        $qb = $this->createQueryBuilder("p");
        if($arr_type == null){
            $qb_type = "1=1";
        }else{
            $qb_type = $qb->expr()->in("p.type", $arr_type);
        }
        if($arr_brand == null){
            $qb_brand = "1=1";
        }else{
            $qb_brand = $qb->expr()->in("p.brand", $arr_brand);
        }
        if($arr_size == null){
            $qb_size = "1=1";
        }else{
            $qb_size = $qb->expr()->in("p.size", $arr_size);
        }
        if($arr_price[0] == null){
            $qb_price1 = "1=1";
            $qb_price2 = "1=1";
        }else{
            $qb_price1 = $qb->expr()->gte("p.price", $arr_price[0]);
            $qb_price2 = $qb->expr()->gte("p.price", $arr_price[1]);
        }
        return $qb->where($qb_type)
            ->andWhere($qb_brand)
            ->andWhere($qb_size)
            ->andWhere($qb_price1)
            ->andWhere($qb_price2)
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();
    }

    public function findInRange($min, $max)
    {
        return $this->createQueryBuilder("p")
            ->where("p.price >= :min")
            ->setParameter('min', $min)
            ->andWhere("p.price <= :max")
            ->setParameter('max', $max)
            ->setMaxResults(20)
            ->getQuery()
            ->execute();

    }



    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}