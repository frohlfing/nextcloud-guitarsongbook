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
 * @template-extends QBMapper<Shot>
 */
class ShotMapper extends QBMapper
{
	public function __construct(IDBConnection $db)
    {
		parent::__construct($db, 'guitarsongbook_shots', Shot::class);
	}

    /**
     * @throws MultipleObjectsReturnedException
     * @throws DoesNotExistException
     * @throws Exception
     */
	public function find(int $id, string $userId): Shot
    {
		$qb = $this->db->getQueryBuilder();
		$qb->select('shots.*')
			->from('guitarsongbook_shots', 'shots')
            ->innerJoin('shots','guitarsongbook', 'songs', 'shots.song_id = songs.id')
			->where($qb->expr()->eq('shots.id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)))
			->andWhere($qb->expr()->eq('songs.user_id', $qb->createNamedParameter($userId)));
		return $this->findEntity($qb);
	}

    /**
     * @param int $songId
     * @param string $userId
     * @return array
     * @throws Exception
     */
	public function findAllOfSong(int $songId, string $userId): array
    {
		$qb = $this->db->getQueryBuilder();
		$qb->select('shots.*')
			->from('guitarsongbook_shots', 'shots')
            ->innerJoin('shots','guitarsongbook', 'songs', 'shots.song_id = songs.id')
            ->where($qb->expr()->eq('songs.id', $qb->createNamedParameter($songId, IQueryBuilder::PARAM_INT)))
			->andWhere($qb->expr()->eq('songs.user_id', $qb->createNamedParameter($userId)));
		return $this->findEntities($qb);
	}
}
