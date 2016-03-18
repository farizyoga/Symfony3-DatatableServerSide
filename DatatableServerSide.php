<?php

namespace AppBundle\Component;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;

/**
 * Generate server side scripting of Datatable
 *
 * @author Fariz Yoga Syahputra <fariz.yoga@gmail.com>
 */
class DatatableServerSide
{
	private $entityManager;

	private $repository;

	private $column = array();

	private $orderColumn = 'id';

	private $orderDir = 'asc';

	private $search;

	private $start = 0;

	private $maxResult = 10;

	/**
	 * Class Constructor
	 *
	 * @param Doctrine $doctrine initiate doctrine service
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * Holds given entity
	 *
	 * @param $entity
	 */
	public function setRepository($repository)
	{
		$this->repository = $repository;
	}

	/**
	 * Holds all required column in current table
	 *
	 * @param array $column
	 */
	public function setColumn(array $column)
	{
		$this->column = $column;
	}

	/**
	 * Holds Request parameter object to be processed in current request
	 *
	 * @param Request $request
	 */
	public function setRequest(Request $request)
	{
		$param = $request->request;

		$this->orderColumn 	= $this->column[$param->get('order')[0]['column']];
		$this->orderDir 	= $param->get('order')[0]['dir'];
		$this->search 		= $param->get('search')['value'];
		$this->start 		= $param->get('start');
		$this->maxResult 	= $param->get('length');
	}

	/**
	 * Generate Query to be processed
	 *
	 * @return Paginator
	 */
	public function generateQuery()
	{
		$allias = 'dt';
		$i = 1;
		$search = $this->search;

		$qb = $this->entityManager->createQueryBuilder();

		$qb->select($allias);
		$qb->from($this->repository, $allias);

		foreach($this->column as $column) {
			if ($i == 1) {
				$qb->where($allias.'.'.$column.' LIKE :search');
			} else {
				$qb->orWhere($allias.'.'.$column.' LIKE :search');
			}

			$i++;
		}

		$qb->orderBy($allias.'.'.$this->orderColumn, $this->orderDir);
		$qb->setParameter('search', "%$search%");
		$qb->setFirstResult($this->start);
		$qb->setMaxResults($this->maxResult);

		$result = $qb->getQuery();

		$paginator = new Paginator($result, $fetchJoinCollection = true);

		return $paginator;
	}
	
	/**
	 * Send result data to the current request
	 *
	 * @return Paginator
	 */
	public function execute()
	{
		return $this->generateQuery();
	}
}