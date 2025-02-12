<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php'); // Redirect to login if not logged in
    exit();
}

// Get user information from the session
$user = $_SESSION['user'];
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

    <hr class="divider">

    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="card" data-id="1">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQUC0xWf3D5qq6AvXcyymZePQ9dYbsagNc7ZQ&s" alt="pacek">
                    <div class="card-footer">
                        <span>pacekbarber</span>
                        <span class="star">
                            <i class="fa fa-star"></i> 4.8
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="card" data-id="2">
                    <img src="https://i.scdn.co/image/ab67616d0000b273bdee5a05e95ec7372a1a36b1" alt="igne">
                    <div class="card-footer">
                        <span>igne</span>
                        <span class="star">
                            <i class="fa fa-star"></i> 4.5
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="card" data-id="3">
                    <img src="https://images.euronics.hu/product_images/800x600/resize/9536035389470_azq0bd3u.jpg?v=2" alt ="nemtom">
                    <div class="card-footer">
                        <span>nemtom</span>
                        <span class="star">
                            <i class="fa fa-star"></i> 4.8
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="card" data-id="4">
                    <img src="https://i.redd.it/jbxpe4olasb11.jpg" alt="barberpro">
                    <div class="card-footer">
                        <span>barberpro</span>
                        <span class="star">
                            <i class="fa fa-star"></i> 4.9
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="card" data-id="5">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/63/Model_T_Casino_interior.jpg/1200px-Model_T_Casino_interior.jpg" alt="futyi">
                    <div class="card-footer">
                        <span>husi kristof futyi service</span>
                        <span class="star">
                            <i class="fa fa-star"></i> 6
                        </span>
                    </div>
                </div>
            </div>
        
            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="card" data-id="6">
                    <img src="https://s.24.hu/app/uploads/2016/08/bolnici_31-1-1024x584.jpg" alt="sigma">
                    <div class="card-footer">
                        <span>sigma boy orvosi</span>
                        <span class="star">
                            <i class="fa fa-star"></i> 1
                        </span>
                    </div>
                </div>
            </div>
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
                <div class="provider-details">
                    <h5 name="name" id="name"></h5>
                    <p name="description" id="description"></p>
                    <p name="provider" id="provider"></p>
                    <p name="duration" id="duration"></p>
                    <p name="price" id="price"></p>
                </div>
                <div id="calendar"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="book">Időpont választás</button>
                <button type="button" class="btn btn-success" id="bookAppointment" style="display: none;">Foglalás</button>
            </div>
        </div>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="../js/mainscript.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>

    <script>
    window.userId = <?php echo json_encode($user['id'] ?? null); ?>;
    window.providerId = <?php echo json_encode($provider_id ?? null); ?>;
</script>
</body>
</html>