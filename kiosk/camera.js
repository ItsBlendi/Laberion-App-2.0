let video = null;
let canvas = null;
let stream = null;
let isProcessing = false;
const CAPTURE_INTERVAL = 2000; // Capture every 2 seconds

async function initializeCamera() {
    video = document.getElementById('videoFeed');
    canvas = document.getElementById('canvasCapture');
    
    try {
        // Request camera access
        stream = await navigator.mediaDevices.getUserMedia({
            video: {
                width: { ideal: 1280 },
                height: { ideal: 720 },
                facingMode: 'user'
            },
            audio: false
        });
        
        video.srcObject = stream;
        
        // Wait for video to load
        video.onloadedmetadata = function() {
            video.play();
            updateStatus('Camera ready. Scanning for faces...', 'info');
            startFaceCapture();
        };
        
    } catch (error) {
        console.error('Camera error:', error);
        updateStatus('Camera access denied. Please allow camera access.', 'error');
        document.getElementById('systemStatus').innerHTML = 
            '<i class="fas fa-circle text-danger"></i> Camera Error';
    }
}

function startFaceCapture() {
    setInterval(function() {
        if (!isProcessing) {
            captureFace();
        }
    }, CAPTURE_INTERVAL);
}

function captureFace() {
    if (!video || !canvas) return;
    
    const ctx = canvas.getContext('2d');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    // Draw video frame to canvas
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    // Convert to blob and send to server
    canvas.toBlob(function(blob) {
        sendFaceToServer(blob);
    }, 'image/jpeg', 0.9);
}

function sendFaceToServer(blob) {
    isProcessing = true;
    
    const formData = new FormData();
    formData.append('face_image', blob, 'face.jpg');
    
    axios.post('../api/check-face.php', formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    })
    .then(response => {
        if (response.data.success) {
            handleFaceMatch(response.data);
        } else {
            updateStatus(response.data.message || 'Face not recognized', 'warning');
            isProcessing = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        updateStatus('Error processing face', 'error');
        isProcessing = false;
    });
}

function handleFaceMatch(data) {
    // Stop camera
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
    }
    
    // Show result
    showResult(data);
    
    // Process attendance
    processAttendance(data.worker_id);
}

function updateStatus(message, type = 'info') {
    const statusElement = document.getElementById('statusMessage');
    statusElement.className = 'status-message status-' + type;
    
    let icon = 'fa-info-circle';
    if (type === 'success') icon = 'fa-check-circle';
    if (type === 'error') icon = 'fa-exclamation-circle';
    if (type === 'warning') icon = 'fa-exclamation-triangle';
    
    statusElement.innerHTML = `<i class="fas ${icon}"></i> ${message}`;
}

function showResult(data) {
    const resultSection = document.getElementById('resultSection');
    const resultIcon = document.getElementById('resultIcon');
    const resultTitle = document.getElementById('resultTitle');
    const resultMessage = document.getElementById('resultMessage');
    const workerName = document.getElementById('workerName');
    const resultTime = document.getElementById('resultTime');
    const resultStatus = document.getElementById('resultStatus');
    
    const now = new Date();
    const timeString = now.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    
    // Determine check-in or check-out
    const isCheckOut = data.action === 'check_out';
    
    resultIcon.innerHTML = '<i class="fas fa-check-circle"></i>';
    resultIcon.className = 'result-icon success';
    
    resultTitle.textContent = isCheckOut ? 'Goodbye' : 'Welcome';
    resultMessage.textContent = isCheckOut ? 'Checked out successfully' : 'Checked in successfully';
    
    workerName.textContent = data.worker_name;
    resultTime.textContent = timeString;
    resultStatus.textContent = isCheckOut ? 'Check Out' : 'Check In';
    
    resultSection.style.display = 'block';
    
    // Auto reset after 5 seconds
    setTimeout(resetKiosk, 5000);
}

function resetKiosk() {
    document.getElementById('resultSection').style.display = 'none';
    updateStatus('Camera ready. Scanning for faces...', 'info');
    
    // Reinitialize camera
    if (!stream || !stream.active) {
        initializeCamera();
    }
}