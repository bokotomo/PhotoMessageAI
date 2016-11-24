# -*- coding: utf-8 -*-
try:
  import cv2
  import numpy as np
  import sys
except ImportError:
  print("import error")

def convertImageData(OriginImg):
# ConvImg = cv2.cvtColor(OriginImg, cv2.COLOR_BGR2RGB)
#  ConvImg = convertThreshold(OriginImg)
#  ConvImg = convertBlur(OriginImg)
#  ConvImg = convertMorph(OriginImg, 4)
#  ConvImg = convertLine(OriginImg)
  ConvImg = convertCascadeClassifier(OriginImg)
  return ConvImg

def convertThreshold(OriginImg):
  ConvImg = cv2.cvtColor(OriginImg, cv2.COLOR_BGR2RGB)
  return ConvImg

def convertThreshold(OriginImg):
  grayed = cv2.cvtColor(OriginImg, cv2.COLOR_BGR2GRAY)
  under_thresh = 105
  upper_thresh = 145
  maxValue = 255
  th, drop_back = cv2.threshold(grayed, under_thresh, maxValue, cv2.THRESH_BINARY)
  th, clarify_born = cv2.threshold(grayed, upper_thresh, maxValue, cv2.THRESH_BINARY_INV)
  merged = np.minimum(drop_back, clarify_born)
  return merged

def convertNega(OriginImg):
  ConvImg = cv2.cvtColor(OriginImg, cv2.COLOR_BGR2RGB)
  return ConvImg

def convertBlur(OriginImg):
  filtered = cv2.GaussianBlur(OriginImg, (15, 15), 0)
  return filtered

def convertMorph(OriginImg, Value):
  ImgSize = OriginImg.shape
  kernel = np.ones((Value, Value),np.uint8)
  opened = cv2.morphologyEx(OriginImg, cv2.MORPH_OPEN, kernel, iterations=2)
  return opened

def convertLine(OriginImg):
  ConvImg = OriginImg
  height = OriginImg.shape[0]
  width = OriginImg.shape[1]
  cv2.line(OriginImg, (width // 2 - 1, 0), (width // 2 - 1, height - 1), (245, 245, 245), 10)
  cv2.line(OriginImg, (0, height // 2), (width - 1, height // 2 - 1), (245, 245, 245), 10)
  return ConvImg

def convertCascadeClassifier(OriginImg):
  ConvImg = OriginImg
  face_cascade = cv2.CascadeClassifier('/usr/local/src/opencv-3.1.0/data/haarcascades/haarcascade_frontalface_default.xml')
  eye_cascade = cv2.CascadeClassifier('/usr/local/src/opencv-3.1.0/data/haarcascades/haarcascade_eye.xml')
  gray = cv2.cvtColor(ConvImg, cv2.COLOR_BGR2GRAY)
  gray = convertMorph(ConvImg, 3)
  faces = face_cascade.detectMultiScale(gray, 1.3, 5)
  for (x,y,w,h) in faces:
    cv2.rectangle(ConvImg,(x,y),(x+w,y+h),(0,200,250),10)
    roi_gray = gray[y:y+h, x:x+w]
    roi_color = ConvImg[y:y+h, x:x+w]
    eyes = eye_cascade.detectMultiScale(roi_gray)
    for (ex,ey,ew,eh) in eyes:
      cv2.rectangle(roi_color,(ex,ey),(ex+ew,ey+eh),(110,110,240),10)
  return ConvImg

args = sys.argv
inputFilePath = args[1]
outputFilePath = args[2]

OriginImg = cv2.imread(inputFilePath)
ConvImg = convertImageData(OriginImg)
cv2.imwrite(outputFilePath, ConvImg)
