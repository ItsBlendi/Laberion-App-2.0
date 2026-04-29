"""
Batch Train Faces
Train multiple faces from a directory
"""

import sys
import json
import os
import face_recognition
import pickle
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
    except:
        return {}, {}

def save_encodings(encodings, names):
    """Save encodings to file"""
    ensure_models_dir()
    
    data = {
        'encodings': encodings,
        'names': names
    }
    
    with open(ENCODINGS_FILE, 'wb') as f:
        pickle.dump(data, f)

def batch_train(faces_directory):
    """Train all faces in directory"""
    try:
        if not os.path.exists(faces_directory):
            return {'success': False, 'message': 'Directory not found'}
        
        encodings, names = load_encodings()
        trained_count = 0
        failed_count = 0
        
        # Process each image file
        for filename in os.listdir(faces_directory):
            if filename.lower().endswith(('.jpg', '.jpeg', '.png')):
                filepath = os.path.join(faces_directory, filename)
                
                try:
                    # Extract worker ID from filename (e.g., face_123_timestamp.jpg)
                    parts = filename.split('_')
                    if len(parts) >= 2:
                        worker_id = parts[1]
                    else:
                        worker_id = filename.split('.')[0]
                    
                    # Load and encode face
                    image = face_recognition.load_image_file(filepath)
                    face_encodings = face_recognition.face_encodings(image)
                    
                    if len(face_encodings) > 0:
                        encodings[str(worker_id)] = face_encodings[0]
                        names[str(worker_id)] = f'Worker {worker_id}'
                        trained_count += 1
                    else:
                        failed_count += 1
                
                except Exception as e:
                    failed_count += 1
        
        # Save encodings
        save_encodings(encodings, names)
        
        return {
            'success': True,
            'message': f'Batch training completed',
            'trained': trained_count,
            'failed': failed_count,
            'total_encodings': len(encodings)
        }
    
    except Exception as e:
        return {'success': False, 'message': f'Error in batch training: {str(e)}'}

def main():
    """Main function"""
    if len(sys.argv) < 2:
        print(json.dumps({'success': False, 'message': 'Directory path required'}))
        sys.exit(1)
    
    directory = sys.argv[1]
    result = batch_train(directory)
    print(json.dumps(result))

if __name__ == '__main__':
    main()