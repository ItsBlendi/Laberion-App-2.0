#!/usr/bin/env python3
"""
Capture Face from Camera
Captures face image from webcam for registration
"""

import sys
import json
import os
import cv2
import face_recognition
from pathlib import Path

# Configuration
FACES_DIR = os.path.join(os.path.dirname(__file__), '../assets/uploads/faces')
CASCADE_PATH = os.path.join(os.path.dirname(__file__), 'models/haarcascade_frontalface_default.xml')

def ensure_faces_dir():
    """Ensure faces directory exists"""
    if not os.path.exists(FACES_DIR):
        os.makedirs(FACES_DIR, exist_ok=True)

def capture_face(worker_id, output_path=None):
    """Capture face from camera"""
    try:
        ensure_faces_dir()
        
        # Initialize camera
        cap = cv2.VideoCapture(0)
        
        if not cap.isOpened():
            return {'success': False, 'message': 'Camera not available'}
        
        # Load cascade classifier
        if not os.path.exists(CASCADE_PATH):
            return {'success': False, 'message': 'Cascade classifier not found'}
        
        face_cascade = cv2.CascadeClassifier(CASCADE_PATH)
        
        captured_faces = []
        frame_count = 0
        max_frames = 100
        
        while len(captured_faces) < 5 and frame_count < max_frames:
            ret, frame = cap.read()
            
            if not ret:
                break
            
            frame_count += 1
            gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
            
            # Detect faces
            faces = face_cascade.detectMultiScale(gray, 1.3, 5)
            
            if len(faces) > 0:
                # Get the largest face
                largest_face = max(faces, key=lambda f: f[2] * f[3])
                x, y, w, h = largest_face
                
                # Extract face region
                face_roi = frame[y:y+h, x:x+w]
                
                # Verify it's a valid face
                face_encodings = face_recognition.face_encodings(face_roi)
                
                if len(face_encodings) > 0:
                    captured_faces.append(face_roi)
        
        cap.release()
        
        if len(captured_faces) == 0:
            return {'success': False, 'message': 'No faces captured'}
        
        # Save the best face (middle one)
        best_face = captured_faces[len(captured_faces) // 2]
        
        if output_path is None:
            output_path = os.path.join(FACES_DIR, f'face_{worker_id}_{int(time.time())}.jpg')
        
        cv2.imwrite(output_path, best_face)
        
        return {
            'success': True,
            'message': 'Face captured successfully',
            'output_path': output_path,
            'faces_captured': len(captured_faces)
        }
    
    except Exception as e:
        return {'success': False, 'message': f'Error capturing face: {str(e)}'}

def main():
    """Main function"""
    if len(sys.argv) < 2:
        print(json.dumps({'success': False, 'message': 'Worker ID required'}))
        sys.exit(1)
    
    worker_id = sys.argv[1]
    result = capture_face(worker_id)
    print(json.dumps(result))

if __name__ == '__main__':
    main()