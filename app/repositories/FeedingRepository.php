<?php

namespace App\Repositories;

use App\Entities\Feeding;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Nette\Utils\DateTime;

class FeedingRepository
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var AnimalRepository
     */
    private $animalRepository;

    /**
     * FeedingRepository constructor.
     *
     * @param EntityManager $entityManager
     * @param UserRepository $userRepository
     * @param AnimalRepository $animalRepository
     */
    public function __construct(EntityManager $entityManager, UserRepository $userRepository, AnimalRepository $animalRepository)
    {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(Feeding::class);
        $this->userRepository = $userRepository;
        $this->animalRepository = $animalRepository;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    /**
     * @param $id
     * @return Feeding|null
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @return Feeding[]
     */
    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function findUserFeedings($userId)
    {
        $queryBuilder = $this->repository->createQueryBuilder()
            ->select('f')
            ->from(Feeding::class, 'f')
            ->where('f.keeper = :user')
            ->setParameter(':user', $userId)
            ->andwhere('f.start >= :actual')
            ->setParameter(':actual', new DateTime())
            ->andWhere('f.done = FALSE');

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param $values
     * @return Feeding
     */
    public function saveFormData($values)
    {
        $feeding = NULL;

        if (isset($values->id)) {
            $feeding = $this->find($values->id);
        }

        if ( ! $feeding) {
            $feeding = new Feeding();
        }

        $feeding->setAmount($values->amount);
        $feeding->setSpecies($values->species);
        $feeding->setDone($values->done);
        $feeding->setKeeper($this->userRepository->find($values->keeper_id));
        $feeding->setAnimal($this->animalRepository->find($values->animal_id));

        if ($values->start) {
            $feeding->setStart(new DateTime($values->start));
        }

        if ($values->end) {
            $feeding->setEnd(new DateTime($values->end));
        }

        $this->entityManager->persist($feeding);
        $this->entityManager->flush();

        return $feeding;
    }
}
