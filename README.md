# Saya - LineBot
Sayaさんは写真を扱ってくれるLineBotです。  
写真の加工や、写真を送ると他の人に意見を聞いてくれたり、画像を検索をしてくれたりします。  
自動学習して賢くなっていきます。  

## Sayaを友達に追加する
<img src="https://tomo.syo.tokyo/openimg/saya_line_qr.png" width="200px">  
<a href="https://line.me/R/ti/p/%40hxs4046d"><img height="36" border="0" alt="友だち追加" src="https://scdn.line-apps.com/n/line_add_friends/btn/ja.png"></a>

## DemoImage
<img src="https://tomo.syo.tokyo/openimg/5219767428574.LINE.jpg" width="200px">
<img src="https://tomo.syo.tokyo/openimg/5219756900962.LINE.jpg" width="200px">  

## できること
1 会話(自動学習して新しいことばの組み合わせなど使う)  
2 写真加工  
3 他の友人に写真をアンケート  

## Getting started
1 edit config.php  

    define("SECRET_TOKEN", "LINE's SECRET_TOKEN");  
    define("ACCESS_TOKEN", "LINE's ACCESS_TOKEN");
    define("URL_ROOT_PATH", "using URL");

2 image_converter run python script so you set Python's PATH that can run OpenCV , to apacheUser.  
Method 1  
    
    you add this code to ./image_converter/response.sh
    export PATH=PYTHON'S PATH:$PATH

Method 2  
    
    you add PYTHON'S PATH to apacheUser

## FolderDescription
image_converter Folder is program that convert image by python and OpenCV  
sayalib Folder is program that response saya's conversation  
tomolib Folder is program class that is written me  
vendor Folder is program that is line-sdk-php  

## Using
PHP  
PYTHON  
OpenCV  
mecab  


## Library
<a href="https://github.com/line/line-bot-sdk-php">LineBotSDK-PHP</a>  
<a href="http://opencv.org/">http://opencv.org</a>
