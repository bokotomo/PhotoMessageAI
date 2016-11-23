# -*- coding: utf-8 -*-
try:
  import cv2
  import numpy as np
  import sys
  import tomopy.autoload
except ImportError:
  print("import error")

args = sys.argv
inputFilePath = args[1]
outputFilePath = args[2]

openCVProvider = opencv_provider.OpenCVProvider()
img = cv2.imread(inputFilePath)
gray = cv2.cvtColor(img, cv2.COLOR_RGB2GRAY)
cv2.imwrite(outputFilePath, gray)