Text="ルノアール徹夜なう！！"
Text="新宿でヘルシーな食事知ってる？"
Text="面白い素晴らしいいろんな画像送ってよ。"
Text="みたいな気持ちってあまりインセンティブにならないかな？"

echo ${Text}

echo "----------------------"

echo "line"

echo ${Text} | mecab -d /usr/local/lib/mecab/dic/mecab-ipadic-neologd

echo "----------------------"

echo "default"

echo ${Text} | mecab
