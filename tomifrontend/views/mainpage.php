<?php
session_start();
require_once '../models/rating.php';
require_once '../database.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

$ratings = $ratings ?? [];
$user = $_SESSION['user'];
$provider_id = $_GET['provider_id'] ?? null;
$ratings = array_column($ratings, 'average_rating', 'provider_id');
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="../css/mainstyle.css">
    <title>Felfedezés</title>
</head>
<body data-user-id='<?php echo htmlspecialchars($user['id']); ?>'>
<button id="theme-toggle" class="btn btn-secondary" style="position: fixed; top: 10px; right: 10px;">Light Mode</button>
    <div class="dynamic-navbar">
        <div class="island">
            <input type="text" class="search-input" placeholder="Keresés...">
            <i class="fas fa-search search-icon"></i>

            <a href="landing.php">
                <i class="fa fa-home"></i>
                <span>Főoldal</span>
            </a>

            <a href="#" class="active">
                <i class="fa fa-compass"></i>
                <span>Felfedezés</span>
            </a>
            
            <a href="appointments.php">
                <i class="fas fa-calendar-alt"></i>
                <span>Időpontjaim</span>
            </a>
            
            <a href="account.php">
                <i class="fa fa-user"></i>
                <span><?php echo htmlspecialchars($user['name']); ?></span>
            </a>

            <a href="../models/logout.php" class="logout-button">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>

    <div class="filter-section">
        <div class="filter-button">
            <i class="fas fa-hospital filter-icon"></i>
            <span class="filter-text">Egészségügy</span>
        </div>
        <div class="filter-button">
            <i class="fas fa-cut filter-icon"></i>
            <span class="filter-text">Szépségipar</span>
        </div>
        <div class="filter-button">
            <i class="fa fa-bank filter-icon"></i>
            <span class="filter-text">Ügyintézés</span>
        </div>
    </div>

    <?php if (isset($_SESSION['user']) && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] !== 'customer'): ?>
        <div class="add-service-container">
    <button type="button" class="btn btn-primary add-service-btn" data-bs-toggle="modal" data-bs-target="#addServiceModal">
        <i class="fas fa-plus"></i> Új Szolgáltatás Hozzáadása
    </button>
