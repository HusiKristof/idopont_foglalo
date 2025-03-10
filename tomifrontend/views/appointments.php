<?php
require_once '../database.php';
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user = $_SESSION['user'];
$userId = $user['id'];

// Feltételezve, hogy van egy adatbázis kapcsolat $db
$appointmentId = $_GET['appointment_id'] ?? null;

if ($appointmentId) {
    $stmt = $db->prepare("SELECT * FROM appointments WHERE id = ?");
    $stmt->execute([$appointmentId]);
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
} 

// If user is admin
if ($user['role'] === 'admin') {
    $query = "SELECT a.id, a.appointment_date, u.name AS user_name, 
             p.name AS provider_name, p.type AS provider_type, 
             a.status, a.provider_id 
             FROM appointments a 
             JOIN users u ON a.user_id = u.id 
             JOIN providers p ON a.provider_id = p.id 
             WHERE p.user_id = ?";
} else {
    // If user is not admin
    $query = "SELECT a.id, a.appointment_date, a.status, 
             p.name AS provider_name, p.type AS provider_type,
             a.provider_id
             FROM appointments a 
             JOIN providers p ON a.provider_id = p.id 
             WHERE a.user_id = ?";
}

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/mainstyle.css">
    <link rel="stylesheet" href="../css/darkmode.css">
    <title>Időpontjaim</title>
</head>
<body>

<!-- A belebegő doboz -->
<div id="floating-box" class="floating-box">
    <button id="close-btn" class="close-btn"><i class="fa-solid fa-xmark"></i></button>
    <input type="checkbox" id="darkmode-toggle" class="darkmode-toggle-input"/>
    <label for="darkmode-toggle" class="darkmode-toggle-label"></label>
</div>

<!-- A visszahozó nyíl -->
<div id="toggle-arrow" class="toggle-arrow"><i class="fa-solid fa-arrow-left"></i></div>


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
                <span><?php echo htmlspecialchars($user['name']); ?></span>
            </a>
        </div>
    </div>

    <div class="container mt-4">
        <div class="appointment-list">
            <?php foreach ($appointments as $appointment): ?>
                <div class="appointment-card">
                    <div class="appointment-header">
                        <i class="fas fa-<?php echo htmlspecialchars($appointment['provider_type']); ?>"></i> 
                        <span class="provider-name"><?php echo htmlspecialchars($appointment['provider_name']); ?></span>
                        <span class="appointment-status">
                            <?php 
                            $status = isset($appointment['status']) ? htmlspecialchars($appointment['status']) : 'N/A';
                            switch ($status) {
                                case 'confirmed':
                                    echo 'Elfogadva';
                                    break;
                                case 'pending':
                                    echo 'Megerősítésre vár';
                                    break;
                                case 'canceled':
                                    echo 'Elutasítva';
                                    break;
                                default:
                                    echo $status;
                                    break;
                            }
                            ?>
                        </span>
                    </div>
                    <div class="appointment-details">
                        <p>
                            <i class="far fa-calendar-alt"></i>
                            <span class="appointment-date"><?php echo htmlspecialchars(date('Y-m-d', strtotime($appointment['appointment_date']))); ?></span>
                            <i class="far fa-clock"></i>
                            <span class="appointment-time"><?php echo htmlspecialchars(date('H:i', strtotime($appointment['appointment_date']))); ?></span>
                        </p>
                        <?php if ($user['role'] === 'admin'): ?>
                            <p>
                                <i class="fas fa-user"></i>
                                <span class="user-name"><?php echo htmlspecialchars($appointment['user_name']); ?></span>
                            </p>
                        <?php endif; ?>
                    </div>
                    <?php if ($user['role'] === 'admin'): ?>
                      <button class="confirm-button" data-id="<?php echo $appointment['id']; ?>"><i class="fas fa-check"></i> Elfogadás</button>
                      <button class="reject-button" data-id="<?php echo $appointment['id']; ?>"><i class="fas fa-times"></i> Elutasítás</button>
                    <?php endif; ?>
                    <button class="delete-button" data-id="<?php echo $appointment['id']; ?>" data-bs-toggle="modal" data-bs-target="#deleteModal"><i class="fas fa-trash-alt"></i> Törlés</button>
                    <?php if ($user['role'] !== 'admin'): ?>
                        <button type="button" 
                                class="btn btn-secondary rate-button" 
                                data-appointment-id="<?php echo htmlspecialchars($appointment['id']); ?>"
                                data-provider-id="<?php echo htmlspecialchars($appointment['provider_id']); ?>"
                                data-bs-toggle="modal" 
                                data-bs-target="#ratingModal">
                            Értékelés
                        </button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($user['role'] !== 'admin'): ?>
            <div class="new-appointment-container">
                <a href="mainpage.php" class="new-appointment-button">
                    <i class="fas fa-plus-circle"></i> Új időpont foglalása
                </a>
            </div>
        <?php endif; ?>
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

<!-- Rating Modal -->
<div class="modal fade" id="ratingModal" tabindex="-1" aria-labelledby="ratingModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" id="dataModal">
      <div class="modal-header">
        <h5 class="modal-title" id="ratingModalLabel">Szolgáltatás értékelése</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="appointment-id" value="">
        <input type="hidden" id="provider-id" value="">
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
        <button type="button" class="btn btn-secondary" id="save-rating">Mentés</button>
      </div>
    </div>
  </div>
</div>

    <script>
        window.userId = <?php echo json_encode($user['id'] ?? null); ?>;
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/ratings.js"></script>
    <script src="../js/booking.js"></script>
    <script src="../js/account.js"></script>
    <script src="../js/theme.js"></script>
    <script src="../js/alert.js"></script>
    <script src="../js/adminAppointment.js"></script>
    <script src="../js/appointments.js"></script>
    <script src="../js/service.js"></script>
</body>
</html>