<?php

require_once __DIR__ . '/config.php';

// --- CSRF Koruması ---

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

function verify_csrf_token() {
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        http_response_code(403);
        die('Geçersiz istek. Lütfen sayfayı yenileyip tekrar deneyin.');
    }
}

// --- Girdi Doğrulama ---

function sanitize_input($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validate_date($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

function validate_date_range($check_in, $check_out) {
    if (!validate_date($check_in) || !validate_date($check_out)) {
        return false;
    }
    $today = new DateTime('today');
    $in = new DateTime($check_in);
    $out = new DateTime($check_out);
    return $in >= $today && $out > $in;
}

function validate_phone($phone) {
    return preg_match('/^[\+]?[0-9\s\-\(\)]{7,20}$/', $phone);
}

// --- Güvenlik Başlıkları ---

function set_security_headers() {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

// --- Oda Müsaitlik Kontrolü ---

function check_room_availability($conn, $room_type, $check_in, $check_out, $exclude_reservation_id = null) {
    // Bu oda tipinde toplam kaç oda var
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM rooms r JOIN room_types rt ON r.room_type_id = rt.id WHERE rt.name_tr = ? AND r.is_available = TRUE");
    $stmt->bind_param("s", $room_type);
    $stmt->execute();
    $total_rooms = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    if ($total_rooms == 0) {
        return false;
    }

    // Bu tarihlerde kaç rezervasyon var
    $sql = "SELECT COUNT(*) as booked FROM reservations WHERE room_type = ? AND status != 'cancelled' AND check_in_date < ? AND check_out_date > ?";
    $params = [$room_type, $check_out, $check_in];
    $types = "sss";

    if ($exclude_reservation_id) {
        $sql .= " AND id != ?";
        $params[] = $exclude_reservation_id;
        $types .= "i";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $booked = $stmt->get_result()->fetch_assoc()['booked'];
    $stmt->close();

    return $booked < $total_rooms;
}

// --- Şablon Yardımcıları ---

function get_header() {
    require_once __DIR__ . '/../includes/header.php';
}

function get_footer() {
    require_once __DIR__ . '/../includes/footer.php';
}
