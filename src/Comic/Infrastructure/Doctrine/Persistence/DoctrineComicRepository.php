<?php

namespace App\Comic\Infrastructure\Doctrine\Persistence;

use App\Comic\Application\Query\ListComics\ListComicsFilter;
use App\Comic\Domain\Contracts\ComicRepositoryInterface;
use App\Comic\Domain\Model\Comic;
use App\Comic\Domain\Model\ComicCollection;
use App\Shared\Domain\Pagination\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comic>
 */
class DoctrineComicRepository extends ServiceEntityRepository implements ComicRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comic::class);
    }

    public function save(Comic $comic): void
    {
        $this->_em->persist($comic);
        $this->_em->flush();
    }

    public function all(): ComicCollection
    {
        $comics = parent::findAll();

        return new ComicCollection($comics);
    }

    public function paginatedFilter(ListComicsFilter $filters, Page $page): ComicCollection
    {
        $qb = $this->createQueryBuilder('c');

        $this->applyFilters($qb, $filters);

        $qb->setFirstResult($page->offset());
        $qb->setMaxResults($page->limit());

        return new ComicCollection($qb->getQuery()->execute());
    }

    public function countByFilters(ListComicsFilter $filters): int
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('count(c.id)');

        $this->applyFilters($qb, $filters);

        return $qb->getQuery()->getSingleScalarResult();
    }

    private function applyFilters(QueryBuilder $qb, ListComicsFilter $filters): void
    {
        if ($publisher = $filters->publisherFilter()) {
            $qb
                ->andWhere('c.publisher = :publisher')
                ->setParameter('publisher', $publisher);
        }
    }
}
