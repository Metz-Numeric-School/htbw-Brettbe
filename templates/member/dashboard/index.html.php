<?php $layout = 'base.html.php'; ?>

<div class="container py-5">
    <h1 class="mb-4">Bonjour <?= htmlspecialchars($_SESSION['user']['username']) ?> !</h1>

    <div class="row mb-4">
        <!-- Statistiques rapides -->
        <div class="col-md-4 mb-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Habitudes actives</h5>
                    <p class="card-text display-4"><?= $stats['active_habits'] ?? 0 ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Jours consécutifs</h5>
                    <p class="card-text display-4"><?= $stats['streak_days'] ?? 0 ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Habitudes complétées aujourd'hui</h5>
                    <p class="card-text display-4"><?= $stats['completed_today'] ?? 0 ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des habitudes -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Mes habitudes</h2>
                <a href="/habits/create" class="btn btn-primary">Ajouter une habitude</a>
            </div>
        </div>
    </div>
</div>