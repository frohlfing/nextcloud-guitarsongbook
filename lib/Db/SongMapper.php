<?php
/** @noinspection PhpUnused */
declare(strict_types=1);
// SPDX-FileCopyrightText: Frank Rohlfing <mail@frank-rohlfing.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\GuitarSongbook\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\Exception;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

/**
 * @template-extends QBMapper<Song>
 */
class SongMapper extends QBMapper
{
	public function __construct(IDBConnection $db)
    {
		parent::__construct($db, 'guitarsongbook', Song::class);
	}

    /**
     * @param int $id
     * @param string $uid
     * @return Song
     * @throws DoesNotExistException
     * @throws Exception
     * @throws MultipleObjectsReturnedException
     */
	public function find(int $id, string $uid): Song
    {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('guitarsongbook')
			->where($qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)))
			->andWhere($qb->expr()->eq('uid', $qb->createNamedParameter($uid)));
		return $this->findEntity($qb);
	}

    /**
     * @param string $uid
     * @return array
     * @throws Exception
     */
	public function findAll(string $uid): array
    {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('guitarsongbook')
			->where($qb->expr()->eq('uid', $qb->createNamedParameter($uid)));
		return $this->findEntities($qb);
	}
}
