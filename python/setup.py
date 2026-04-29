#!/usr/bin/env python3
"""
Setup Script
Downloads required models and initializes the system
"""

import os
import sys
import urllib.request
import json

# Configuration
MODELS_DIR = os.path.join(os.path.dirname(__file__), 'models')
CASCADE_URL = 'https://raw.githubusercontent.com/opencv/opencv/master/data/haarcascades/haarcascade_frontalface_default.xml'
CASCADE_PATH = os.path.join(MODELS_DIR, 'haarcascade_frontalface_default.xml')

def ensure_models_dir():
    """Create models directory"""
    if not os.path.exists(MODELS_DIR):
        os.makedirs(MODELS_DIR, exist_ok=True)
        print(f"Created models directory: {MODELS_DIR}")

def download_cascade():
    """Download Haar Cascade classifier"""
    try:
        if os.path.exists(CASCADE_PATH):
            print("Cascade classifier already exists")
            return True
        
        print("Downloading Haar Cascade classifier...")
        urllib.request.urlretrieve(CASCADE_URL, CASCADE_PATH)
        print(f"Downloaded to: {CASCADE_PATH}")
        return True
    
    except Exception as e:
        print(f"Error downloading cascade: {str(e)}")
        return False

def check_dependencies():
    """Check if required packages are installed"""
    required_packages = [
        'cv2',
        'face_recognition',
        'numpy',
        'PIL'
    ]
    
    missing_packages = []
    
    for package in required_packages:
        try:
            __import__(package)
            print(f"✓ {package} is installed")
        except ImportError:
            print(f"✗ {package} is NOT installed")
            missing_packages.append(package)
    
    if missing_packages:
        print(f"\nMissing packages: {', '.join(missing_packages)}")
        print("Install with: pip install -r requirements.txt")
        return False
    
    return True

def main():
    """Main setup function"""
    print("=" * 50)
    print("Laberion WMS - Python Setup")
    print("=" * 50)
    
    # Check dependencies
    print("\nChecking dependencies...")
    if not check_dependencies():
        sys.exit(1)
    
    # Create models directory
    print("\nSetting up models directory...")
    ensure_models_dir()
    
    # Download cascade
    print("\nDownloading required models...")
    if not download_cascade():
        print("Warning: Could not download cascade classifier")
    
    print("\n" + "=" * 50)
    print("Setup completed successfully!")
    print("=" * 50)

if __name__ == '__main__':
    main()