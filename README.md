# Saya - LineBot
Sayaさんは写真を扱ってくれるLineBotです。  
写真の加工や、写真を送ると他の人に意見を聞いてくれたり、画像を検索をしてくれたりします。  
自動学習して賢くなっていきます。  

## Sayaを友達に追加する
<img src="https://tomo.syo.tokyo/openimg/saya_line_qr.png" width="200px">  
<a href="https://line.me/R/ti/p/%40hxs4046d"><img height="36" border="0" alt="友だち追加" src="https://scdn.line-apps.com/n/line_add_friends/btn/ja.png"></a>

## DemoImage
<img src="https://tomo.syo.tokyo/openimg/5219767428574.LINE.jpg" width="180px">
<img src="https://tomo.syo.tokyo/openimg/5219756900962.LINE.jpg" width="180px">
<img src="https://tomo.syo.tokyo/openimg/5249198785297.LINE.jpg" width="180px">
<img src="https://tomo.syo.tokyo/openimg/5249388613463.LINE.jpg" width="180px">  

## できること
1 会話(自動学習して新しいことばの組み合わせなど使う)  
2 写真加工  
3 他の友人に写真をアンケート  

## Getting started
1 edit config.php  

    define("SECRET_TOKEN", "LINE's SECRET_TOKEN");  
    define("ACCESS_TOKEN", "LINE's ACCESS_TOKEN");
    define("URL_ROOT_PATH", "using URL");

2 ./script/image_converter run python script, So you must set Python's PATH that can run OpenCV, to apacheUser's $PATH.  
Method 1  
    
    you add this code to ./script/image_converter/response_image.sh
    export PATH=(PYTHON'S PATH):$PATH
    

## FolderDescription
./script/image_converter Folder is program that convert image by python and OpenCV  
./script/mecab_string_analysis Folder is program that analys user's text  
./library/sayalib Folder is program that response saya's conversation  
./library/tomolib Folder is program class that is written me  
./vendor Folder is program that is line-sdk-php  


## Using
PHP 7.0.12  
Python 3.6.0b3  
C++  
OpenCV'3.1.0'  
mecab0.996  


## Library
<a href="https://github.com/line/line-bot-sdk-php">LineBotSDK-PHP</a>  
<a href="http://opencv.org/">opencv</a>  
<a href="http://opencv.jp/opencv-2svn/cpp/index.html">opencv japan</a>  
<a href="http://taku910.github.io/mecab/#usage-tools">mecab</a>  

