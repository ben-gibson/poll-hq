<?php

declare(strict_types=1);

namespace App\Adapter\Persistence\Doctrine\ReadModel;

use App\Application\ReadModel\Poll\PollDetails;
use App\Application\ReadModel\Poll\PollRepository;
use Doctrine\DBAL\Connection;

class DBALPollRepository implements PollRepository
{
    private $connection;


    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }


    public function fetchById(string $id) : ?PollDetails
    {
        $qb = $this->connection->createQueryBuilder();

        $qb
            ->select('*')
            ->from('poll_details')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->getSQL();

        $result = $this->connection->fetchAssoc($qb->getSQL(), $qb->getParameters());

        if ($result === false) {
            return null;
        }

        return new PollDetails($result['id'], $result['name'], $result['topic'], $result['description']);
    }


    /**
     * @inheritDoc
     */
    public function fetchAll(?int $limit = null) : array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb
            ->select('*')
            ->from('poll_details');

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        return $this->connection->project($qb->getSQL(), [], static function (array $row) : PollDetails {
            return new PollDetails($row['id'], $row['name'], $row['topic'], $row['description']);
        });
    }


    /**
     * This could be moved under a different interface to force a stronger separation between the things reading
     * the read models i.e. the controllers and the projections that create them.
     */
    public function saveDetails(PollDetails $details) : void
    {
        $this->connection->createQueryBuilder()
            ->insert('poll_details')
            ->values(
                [
                    'id'          => ':id',
                    'name'        => ':name',
                    'topic'       => ':topic',
                    'description' => ':description',
                ]
            )
            ->setParameter('id', $details->id())
            ->setParameter('name', $details->name())
            ->setParameter('topic', $details->topic())
            ->setParameter('description', $details->description())
            ->execute();
    }
}
