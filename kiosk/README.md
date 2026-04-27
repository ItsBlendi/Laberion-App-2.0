# Laberion WMS - Kiosk Application

## Overview
The Kiosk application is a tablet-based interface for employee check-in and check-out using face recognition technology.

## Features
- Real-time camera feed
- Automatic face detection and recognition
- Instant check-in/check-out
- Visual feedback and confirmation
- Responsive design for tablets
- System status monitoring

## Hardware Requirements
- Tablet or touchscreen device (iPad, Android tablet, or similar)
- Built-in or external camera
- Internet connection
- Minimum 1280x720 resolution

## Browser Compatibility
- Chrome/Chromium (recommended)
- Safari (iOS)
- Firefox
- Edge

## Installation

1. Place kiosk folder in your web server root
2. Ensure camera permissions are enabled
3. Configure Python face recognition service
4. Access via: `http://your-server/laberion/kiosk/`

## Configuration

### Camera Settings
Edit `camera.js` to adjust:
- Capture interval (default: 2 seconds)
- Video resolution
- Face detection sensitivity

### Face Recognition
Configure in `../python/face_recognition.py`:
- Confidence threshold
- Model path
- Processing timeout

## Usage

1. Employee approaches kiosk
2. Face is automatically detected and captured
3. System compares with registered faces
4. Automatic check-in or check-out
5. Confirmation message displayed
6. System resets for next employee

## Troubleshooting

### Camera Not Working
- Check browser permissions
- Verify camera hardware
- Clear browser cache
- Try different browser

### Face Not Recognized
- Ensure good lighting
- Position face in center
- Check face registration
- Verify Python service running

### Slow Performance
- Check internet connection
- Reduce video resolution
- Clear browser cache
- Restart application

## Security Notes
- Kiosk should be in secure location
- Regular face data backups
- Monitor access logs
- Update system regularly

## Support
Contact: admin@laberion.com