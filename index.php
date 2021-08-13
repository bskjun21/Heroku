<?php
//  Compooserでインストールしたライブラリを一括読み込み
require_once __DIR__ . '/vendor/autoload.php';

//  アクセストークンを使いCurlHTTPClientをインスタンス化
$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('CHANNEL_ACCESS_TOKEN'));
//  CurlHTTPClientとシークレットを使いLINEBotをインスタンス化
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => getenv('CHANNEL_SECRET')]);
//  LINE Messaging APIがリクエストに付与した署名を取得
$signature = $_SERVER['HTTP_' . \LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];
//  署名が正当かチェック。政党であればリクエストをパース配列へ
$events = $bot->parseEventRequest(file_get_contents('php://input'), $signature);

//  配列に格納された各イベントをループで処理
foreach ($events as $event){
  //  テキストを返信
  //replyTextMessage($bot, $event->getReplyToken(), 'かれん');
  replyImageMessage($bot, $event->getReplyToken(), 'https://' .
                    $SERVER['HTTP_HOST'] .
                    '/imgs/original.jpg', 
                    'https://' . $SERVER['HTTP_HOST'] .
                    '/imgs/preview.jpg'); 
}

//  テキストを返信。引数はLINEBot、返信先、テキスト
function replyTextMessage($bot, $replyToken, $text){
  //  返信を行いレスポンスを取得
  //  TextMessageBuilderの引数はテキスト
  $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text));
  
  //  レスポンスが異常な場合
  if (!$response->isSucceeded()){
    //  エラー内容の出力
    error_log('Failed! '. $response->getHTTPStatus . ' ' . $response->getRawBody());
  }
}

//  画像を返信。引数はLINEBot、返信先、画像URL、サムネイルURL
function replyImageMessage($bot, $replyToken, $originalImageUrl, $previewImageUrl){
  //  ImageMessageBuilderの引数は画像URL、サムネイルURL
  $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($originalImageUrl, $previewImageUrl));
  if (!$response->isSucceeded()){
    error_log('Failed! '. $response->getHTTPStatus . ' ' . $response->getRawBody());
  }
}
?>