# -*- coding: utf-8 -*-
try:
  import cv2
  import numpy as np
  import sys
  import json
except ImportError:
  print("import error")

def analyzeImageData(OriginImg):
  HumanFlag = checkCascadeClassifier(OriginImg)
  return HumanFlag

def convertMorph(OriginImg, Value):
  ImgSize = OriginImg.shape
  kernel = np.ones((Value, Value),np.uint8)
  opened = cv2.morphologyEx(OriginImg, cv2.MORPH_OPEN, kernel, iterations=2)
  return opened

def checkCascadeClassifier(OriginImg):
  ConvImg = OriginImg
  face_cascade = cv2.CascadeClassifier('/usr/local/src/opencv-3.1.0/data/haarcascades/haarcascade_frontalface_default.xml')
  eye_cascade = cv2.CascadeClassifier('/usr/local/src/opencv-3.1.0/data/haarcascades/haarcascade_eye.xml')
  gray = cv2.cvtColor(ConvImg, cv2.COLOR_BGR2GRAY)
  gray = convertMorph(ConvImg, 3)
  faces = face_cascade.detectMultiScale(gray, 1.3, 5)
  HumanNum = 0
  for (x,y,w,h) in faces:
    HumanNum += 1
  return HumanNum

args = sys.argv
inputFilePath = args[1]

OriginImg = cv2.imread(inputFilePath)
HumanFlag = analyzeImageData(OriginImg)
dict = {
    "human_num": HumanFlag
}
jsonstring = json.dumps(dict, ensure_ascii=False)
print(jsonstring)
