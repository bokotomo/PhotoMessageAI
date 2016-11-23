# -*- coding: utf-8 -*-
try:
  import cv2
  import numpy as np
  import sys
except ImportError:
  print("import error")

args = sys.argv
inputFilePath = args[1]
outputFilePath = args[2]

img = cv2.imread(inputFilePath)
gray = cv2.cvtColor(img, cv2.COLOR_BGR2RGB)
cv2.imwrite(outputFilePath, gray)