<?php
// Simple test file to check if PHP works
header('Content-Type: application/json');
echo json_encode(['ok' => true, 'message' => 'PHP works!', 'session' => session_status()]);

