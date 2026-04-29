#!/usr/bin/env python3
"""
Compare Two Faces
Compares two face images and returns similarity score
"""

import sys
import json
import os
import face_recognition

def compare_faces(image1_path, image2_path):
    """Compare two face images"""
    try:
        # Validate files
        if not os.path.exists(image1_path):
            return {'success': False, 'message': 'First image not found'}
        
        if not os.path.exists(image2_path):
            return {'success': False, 'message': 'Second image not found'}
        
        # Load images
        image1 = face_recognition.load_image_file(image1_path)
        image2 = face_recognition.load_image_file(image2_path)
        
        # Get encodings
        encodings1 = face_recognition.face_encodings(image1)
        encodings2 = face_recognition.face_encodings(image2)
        
        if len(encodings1) == 0:
            return {'success': False, 'message': 'No face found in first image'}
        
        if len(encodings2) == 0:
            return {'success': False, 'message': 'No face found in second image'}
        
        # Compare faces
        results = face_recognition.compare_faces(
            [encodings1[0]], 
            encodings2[0],
            tolerance=0.6
        )
        
        # Get distance
        distance = face_recognition.face_distance(
            [encodings1[0]], 
            encodings2[0]
        )[0]
        
        # Calculate similarity (inverse of distance)
        similarity = 1 - distance
        
        return {
            'success': True,
            'match': results[0],
            'similarity': float(similarity),
            'distance': float(distance)
        }
    
    except Exception as e:
        return {'success': False, 'message': f'Error comparing faces: {str(e)}'}

def main():
    """Main function"""
    if len(sys.argv) < 3:
        print(json.dumps({'success': False, 'message': 'Two image paths required'}))
        sys.exit(1)
    
    image1_path = sys.argv[1]
    image2_path = sys.argv[2]
    
    result = compare_faces(image1_path, image2_path)
    print(json.dumps(result))

if __name__ == '__main__':
    main()
