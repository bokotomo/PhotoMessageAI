# -*- coding: utf-8 -*-

import cv2
import numpy as np
import sys

args = sys.argv

inputFilePath = args[1]
outputFilePath = args[2]

img = cv2.imread(inputFilePath)
gray = cv2.cvtColor(img, cv2.COLOR_RGB2GRAY)
cv2.imwrite(outputFilePath, gray)

print "ok";