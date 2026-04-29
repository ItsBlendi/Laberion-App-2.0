#!/usr/bin/env python3
"""
Train Face Recognition Model
Registers new worker faces and updates encodings
"""

import sys
import json
import os
import pickle
import face_recognition
import numpy as np
from pathlib import Path

# Configuration
MODELS_DIR = os.path.join(os.path.dirname(__file__), 'models')
ENCODINGS_FILE = os.path.join(MODELS_DIR, 'face_encodings.pkl')

def ensure_models_dir():
    """Ensure models directory exists"""
    if not os.path.exists(MODELS_DIR):
        os.makedirs(MODELS_DIR, exist_ok=True)

def load_encodings():
    """Load existing encodings"""
    if not os.path.exists(ENCODINGS_FILE):
        return {}, {}
    
    try:
        with open(ENCODINGS_FILE, 'rb') as f:
            data = pickle.load(f)
            return data.get('encodings', {}), data.get('names', {})
    except Exception as e:
        print(json.dumps({'success': False, 'message': f'Error loading encodings: {str(e)}'}))
        sys.exit(1)

def save_encodings(encodings, names):
    """Save encodings to file"""
    try:
        ensure_models_dir()
        
        data = {
            'encodings': encodings,
            'names': names
        }
        
        with open(ENCODINGS_FILE, 'wb') as f:
            pickle.dump(data, f)
        
        return True
    except Exception as e:
        print(json.dumps({'success': False, 'message': f'Error saving encodings: {str(e)}'}))
        return False

def train_face(worker_id, image_path):
    """Train face for a worker"""
    try:
        # Validate inputs
        if not image_path or not os.path.exists(image_path):
            return {'success': False, 'message': 'Image file not found'}
        
        # Load image
        image = face_recognition.load_image_file(image_path)
        
        # Get face encodings
        face_encodings = face_recognition.face_encodings(image)
        
        if len(face_encodings) == 0:
            return {'success': False, 'message': 'No face detected in image'}
        
        if len(face_encodings) > 1:
            return {'success': False, 'message': 'Multiple faces detected'}
        
        # Get encoding
        face_encoding = face_encodings[0]
        
        # Load existing encodings
        encodings, names = load_encodings()
        
        # Update or add encoding
        encodings[str(worker_id)] = face_encoding
        names[str(worker_id)] = f'Worker {worker_id}'
        
        # Save encodings
        if save_encodings(encodings, names):
            return {
                'success': True,
                'message': 'Face trained successfully',
                'worker_id': worker_id,
                'encoding': str(face_encoding.tolist())[:50] + '...'  # Return partial encoding for verification
            }
        else:
            return {'success': False, 'message': 'Error saving encodings'}
    
    except Exception as e:
        return {'success': False, 'message': f'Error training face: {str(e)}'}

def main():
    """Main function"""
    if len(sys.argv) < 3:
        print(json.dumps({'success': False, 'message': 'Missing arguments: worker_id and image_path required'}))
        sys.exit(1)
    
    worker_id = sys.argv[1]
    image_path = sys.argv[2]
    
    result = train_face(worker_id, image_path)
    print(json.dumps(result))

if __name__ == '__main__':
    main()