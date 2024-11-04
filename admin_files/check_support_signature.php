<?php
function checkSupportSignature() {
    global $pdo;
    if ($_SESSION['user_role'] === 'support') {
        // Check if user has already signed in this session
        if (!isset($_SESSION['has_signed'])) {
            $stmt = $pdo->prepare("SELECT signature_time FROM user_sessions WHERE session_id = ? AND user_id = ? AND DATE(signature_time) = CURDATE()");
            $stmt->execute([$_SESSION['session_id'], $_SESSION['client_id']]);
            $result = $stmt->fetch();
            
            if ($result && $result['signature_time']) {
                $_SESSION['has_signed'] = true;
            } else {
                header("Location: support_signature.php");
                exit();
            }
        }
    }
}