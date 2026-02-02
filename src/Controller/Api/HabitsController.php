<?php

namespace App\Controller\Api;

use App\Repository\HabitRepository;
use Mns\Buggy\Core\AbstractController;

class HabitsController extends AbstractController
{
    private HabitRepository $habitRepository;

    public function __construct()
    {
        $this->habitRepository = new HabitRepository();
    }

    public function index()
    {
        $habits = $this->habitRepository->findAll();
        $habitsArray = array_map(function($habit) {
            return [
                'id' => $habit->getId(),
                'user_id' => $habit->getUserId(),
                'name' => $habit->getName(),
                'description' => $habit->getDescription(),
                'created_at' => $habit->getCreatedAt(),
            ];
        }, $habits);
        return $this->json([
            'habits' => $habitsArray
        ]);
    }

}