</div>
<?php endif; ?>

    <hr class="divider">

    <div class="container mt-4">
    <div class="row">
        <?php
        // Fetch all providers from database
        $stmt = $db->prepare("SELECT p.*, COALESCE(AVG(r.rating), 0) as average_rating 
                            FROM providers p 
                            LEFT JOIN ratings r ON p.id = r.provider_id 
                            GROUP BY p.id");
        $stmt->execute();
        $providers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($providers as $provider): ?>
            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="card" data-id="<?php echo htmlspecialchars($provider['id']); ?>">
                    <?php if (isset($provider['image_path']) && !empty($provider['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($provider['image_path']); ?>" alt="<?php echo htmlspecialchars($provider['name']); ?>">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/300" alt="Default image">
                    <?php endif; ?>
                    <div class="card-footer">
                        <span><?php echo htmlspecialchars($provider['name']); ?></span>
                        <span class="star">
                            <i class="fa fa-star"></i>
                            <span><?php echo number_format($provider['average_rating'], 1); ?></span>
                        </span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

    <hr class="divider">

    <div class="social-media-section">
    <h2>Kövess minket!</h2>
    <a href="https://facebook.com" target="_blank"><i class="fab fa-facebook"></i></a>
    <a href="https://twitter.com" target="_blank"><i class="fab fa-twitter"></i></a>
    <a href="https://instagram.com" target="_blank"><i class="fab fa-instagram"></i></a>
</div>

<div class="faq-section">
    <h2>Gyakran Ismételt Kérdések</h2>
    <div class="faq">
        <h3>Hogyan foglalhatok időpontot?</h3>
        <p>Az időpontfoglalás egyszerű. Csak válaszd ki a szolgáltatást, majd kattints az "Időpont választás" gombra.</p>
    </div>
    <div class="faq">
        <h3>Hogyan mondhatom le a folgalásomat?</h3>
        <p>A foglalás után, az "Időpontjaim" menüre kattintva mondhatod le foglalásod.</p>
    </div>
    <div class="faq">
        <h3>Szükséges-e előleget fizetni a foglalás megerősítéséhez?</h3>
        <p>Nem, előleget nem kell fizetnie. A szolgáltatás árát csak az igénybevétel után kell kiegyenlíteni.</p>
    </div>
    <div class="faq">
        <h3>Milyen fizetési módok állnak rendelkezésre?</h3>
        <p>A szolgáltatás díját készpénzben, bankkártyával vagy átutalással tudja kiegyenlíteni a helyszínen.</p>
    </div>
</div>

    <!-- Modal -->
    <div class="modal fade" id="dataModal" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dataModalLabel">Szolgáltatás részletei</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Provider details will be inserted here -->
            </div>
            <div class="modal-footer">
                <?php if ($user['role'] !== 'admin'): ?>
                    <button type="button" class="btn btn-secondary" id="book">Időpont választás</button>
                <?php endif; ?>
                <button type="button" class="btn btn-success" id="bookAppointment" style="display: none;">Foglalás</button>
            </div>
        </div>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="../js/ratings.js"></script>
    <script src="../js/booking.js"></script>
    <script src="../js/account.js"></script>
    <script src="../js/theme.js"></script>
    <script src="../js/appointments.js"></script>
    <script src="../js/alert.js"></script>
    <script src="../js/service.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>

    <script>
    window.userId = <?php echo json_encode($user['id'] ?? null); ?>;
    window.providerId = <?php echo json_encode($provider_id ?? null); ?>;
</script>

<?php if (isset($_SESSION['user']) && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] !== 'customer'): ?>
<!-- Add Service Modal -->
<div class="modal fade" id="addServiceModal" tabindex="-1" aria-labelledby="addServiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addServiceModalLabel">Új szolgáltatás hozzáadása</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addServiceForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="serviceType" class="form-label">Szolgáltatás típusa</label>
                        <select class="form-control" id="serviceType" name="type" required>
                            <option value="haircut">Szépség</option>
                            <option value="education">Oktatás</option>
                            <option value="medical">Egészségügy</option>
                            <option value="administrative">Adminisztratív</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="serviceName" class="form-label">Szolgáltatás neve</label>
                        <input type="text" class="form-control" id="serviceName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Leírás</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="workingHours" class="form-label">Nyitvatartás</label>
                        <input type="text" class="form-control" id="working_hours" name="working_hours" required pattern="^(Hétfő|Kedd|Szerda|Csütörtök|Péntek|Szombat|Vasárnap)-(Hétfő|Kedd|Szerda|Csütörtök|Péntek|Szombat|Vasárnap)\s([01][0-9]|2[0-3]):[0-5][0-9]-([01][0-9]|2[0-3]):[0-5][0-9]$"placeholder="Hétfő-Péntek 09:00-17:00">
                        <small class="form-text text-muted">Formátum: Nap-Nap ÓÓ:PP-ÓÓ:PP (például: Hétfő-Vasárnap 09:00-17:00)</small>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Cím</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Ár (HUF)</label>
                        <input type="number" class="form-control" id="price" name="price" required>
                    </div>
                    <div class="mb-3">
                        <label for="duration" class="form-label">Időtartam (perc)</label>
                        <input type="number" class="form-control" id="duration" name="duration" required>
                    </div>
                    <div class="mb-3">
                        <label for="serviceImage" class="form-label">Szolgáltatás kép</label>
                        <input type="file" class="form-control" id="serviceImage" name="image" accept="image/*" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Töröl</button>
                <button type="button" class="btn btn-primary" id="saveService">Mentés</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
</body>
</html>