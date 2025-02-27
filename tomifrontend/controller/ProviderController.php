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
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
        $provider_id = (int)$_POST['id'];
        $provider = $providerModel->getProviderById($provider_id);
        if ($provider) {
            echo '<div class="provider-details">';
            echo '<h4 class="mb-4">' . htmlspecialchars($provider['name']) . '</h4>';
            echo '<div class="detail-row">';
            echo '<p><strong>Leírás:</strong> ' . htmlspecialchars($provider['description']) . '</p>';
            echo '<p><strong>Szolgáltatás:</strong> ' . htmlspecialchars($provider['type']) . '</p>';
            echo '<p><strong>Ár:</strong> ' . htmlspecialchars($provider['price']) . ' Ft</p>';
            echo '<p><strong>Időtartam:</strong> ' . htmlspecialchars($provider['duration']) . ' perc</p>';
            echo '<p><strong>Telefon szám:</strong> ' . htmlspecialchars($provider['phone_number']) . '</p>';
            echo '<p><strong>Nyitvatartás:</strong> ' . htmlspecialchars($provider['working_hours']) . '</p>';
            echo '<p><strong>Cím:</strong> ' . htmlspecialchars($provider['address']) . '</p>';
            echo '</div>';
            echo '</div>';
        } else {
            echo 'Szolgáltató nem található.';
        }
    }
} elseif ($action === 'book') {
    // Validate if selected time is within working hours
    $user_id = $_SESSION['user']['id'] ?? null;
    if (!$user_id) {
        echo json_encode(['status' => 'error', 'message' => 'User ID not found in session.']);
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Log the incoming data
        error_log('Booking request data: ' . print_r($_POST, true));
        
        $date = $_POST['selectedDate'] ?? null;
        $time = $_POST['selectedTime'] ?? null;
        $provider_id = $_POST['provider_id'] ?? null;
        
        // Check if time is within working hours
        $stmt = $db->prepare("SELECT working_hours FROM providers WHERE id = ?");
        $stmt->execute([$provider_id]);
        $provider = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($provider) {
            $workingHours = explode('-', $provider['working_hours']);
            $startTime = strtotime($workingHours[0]);
            $endTime = strtotime($workingHours[1]);
            $selectedTime = strtotime($time);
            
            if ($selectedTime >= $startTime && $selectedTime <= $endTime) {
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
        // Fetch available hours from the database
        $query = "SELECT appointment_date FROM appointments WHERE provider_id = ? AND DATE(appointment_date) = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$provider_id, $date]);
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format the response for FullCalendar
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
    exit();
// Add this new action to handle working hours fetching
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
}

function validateWorkingHours($workingHours) {
    // Regular expression pattern for the working hours format
    $pattern = '/^(Hétfő|Kedd|Szerda|Csütörtök|Péntek|Szombat|Vasárnap)-(Hétfő|Kedd|Szerda|Csütörtök|Péntek|Szombat|Vasárnap)\s([01][0-9]|2[0-3]):[0-5][0-9]-([01][0-9]|2[0-3]):[0-5][0-9]$/';

    if (!preg_match($pattern, $workingHours)) {
        return [
            'isValid' => false,
            'message' => 'Invalid working hours format'
        ];
    }

    // Split the working hours into components
    list($days, $hours) = explode(' ', $workingHours);
    list($startDay, $endDay) = explode('-', $days);
    list($startTime, $endTime) = explode('-', $hours);

    // Define the valid days order
    $validDays = ['Hétfő', 'Kedd', 'Szerda', 'Csütörtök', 'Péntek', 'Szombat', 'Vasárnap'];
    
    // Check if days are in correct order
    $startDayIndex = array_search($startDay, $validDays);
    $endDayIndex = array_search($endDay, $validDays);
    
    if ($startDayIndex > $endDayIndex) {
        return [
            'isValid' => false,
            'message' => 'Start day cannot be after end day'
        ];
    }

    // Compare times
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

// Add this validation to your provider creation/update logic
if ($action === 'create_provider' || $action === 'update_provider') {
    // Validate working hours before proceeding
    $workingHours = $_POST['working_hours'] ?? '';
    
    // Regular expression pattern for the working hours format
    $pattern = '/^(Hétfő|Kedd|Szerda|Csütörtök|Péntek|Szombat|Vasárnap)-(Hétfő|Kedd|Szerda|Csütörtök|Péntek|Szombat|Vasárnap)\s([01][0-9]|2[0-3]):[0-5][0-9]-([01][0-9]|2[0-3]):[0-5][0-9]$/';

    if (!preg_match($pattern, $workingHours)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'A nyitvatartási idő formátuma helytelen. Használja a következő formátumot: Nap-Nap ÓÓ:PP-ÓÓ:PP'
        ]);
        exit;
    }

    // Additional validation for days and times
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

    // Continue with the rest of your provider creation/update logic
    // ...
}