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

// Pagination setup
$perPage = 9;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

// Get total count for pagination
$totalStmt = $db->query("SELECT COUNT(*) FROM providers");
$totalProviders = $totalStmt->fetchColumn();
$totalPages = ceil($totalProviders / $perPage);

// Fetch paginated providers
$stmt = $db->prepare("SELECT p.*, COALESCE(AVG(r.rating), 0) as average_rating 
    FROM providers p 
    LEFT JOIN ratings r ON p.id = r.provider_id 
    GROUP BY p.id
    ORDER BY p.id DESC
    LIMIT :offset, :perPage");
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
$stmt->execute();
$providers = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <link rel="stylesheet" href="../css/darkmode.css">
    <link rel="shortcut icon" href="logo.jpg">
    <title>Felfedezés</title>
</head>
<body data-user-id='<?php echo htmlspecialchars($user['id']); ?>'>

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
            <input type="text" class="search-input" id="provider-search" placeholder="Keresés...">
            <i class="fas fa-search search-icon" id="provider-search-icon"></i>

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
        </div>
    </div>

    <div class="filter-section">
    <div class="filter-button" data-type="Egészségügy">
        <i class="fas fa-hospital filter-icon"></i>
        <span class="filter-text">Egészségügy</span>
    </div>
    <div class="filter-button" data-type="Szépségipar">
        <i class="fas fa-cut filter-icon"></i>
        <span class="filter-text">Szépségipar</span>
    </div>
    <div class="filter-button" data-type="Oktatás">
        <i class="fas fa-graduation-cap filter-icon"></i>
        <span class="filter-text">Oktatás</span>
    </div>
    <div class="filter-button" data-type="Adminisztratív">
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
        <div class="row" id="provider-list">
        <?php
        foreach ($providers as $provider): ?>
            <div class="col-lg-4 col-md-6 col-sm-12 mb-4 provider-item" data-category="<?php echo htmlspecialchars($provider['type']); ?>">
                <div class="card" data-id="<?php echo htmlspecialchars($provider['id']); ?>">
                    <?php if (isset($provider['image_path']) && !empty($provider['image_path'])): ?>
                        <img class="lazy" data-src="<?php echo htmlspecialchars($provider['image_path']); ?>" alt="<?php echo htmlspecialchars($provider['name']); ?>">
                    <?php else: ?>
                        <img class="lazy" data-src="https://via.placeholder.com/300" alt="Default image">
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

    <!-- Pagination controls -->
    <nav>
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item<?php if ($i == $page) echo ' active'; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>



    <div class="container mt-4">
    <div class="row">
        <?php
        foreach ($providers as $provider): ?>
            <div class="col-lg-4 col-md-6 col-sm-12 mb-4 base-providers">
                <div class="card" data-id="<?php echo htmlspecialchars($provider['id']); ?>">
                    <?php if (isset($provider['image_path']) && !empty($provider['image_path'])): ?>
                        <img class="lazy" data-src="<?php echo htmlspecialchars($provider['image_path']); ?>" alt="<?php echo htmlspecialchars($provider['name']); ?>">
                    <?php else: ?>
                        <img class="lazy" data-src="https://via.placeholder.com/300" alt="Default image">
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

<footer>
    <div class="footer-container">
        <div class="footer-section logo">
            <img src="logo.jpg" alt="Bookify logo">
        </div>
        <div class="footer-section">
            <h3>Alapvető információk</h3>
            <ul>
                
                <li>Kapcsolat: bookify@gmail.com</li>
                <li>Tel.: +36 30 284 0264</li>
                <li>Cím: 7632 Pécs, Móra Ferenc utca 95.</li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Hasznos linkek</h3>
            <ul>
                <li><a href="#">Rólunk</a></li>
                <li><a href="#">ügyfélszolgálat</a></li>
                <li><a href="#">GYIK (Gyakran Ismételt Kérdések)</a></li>
                <li><a href="#">Lemondási szabályzat</a></li>
            </ul>
        </div>
        <div class="footer-section social">
            <h4>Kövess minket!</h4>
            <div class="social-icons">
                <a href="https://facebook.com" target="_blank" class="social-icon"><i class="fab fa-facebook"></i></a>
                <a href="https://x.com" target="_blank" class="social-icon"><i class="fab fa-x"></i></a>
                <a href="https://instagram.com" target="_blank" class="social-icon"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>© 2024 Bookify Kft. Minden jog fenntartva.</p>
    </div>
</footer>



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
            <div id="adminServiceActions" class="admin-service-actions" style="display:none;">
                <button type="button" class="btn btn-primary me-2" id="editServiceBtn">Szerkesztés</button>
                <button type="button" class="btn btn-danger" id="deleteServiceBtn">Törlés</button>
            </div>
        </div>
    </div>
</div>

<!-- Add a delete confirmation modal (like appointments) if not present already -->
<div class="modal fade" id="deleteServiceModal" tabindex="-1" aria-labelledby="deleteServiceModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" id="deleteServiceModalContent">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteServiceModalLabel">Szolgáltatás törlése</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Bezár"></button>
      </div>
      <div class="modal-body">
        Biztosan törölni szeretnéd ezt a szolgáltatást?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mégse</button>
        <button type="button" class="btn btn-danger btn-delete-service-confirm">Törlés</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" id="dataModal">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Törlés megerősítése</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Bezár"></button>
      </div>
      <div class="modal-body">
        Biztosan törölni szeretnéd ezt a szolgáltatást?
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
    <script src="../js/ratings.js"></script>
    <script src="../js/booking.js"></script>
    <script src="../js/account.js"></script>
    <script src="../js/theme.js"></script>
    <script src="../js/appointments.js"></script>
    <script src="../js/alert.js"></script>
    <script src="../js/service.js"></script>
    <script src="../js/filter.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.11/jquery.lazy.min.js"></script>
    <script>
        $(function() {
            $('.lazy').Lazy();
        });

        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                const context = this;
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(context, args), wait);
            };
        }

        $(window).on('scroll', debounce(function() {
            console.log('Scroll event handled');
        }, 100));
    </script>

    <script>
    window.userId = <?php echo json_encode($user['id'] ?? null); ?>;
    window.providerId = <?php echo json_encode($provider_id ?? null); ?>;
</script>

<script>
$(function() {
    $('.lazy').Lazy();

    // Re-initialize lazy loading after AJAX filter or pagination
    $(document).on('ajaxComplete', function() {
        $('.lazy').Lazy();
    });

    // Event delegation for dynamically loaded cards
    $('#provider-list').on('click', '.card', function() {
        const providerId = $(this).data('id');
        $('#dataModal').data('provider-id', providerId);

        // Dynamically load FullCalendar scripts only when needed
        if (!window.fullCalendarLoaded) {
            $.when(
                $.getScript('https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'),
                $.getScript('https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js')
            ).done(function() {
                window.fullCalendarLoaded = true;
                // Now show modal and initialize calendar
                $('#dataModal').modal('show');
                // ...initialize calendar here...
            });
        } else {
            $('#dataModal').modal('show');
            // ...initialize calendar here...
        }
    });
});
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
                            <option value="Szépségipar">Szépség</option>
                            <option value="Oktatás">Oktatás</option>
                            <option value="Egészségügy">Egészségügy</option>
                            <option value="Adminisztratív">Adminisztratív</option>
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
                    <div class="mb-3">
                        <label for="servicePhone" class="form-label">Telefonszám</label>
                        <input type="text" class="form-control" id="servicePhone" name="phone_number" placeholder="+36 12-345-6789">
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