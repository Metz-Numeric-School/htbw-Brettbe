<?php
namespace App\Controller\Admin;

use App\Repository\HabitRepository;
use App\Repository\HabitLogRepository;
use Mns\Buggy\Core\AbstractController;

class HabitsController extends AbstractController
{
    private HabitRepository $habitRepository;
    private HabitLogRepository $habitLogRepository;

    public function __construct()
    {
        $this->habitRepository = new HabitRepository();
        $this->habitLogRepository = new HabitLogRepository();
    }

    /**
     * Liste toutes les habitudes
     */
    public function index()
    {
        $habits = $this->habitRepository->findAll();

        return $this->render('admin/habits/index.html.php', [
            'habits' => $habits,
            'title' => 'Habit Management'
        ]);
    }
}
