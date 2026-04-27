function processAttendance(workerId) {
    const now = new Date();
    const currentTime = now.toTimeString().split(' ')[0]; // HH:MM:SS
    const currentDate = now.toISOString().split('T')[0]; // YYYY-MM-DD
    
    // Determine if it's check-in or check-out
    const hour = now.getHours();
    const isCheckOut = hour >= 16; // After 4 PM is check-out
    
    axios.post('../api/save-attendance.php', {
        worker_id: workerId,
        date: currentDate,
        time: currentTime,
        action: isCheckOut ? 'check_out' : 'check_in'
    })
    .then(response => {
        if (response.data.success) {
            console.log('Attendance saved:', response.data);
        } else {
            console.error('Error saving attendance:', response.data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Alternative: Manual face capture for registration
function captureAndRegisterFace(workerId) {
    const video = document.getElementById('videoFeed');
    const canvas = document.getElementById('canvasCapture');
    
    if (!video || !canvas) return;
    
    const ctx = canvas.getContext('2d');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    canvas.toBlob(function(blob) {
        const formData = new FormData();
        formData.append('face_image', blob, 'face.jpg');
        formData.append('worker_id', workerId);
        
        axios.post('../api/upload-face.php', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
        .then(response => {
            if (response.data.success) {
                alert('Face registered successfully!');
            } else {
                alert('Error registering face: ' + response.data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error uploading face');
        });
    }, 'image/jpeg', 0.9);
}