<?php
require_once '../models/Provider.php';
require_once '../database.php';
session_start();

if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to make a booking.']);
    exit();
}

$action = $_GET['action'] ?? '';
$providerModel = new Provider($db);

if ($action === 'fetch') {
    header('Content-Type: application/json');
    $id = $_POST['id'];
    $provider = $providerModel->getProviderById($id);
    echo json_encode($provider);
    exit;
} elseif ($action === 'book') {
    $user_id = $_SESSION['user']['id'] ?? null;
    if (!$user_id) {
        echo json_encode(['status' => 'error', 'message' => 'User ID not found in session.']);
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $date = $_POST['selectedDate'] ?? null;
        $time = $_POST['selectedTime'] ?? null;
        $provider_id = $_POST['provider_id'] ?? null;
        
        //provider working hours csekkolása
        $stmt = $db->prepare("SELECT working_hours FROM providers WHERE id = ?");
        $stmt->execute([$provider_id]);
        $provider = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($provider) {
            //working hours parsolása
            $workingHours = $provider['working_hours'];
            list($days, $hours) = explode(' ', $workingHours);
            list($startDay, $endDay) = explode('-', $days);
            list($startTime, $endTime) = explode('-', $hours);

            //napok és időpontok ellenőrzése
            $selectedDateTime = new DateTime($date);
            $selectedDayName = $selectedDateTime->format('l');
            $dayMapping = [
                'Monday' => 'Hétfő',
                'Tuesday' => 'Kedd',
                'Wednesday' => 'Szerda',
                'Thursday' => 'Csütörtök',
                'Friday' => 'Péntek',
                'Saturday' => 'Szombat',
                'Sunday' => 'Vasárnap'
            ];
            $selectedDay = $dayMapping[$selectedDayName];

            //kiválasztott nap és időpont ellenőrzése
            $validDays = ['Hétfő', 'Kedd', 'Szerda', 'Csütörtök', 'Péntek', 'Szombat', 'Vasárnap'];
            $startDayIndex = array_search($startDay, $validDays);
            $endDayIndex = array_search($endDay, $validDays);
            $selectedDayIndex = array_search($selectedDay, $validDays);

            $isValidDay = $selectedDayIndex >= $startDayIndex && $selectedDayIndex <= $endDayIndex;

            //idő ellenőrzése a munkanapon belül
            $selectedTime = strtotime($time);
            $workingStartTime = strtotime($startTime);
            $workingEndTime = strtotime($endTime);

            if ($isValidDay && $selectedTime >= $workingStartTime && $selectedTime <= $workingEndTime) {
                $appointment_datetime = $date . ' ' . $time;
                $stmt = $db->prepare("INSERT INTO appointments (user_id, provider_id, appointment_date, status) VALUES (?, ?, ?, 'pending')");
                if ($stmt->execute([$user_id, $provider_id, $appointment_datetime])) {
                    echo json_encode(['status' => 'success']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to create appointment']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Selected time is outside working hours']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Provider not found']);
        }
    }
} elseif ($action === 'fetchHours') {
    $date = $_POST['date'] ?? null;
    $provider_id = $_POST['provider_id'] ?? null;

    if ($date && $provider_id) {
        //elérhető időpontok lekérdezése
        $query = "SELECT appointment_date FROM appointments WHERE provider_id = ? AND DATE(appointment_date) = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$provider_id, $date]);
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //válasz formatálása
        $events = [];
        foreach ($appointments as $appointment) {
            $events[] = [
                'title' => 'Booked',
                'start' => $appointment['appointment_date'],
                'end' => $appointment['appointment_date'],
                'allDay' => false
            ];
        }

        echo json_encode($events);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    }
    exit;
} elseif ($action === 'getWorkingHours') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['provider_id'])) {
        $provider_id = (int)$_POST['provider_id'];
        $stmt = $db->prepare("SELECT working_hours FROM providers WHERE id = ?");
        $stmt->execute([$provider_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            echo json_encode($result);
        } else {
            echo json_encode(['error' => 'Provider not found']);
        }
    }
} elseif ($action === 'getBookedAppointments') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $date = $_POST['date'];
        $provider_id = $_POST['provider_id'];
        
        $stmt = $db->prepare("SELECT appointment_date FROM appointments 
                             WHERE provider_id = ? 
                             AND DATE(appointment_date) = ?
                             AND status != 'canceled'");
        $stmt->execute([$provider_id, $date]);
        $bookedSlots = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($bookedSlots);
        exit;
    }
} elseif ($_GET['action'] === 'search' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $query = trim($_POST['query'] ?? '');
    $stmt = $db->prepare(
        "SELECT p.*, COALESCE(AVG(r.rating), 0) as average_rating
         FROM providers p
         LEFT JOIN ratings r ON p.id = r.provider_id
         WHERE p.name LIKE :q OR p.type LIKE :q OR p.description LIKE :q
         GROUP BY p.id
         ORDER BY p.id DESC"
    );
    $like = '%' . $query . '%';
    $stmt->bindValue(':q', $like, PDO::PARAM_STR);
    $stmt->execute();
    $providers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode([
        'status' => 'success',
        'providers' => $providers
    ]);
    exit;
}

