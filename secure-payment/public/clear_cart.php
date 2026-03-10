<?php
session_start();
$_SESSION['cart'] = [];
http_response_code(200);
echo json_encode(['cleared' => true]);
