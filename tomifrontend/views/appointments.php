<?php
require_once '../database.php';
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user']['id'];
$query = "SELECT a.id, a.appointment_date, p.name AS provider_name, p.type AS provider_type FROM appointments a JOIN providers p ON a.provider_id = p.id WHERE a.user_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$userId]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/mainstyle.css">
    <title>Időpontjaim</title>
</head>
<body>
<button id="theme-toggle" class="btn btn-secondary" style="position: fixed; top: 10px; right: 10px;">Light Mode</button>
    <div class="dynamic-navbar">
        <div class="island">
            <input type="text" class="search-input" placeholder="Keresés...">
            <i class="fas fa-search search-icon"></i>

            <a href="landing.php">
                <i class="fa fa-home"></i>
                <span>Főoldal</span>
            </a>

            <a href="mainpage.php">
                <i class="fa fa-compass"></i>
                <span>Felfedezés</span>
            </a>
            
            <a href="#" class="active">
                <i class="fas fa-calendar-alt"></i>
                <span>Időpontjaim</span>
            </a>
            
            <a href="account.php">
                <i class="fa fa-user"></i>
                <span>Fiók</span>
            </a>

        </div>
    </div>

    <div class="container mt-4">
        <div class="appointment-list">
            <?php foreach ($appointments as $appointment): ?>
                <div class="appointment-card">
                    <div class="appointment-header">
                        <i class="fas fa-<?php echo htmlspecialchars($appointment['provider_type']); ?>"></i> <?php echo htmlspecialchars($appointment['provider_name']); ?>
                    </div>
                    <div class="appointment-details">
                        <p>
                            <i class="far fa-calendar-alt"></i>
                            <span class="appointment-date"><?php echo htmlspecialchars(date('Y-m-d', strtotime($appointment['appointment_date']))); ?></span>
                            <i class="far fa-clock"></i>
                            <span class="appointment-time"><?php echo htmlspecialchars(date('H:i', strtotime($appointment['appointment_date']))); ?></span>
                        </p>
                    </div>
                    <button class="delete-button" data-id="<?php echo $appointment['id']; ?>" data-bs-toggle="modal" data-bs-target="#deleteModal"><i class="fas fa-trash-alt"></i> Törlés</button>
                    <button class="rate-button" data-bs-toggle="modal" data-bs-target="#ratingModal"><i class="fas fa-star"></i> Értékelés</button>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="new-appointment-container">
            <a href="mainpage.php" class="new-appointment-button">
                <i class="fas fa-plus-circle"></i> Új időpont foglalása
            </a>
        </div>
    </div>

    <!-- Rating Modal -->
    <div class="modal fade" id="ratingModal" tabindex="-1" aria-labelledby="ratingModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content" id="dataModal">
          <div class="modal-header">
            <h5 class="modal-title" id="ratingModalLabel">Szolgáltatás értékelése</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="rating">
              <input type="radio" name="rating" id="rating-5" value="5"><label for="rating-5"><i class="fas fa-star"></i></label>
              <input type="radio" name="rating" id="rating-4" value="4"><label for="rating-4"><i class="fas fa-star"></i></label>
              <input type="radio" name="rating" id="rating-3" value="3"><label for="rating-3"><i class="fas fa-star"></i></label>
              <input type="radio" name="rating" id="rating-2" value="2"><label for="rating-2"><i class="fas fa-star"></i></label>
              <input type="radio" name="rating" id="rating-1" value="1"><label for="rating-1"><i class="fas fa-star"></i></label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mégse</button>
            <button type="button" class="btn btn-secondary">Mentés</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content" id="dataModal">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteModalLabel">Törlés megerősítése</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Biztosan törölni szeretnéd?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mégse</button>
            <button type="button" class="btn btn-danger btn-delete-confirm">Törlés</button>
          </div>
        </div>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="../js/mainscript.js"></script>
</body>
</html>