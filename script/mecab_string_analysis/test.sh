Text="ルノアール徹夜なう！！"
Text="新宿でヘルシーな食事"

echo ${Text}

echo "----------------------"

echo "line"

echo ${Text} | mecab -d /usr/local/lib/mecab/dic/mecab-ipadic-neologd

echo "----------------------"

echo "default"

echo ${Text} | mecab