function validateWorkingHours($workingHours) {
    //working hour format
    $pattern = '/^(Hétfő|Kedd|Szerda|Csütörtök|Péntek|Szombat|Vasárnap)-(Hétfő|Kedd|Szerda|Csütörtök|Péntek|Szombat|Vasárnap)\s([01][0-9]|2[0-3]):[0-5][0-9]-([01][0-9]|2[0-3]):[0-5][0-9]$/';

    if (!preg_match($pattern, $workingHours)) {
        return [
            'isValid' => false,
            'message' => 'Invalid working hours format'
        ];
    }

    //working hour splitelése
    list($days, $hours) = explode(' ', $workingHours);
    list($startDay, $endDay) = explode('-', $days);
    list($startTime, $endTime) = explode('-', $hours);

    $validDays = ['Hétfő', 'Kedd', 'Szerda', 'Csütörtök', 'Péntek', 'Szombat', 'Vasárnap'];
    
    //sorbarendelés
    $startDayIndex = array_search($startDay, $validDays);
    $endDayIndex = array_search($endDay, $validDays);
    
    if ($startDayIndex > $endDayIndex) {
        return [
            'isValid' => false,
            'message' => 'Start day cannot be after end day'
        ];
    }

    //idők egymáshoz viszonyítása
    list($startHour, $startMinute) = explode(':', $startTime);
    list($endHour, $endMinute) = explode(':', $endTime);
    
    $startMinutes = $startHour * 60 + $startMinute;
    $endMinutes = $endHour * 60 + $endMinute;
    
    if ($startMinutes >= $endMinutes) {
        return [
            'isValid' => false,
            'message' => 'Opening time cannot be later than or equal to closing time'
        ];
    }

    return [
        'isValid' => true,
        'message' => 'Working hours format is valid'
    ];
}

if ($action === 'create_provider' || $action === 'update_provider') {
    //working hours validálása mielött továbbmegyünk
    $workingHours = $_POST['working_hours'] ?? '';
    
    $pattern = '/^(Hétfő|Kedd|Szerda|Csütörtök|Péntek|Szombat|Vasárnap)-(Hétfő|Kedd|Szerda|Csütörtök|Péntek|Szombat|Vasárnap)\s([01][0-9]|2[0-3]):[0-5][0-9]-([01][0-9]|2[0-3]):[0-5][0-9]$/';

    if (!preg_match($pattern, $workingHours)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'A nyitvatartási idő formátuma helytelen. Használja a következő formátumot: Nap-Nap ÓÓ:PP-ÓÓ:PP'
        ]);
        exit;
    }

    //mégtöbb validálás
    list($days, $hours) = explode(' ', $workingHours);
    list($startDay, $endDay) = explode('-', $days);
    list($startTime, $endTime) = explode('-', $hours);

    $validDays = ['Hétfő', 'Kedd', 'Szerda', 'Csütörtök', 'Péntek', 'Szombat', 'Vasárnap'];
    $startDayIndex = array_search($startDay, $validDays);
    $endDayIndex = array_search($endDay, $validDays);

    if ($startDayIndex === false || $endDayIndex === false) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Érvénytelen nap megadva'
        ]);
        exit;
    }

    if ($startDayIndex > $endDayIndex) {
        echo json_encode([
            'status' => 'error',
            'message' => 'A kezdő nap nem lehet később, mint a záró nap'
        ]);
        exit;
    }
}

if ($_GET['action'] === 'filter_providers') {
    $type = $_POST['type'];
    
    //lekérdezés a szolgáltatók adatbázisából a kategória alapján
    $stmt = $db->prepare("SELECT p.*, COALESCE(AVG(r.rating), 0) as average_rating 
                        FROM providers p 
                        LEFT JOIN ratings r ON p.id = r.provider_id 
                        WHERE p.type = :type 
                        GROUP BY p.id");
    $stmt->bindParam(':type', $type);
    $stmt->execute();
    $providers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'status' => 'success',
        'providers' => $providers
    ]);
    exit;
}