#!/usr/bin/env python3
"""
Face Recognition Script
Identifies workers by comparing captured face with registered faces
"""

import sys
import json
import os
import pickle
import face_recognition
import numpy as np
from pathlib import Path

# Configuration
FACES_DIR = os.path.join(os.path.dirname(__file__), '../assets/uploads/faces')
MODELS_DIR = os.path.join(os.path.dirname(__file__), 'models')
ENCODINGS_FILE = os.path.join(MODELS_DIR, 'face_encodings.pkl')
CONFIDENCE_THRESHOLD = 0.6

def load_encodings():
    """Load pre-trained face encodings"""
    if not os.path.exists(ENCODINGS_FILE):
        return {}, {}
    
    try:
        with open(ENCODINGS_FILE, 'rb') as f:
            data = pickle.load(f)
            return data.get('encodings', {}), data.get('names', {})
    except Exception as e:
        print(json.dumps({'success': False, 'message': f'Error loading encodings: {str(e)}'}))
        sys.exit(1)

def recognize_face(image_path):
    """Recognize face in image"""
    try:
        # Load image
        if not os.path.exists(image_path):
            return {'success': False, 'message': 'Image file not found'}
        
        image = face_recognition.load_image_file(image_path)
        
        # Get face encodings
        face_encodings = face_recognition.face_encodings(image)
        
        if len(face_encodings) == 0:
            return {'success': False, 'message': 'No face detected in image'}
        
        if len(face_encodings) > 1:
            return {'success': False, 'message': 'Multiple faces detected. Please ensure only one face is in the image'}
        
        # Get the first (and only) face encoding
        captured_encoding = face_encodings[0]
        
        # Load known encodings
        known_encodings, known_names = load_encodings()
        
        if not known_encodings:
            return {'success': False, 'message': 'No registered faces found'}
        
        # Compare with known faces
        best_match_distance = float('inf')
        best_match_id = None
        best_match_name = None
        
        for worker_id, encoding in known_encodings.items():
            # Calculate face distance
            distance = face_recognition.face_distance([encoding], captured_encoding)[0]
            
            if distance < best_match_distance:
                best_match_distance = distance
                best_match_id = worker_id
                best_match_name = known_names.get(worker_id, 'Unknown')
        
        # Check if match is good enough
        if best_match_distance > CONFIDENCE_THRESHOLD:
            return {
                'success': False,
                'message': 'Face not recognized',
                'confidence': float(1 - best_match_distance)
            }
        
        # Calculate confidence (inverse of distance)
        confidence = 1 - best_match_distance
        
        return {
            'success': True,
            'worker_id': int(best_match_id),
            'worker_name': best_match_name,
            'confidence': float(confidence),
            'distance': float(best_match_distance)
        }
    
    except Exception as e:
        return {'success': False, 'message': f'Error processing image: {str(e)}'}

def main():
    """Main function"""
    if len(sys.argv) < 2:
        print(json.dumps({'success': False, 'message': 'No image path provided'}))
        sys.exit(1)
    
    image_path = sys.argv[1]
    result = recognize_face(image_path)
    print(json.dumps(result))

if __name__ == '__main__':
    main()