<?php
namespace App\Repository;

use App\Entity\Habit;
use App\Utils\EntityMapper;
use Mns\Buggy\Core\AbstractRepository;

class HabitRepository extends AbstractRepository
{
    public function findAll()
    {
        $sql = "SELECT * FROM habits";
        $habits = $this->getConnection()->query($sql);
        return EntityMapper::mapCollection(Habit::class, $habits->fetchAll());
    }

    public function find(int $id)
    {
        $sql = "SELECT * FROM habits WHERE id = :id";
        $query = $this->getConnection()->prepare($sql);
        $query->execute(['id' => $id]);
        return EntityMapper::map(Habit::class, $query->fetch());
    }

    public function findByUser(int $userId)
    {
        $sql = "SELECT * FROM habits WHERE user_id = :user_id";
        $query = $this->getConnection()->prepare($sql);
        $query->execute(['user_id' => $userId]);
        return EntityMapper::mapCollection(Habit::class, $query->fetchAll());
    }

     /**
     * Compte le nombre d'habitudes actives pour un utilisateur
     */
    public function countByUser(int $userId): int
    {
        $sql = "SELECT COUNT(*) as total FROM habits WHERE user_id = :user_id";
        $query = $this->getConnection()->prepare($sql);
        $query->execute(['user_id' => $userId]);
        $row = $query->fetch();
        return (int)($row['total'] ?? 0);
    }

    public function insert(array $data = array())
    {
        $name = $data['name'];
        $description = $data['description'];

        $sql = "INSERT INTO habits (user_id, name, description, created_at) VALUES (:user_id, :name, :description, NOW())";

        $query = $this->getConnection()->prepare($sql);
        $query->execute(['user_id' => $data['user_id'], 'name' => $name, 'description' => $description]);

        return $this->getConnection()->lastInsertId();
    }

    /**
     * Calcule le nombre de jours consécutifs où l'utilisateur a complété au moins une habitude
     */
    public function getStreak(int $userId): int
    {
        $sql = "
            SELECT DISTINCT log_date
            FROM habit_logs hl
            JOIN habits h ON hl.habit_id = h.id
            WHERE h.user_id = :user_id AND hl.status = 1
            ORDER BY log_date DESC
        ";

        $query = $this->getConnection()->prepare($sql);
        $query->execute(['user_id' => $userId]);
        $dates = $query->fetchAll(\PDO::FETCH_COLUMN);

        $streak = 0;
        $today = new \DateTime();
        foreach ($dates as $dateStr) {
            $date = new \DateTime($dateStr);
            if ($date->format('Y-m-d') === $today->format('Y-m-d')) {
                $streak++;
                $today->modify('-1 day');
            } elseif ($date->format('Y-m-d') === $today->format('Y-m-d')) {
                // continue streak
                $streak++;
                $today->modify('-1 day');
            } else {
                break;
            }
        }

        return $streak;
    }

}
