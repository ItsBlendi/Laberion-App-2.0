<?php
$page_title = "Check-In/Out - Laberion WMS Kiosk";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/kiosk.css">
</head>
<body class="kiosk-body">
    <div class="kiosk-container">
        <!-- Header -->
        <div class="kiosk-header">
            <div class="logo-section">
                <i class="fas fa-building"></i>
                <h1>LABERION</h1>
                <p>Workforce Management System</p>
            </div>
            <div class="time-section">
                <div class="current-time" id="currentTime">00:00:00</div>
                <div class="current-date" id="currentDate">Loading...</div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="kiosk-content">
            <!-- Camera Section -->
            <div class="camera-section">
                <div class="camera-container">
                    <video id="videoFeed" autoplay playsinline></video>
                    <canvas id="canvasCapture" style="display: none;"></canvas>
                    <div class="camera-overlay">
                        <div class="face-detection-box"></div>
                        <p class="camera-instruction">
                            <i class="fas fa-face-smile"></i>
                            Position your face in the frame
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Status Section -->
            <div class="status-section">
                <div id="statusMessage" class="status-message">
                    <i class="fas fa-spinner fa-spin"></i>
                    Initializing camera...
                </div>
            </div>
            
            <!-- Result Section (Hidden by default) -->
            <div id="resultSection" class="result-section" style="display: none;">
                <div class="result-card">
                    <div class="result-icon" id="resultIcon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h2 id="resultTitle">Welcome</h2>
                    <p id="resultMessage">Processing...</p>
                    <div class="result-details">
                        <p><strong>Name:</strong> <span id="workerName">-</span></p>
                        <p><strong>Time:</strong> <span id="resultTime">-</span></p>
                        <p><strong>Status:</strong> <span id="resultStatus">-</span></p>
                    </div>
                    <button class="btn btn-lg btn-primary mt-4" onclick="resetKiosk()">
                        <i class="fas fa-redo"></i> Scan Another Face
                    </button>
                </div>
            </div>
            
            <!-- Instructions -->
            <div class="instructions-section">
                <div class="instruction-card">
                    <h5><i class="fas fa-info-circle"></i> How to Use</h5>
                    <ol>
                        <li>Position your face in the center of the camera frame</li>
                        <li>Make sure your face is clearly visible</li>
                        <li>The system will automatically detect and process your face</li>
                        <li>Wait for confirmation message</li>
                    </ol>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="kiosk-footer">
            <p>&copy; 2024 Laberion WMS. All rights reserved.</p>
            <p id="systemStatus" class="system-status">
                <i class="fas fa-circle text-success"></i> System Online
            </p>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="camera.js"></script>
    <script src="face-capture.js"></script>
    
    <script>
        // Update time and date
        function updateTime() {
            const now = new Date();
            document.getElementById('currentTime').textContent = now.toLocaleTimeString('en-US', {
                hour12: false,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('currentDate').textContent = now.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
        
        updateTime();
        setInterval(updateTime, 1000);
        
        // Initialize on page load
        window.addEventListener('load', function() {
            initializeCamera();
        });
    </script>
</body>
</html>