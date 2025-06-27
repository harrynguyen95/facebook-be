<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['base64String'])) {
        $base64String = $_POST['base64String'];

        // Tách header nếu có
        if (strpos($base64String, 'base64,') !== false) {
            $base64String = explode('base64,', $base64String)[1];
        }

        // Giải mã base64 và lưu thành file ảnh tạm thời
        $imageData = base64_decode($base64String);
        $tempImagePath = tempnam(sys_get_temp_dir(), 'qr_') . '.png';
        file_put_contents($tempImagePath, $imageData);

        // Gửi request đến qrserver
        $curl = curl_init();
        $cfile = new CURLFile($tempImagePath, 'image/png', 'qr.png');

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.qrserver.com/v1/read-qr-code/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => ['file' => $cfile],
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        // Xóa file tạm
        unlink($tempImagePath);

        if ($error) {
            echo json_encode(['error' => 'CURL Error: ' . $error]);
        } else {
            echo $response; // JSON trả về từ qrserver
        }
    } else {
        echo json_encode(['error' => 'Missing base64String']);
    }
} else {
    echo json_encode(['error' => 'Must be POST.']);
}
?>
