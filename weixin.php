<?php

if (! empty ( $_GET ['echostr'] ) && ! empty ( $_GET ["signature"] ) && ! empty ( $_GET ["nonce"] )) {
	$signature = $_GET ["signature"];
	$timestamp = $_GET ["timestamp"];
	$nonce = $_GET ["nonce"];

	$tmpArr = array (
			'James',
			$timestamp,
			$nonce
	);
	sort ( $tmpArr, SORT_STRING );
	$tmpStr = sha1 ( implode ( $tmpArr ) );

	if ($tmpStr == $signature) {
		echo $_GET ["echostr"];
	}
	exit ();
}

function get_base_url() {
	return "http://". $_SERVER['HTTP_HOST'];
}

function get_dogs_base_url() {
	return "http://dogs-developer-edition.ap1.force.com";
}

/*
 * Text Messages retrieved from weixin mobile platform. 
 * <xml> 
 * 		<ToUserName><![CDATA[toUser]]></ToUserName> 
 * 		<FromUserName><![CDATA[fromUser]]></FromUserName> 
 * 		<CreateTime>1348831860</CreateTime> 
 * 		<MsgType><![CDATA[text]]></MsgType> 
 * 		<Content><![CDATA[this is a test]]></Content> 
 * 		<MsgId>1234567890123456</MsgId>
 * </xml>
 */
function process_text_request($postObj) {
	$textMessage = trim ( $postObj->Content );
	error_log ( "The message from client is:" . $textMessage );
	switch ($textMessage) {
		case 'T' :
		case "t" :
			test_post_text_interface ( $postObj, "Hi, welcom to James Karl's club!" );
			break;
		case 'M' :
		case 'm' :
			test_post_music_interface ( $postObj );
			break;
		case 'n':
		case 'N':
			test_post_news_msg($postObj);
			break;
		default :
			echo post_text_msg ( $postObj->ToUserName, $postObj->FromUserName, "Reply the character bellow to complete your request." . "\nT: text \nM: music\nN: article	
					");
	}
}

/*
 * Picture Messages retrieved from weixin mobile platform. 
 * <xml> 
 * 		<ToUserName><![CDATA[toUser]]></ToUserName> 
 * 		<FromUserName><![CDATA[fromUser]]></FromUserName> 
 * 		<CreateTime>1348831860</CreateTime> 
 * 		<MsgType><![CDATA[image]]></MsgType> 
 * 		<PicUrl><![CDATA[this is a url]]></PicUrl>
 * 		<MediaId><![CDATA[media_id]]></MediaId>
 * 		<MsgId>1234567890123456</MsgId> 
 * </xml>
 */
function process_image_request($postObj) {
	 post_image_msg($postObj->toUserName, $postObj->fromUserName, $postObj->MediaId);
}

/*
 * Location message retrieved from weixin mobile platform. 
 * <xml> 
 * 		<ToUserName><![CDATA[toUser]]></ToUserName> 
 * 		<FromUserName><![CDATA[fromUser]]></FromUserName> 
 * 		<CreateTime>1351776360</CreateTime> 
 * 		<MsgType><![CDATA[location]]></MsgType> 
 * 		<Location_X>23.134521</Location_X> 
 * 		<Location_Y>113.358803</Location_Y> 
 * 		<Scale>20</Scale> 
 * 		<Label><![CDATA[位置信息]]></Label> 
 * 		<MsgId>1234567890123456</MsgId>
 * </xml>
 */
function process_location_request($postObj) {
}

/*
 * Url Messages retrieved from weixin mobile platform. 
 * <xml> 
 * 		<ToUserName><![CDATA[toUser]]></ToUserName> 
 * 		<FromUserName><![CDATA[fromUser]]></FromUserName> 
 * 		<CreateTime>1351776360</CreateTime> 
 * 		<MsgType><![CDATA[link]]></MsgType> 
 * 		<Title><![CDATA[公众平台官网链接]]></Title> 
 * 		<Description><![CDATA[公众平台官网链接]]></Description> 
 * 		<Url><![CDATA[url]]></Url> 
 * 		<MsgId>1234567890123456</MsgId> 
 * </xml>
 */
function process_link_request($postObj) {
}

/*
 * <xml>
*		<ToUserName><![CDATA[toUser]]></ToUserName>
*		<FromUserName><![CDATA[FromUser]]></FromUserName>
*		<CreateTime>123456789</CreateTime>
*		<MsgType><![CDATA[event]]></MsgType>
*		<Event><![CDATA[subscribe]]></Event>
* </xml> 
 */
function process_event_request($postObj) {
	$event = $postObj->Event;
	switch ($event){
	case 'CLICK':
		process_menu_click_request($postObj);
		break;
	default:
		post_text_msg($postObj->ToUserName, $postObj->FromUserName, '');
	}
}

/*
*<xml>
<ToUserName><![CDATA[toUser]]></ToUserName>
<FromUserName><![CDATA[FromUser]]></FromUserName>
<CreateTime>123456789</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[CLICK]]></Event>
<EventKey><![CDATA[EVENTKEY]]></EventKey>
</xml>
*/
function process_menu_click_request($postObj) {
	$key = $postObj->EventKey;
	switch ($key) {
	case 'query_express':
		$item = new Article();
		$item->desc = "Please follow the link to vote who is MVP and See the vote result.";
		$item->picUrl = get_base_url() . "/testdata/bg.jpg";
		$item->url = get_dogs_base_url() . "/weixin";
		$item->title = 'Who is MVP until now?';
		$articleArray[0] = $item;
		echo post_news_msg($postObj->ToUserName, $postObj->FromUserName, $articleArray);
		break;
	case 'query_weather':
		echo post_text_msg($postObj->ToUserName, $postObj->FromUserName, 'This feature will be ready soon. Please more patient.');
		break;
	default:
		echo post_text_msg($postObj->ToUserName, $postObj->FromUserName, '');
	}
}

/* 
 * <xml>
*		<ToUserName><![CDATA[toUser]]></ToUserName>
*		<FromUserName><![CDATA[fromUser]]></FromUserName>
*		<CreateTime>1357290913</CreateTime>
*		<MsgType><![CDATA[voice]]></MsgType>
*		<MediaId><![CDATA[media_id]]></MediaId>
*		<Format><![CDATA[Format]]></Format>
*		<MsgId>1234567890123456</MsgId>
* </xml>
*/
function process_voice_request($postObj) {
	post_text_msg($postObj->toUserName, $postObj->fromUserName, 'You have received your voice message.');
	#post_voice_msg($postObj->toUserName, $postObj->fromUserName, $postObj->MediaId);	
}

/*
 * Text Messages sent to weixin mobile platform. <xml> <ToUserName><![CDATA[toUser]]></ToUserName> <FromUserName><![CDATA[fromUser]]></FromUserName> <CreateTime>12345678</CreateTime> <MsgType><![CDATA[text]]></MsgType> <Content><![CDATA[content]]></Content> </xml>
 */
function post_text_msg($fromUserName, $toUserName, $textMessage) {
	$tpl = "
<xml>
     <ToUserName><![CDATA[%s]]></ToUserName>
     <FromUserName><![CDATA[%s]]></FromUserName>
     <CreateTime>%s</CreateTime>
     <MsgType><![CDATA[%s]]></MsgType>
     <Content><![CDATA[%s]]></Content>
</xml>";
	return sprintf ( $tpl, $toUserName, $fromUserName, time (), "text", $textMessage );
}

/*
 * Send music to weixin mp <xml> <ToUserName><![CDATA[toUser]]></ToUserName> <FromUserName><![CDATA[fromUser]]></FromUserName> <CreateTime>12345678</CreateTime> <MsgType><![CDATA[music]]></MsgType> <Music> <Title><![CDATA[TITLE]]></Title> <Description><![CDATA[DESCRIPTION]]></Description> <MusicUrl><![CDATA[MUSIC_Url]]></MusicUrl> <HQMusicUrl><![CDATA[HQ_MUSIC_Url]]></HQMusicUrl> </Music> </xml>
 */
function post_music_msg($fromUserName, $toUserName, $musicTitle, $musicDesc, $musicUrl, $HQMusicUrl, $thumbMediaId) {
	$tpl = "
<xml>
	<ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[%s]]></MsgType>
    <Music>
    	<Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <MusicUrl><![CDATA[%s]]></MusicUrl>
        <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
		<ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
    </Music>
</xml>";
	return sprintf ( $tpl, $toUserName, $fromUserName, time (), "music", $musicTitle, $musicDesc, $musicUrl, $HQMusicUrl, $thumbMediaId);
}

/*
 * <xml> <ToUserName><![CDATA[toUser]]></ToUserName> <FromUserName><![CDATA[fromUser]]></FromUserName> <CreateTime>12345678</CreateTime> <MsgType><![CDATA[news]]></MsgType> <ArticleCount>2</ArticleCount> <Articles> <item> <Title><![CDATA[title1]]></Title> <Description><![CDATA[description1]]></Description> <PicUrl><![CDATA[picurl]]></PicUrl> <Url><![CDATA[url]]></Url> </item> <item> <Title><![CDATA[title]]></Title> <Description><![CDATA[description]]></Description> <PicUrl><![CDATA[picurl]]></PicUrl> <Url><![CDATA[url]]></Url> </item> </Articles> </xml>
 */
function post_image_msg($fromUserName, $toUserName, $mediaId) {
	$tpl = "
<xml>
	<ToUserName><![CDATA[%s]]></ToUserName>
	<FromUserName><![CDATA[%s]]></FromUserName>
	<CreateTime>%s</CreateTime>
	<MsgType><![CDATA[%s]]></MsgType>
	<Image>
		<MediaId><![CDATA[%s]]></MediaId>
	</Image>
</xml>
			";
	return sprintf($tpl, $fromUserName, $toUserName, time(), "image", $mediaId);
}

function post_voice_msg($fromUserName, $toUserName, $mediaId) {
	$tpl = "
<xml>
	<ToUserName><![CDATA[%s]]></ToUserName>
	<FromUserName><![CDATA[%s]]></FromUserName>
	<CreateTime>%s</CreateTime>
	<MsgType><![CDATA[%s]]></MsgType>
	<Voice>
		<MediaId><![CDATA[%s]]></MediaId>
	</Voice>
</xml>
			";
	return sprintf($tpl, $fromUserName, $toUserName, time(), "voice", $mediaId);
}

function post_video_msg($fromUserName, $toUserName, $mediaId, $title, $description) {
	$tpl = "
<xml>
	<ToUserName><![CDATA[%s]]></ToUserName>
	<FromUserName><![CDATA[%s]]></FromUserName>
	<CreateTime>%s</CreateTime>
	<MsgType><![CDATA[%s]]></MsgType>
	<Video>
		<MediaId><![CDATA[%s]]></MediaId>
		<Title><![CDATA[%s]]></Title>
		<Description><![CDATA[%s]]></Description>
	</Video> 
</xml>
			";
	return sprintf($tpl, $fromUserName, $toUserName, time(), "video", $mediaId, $title, $description);
}

function post_news_msg($fromUserName, $toUserName, $articleArray) {

	define('MAX_ARTICLE_NUM', 10); //from the official specification.
	$arrLen = count ( $articleArray );
	
	if (!$articleArray) {
		return ""; //does't it work correctlly?
	}
	
	$tpl = "
<xml>
	<ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[%s]]></MsgType>
    <ArticleCount>%s</ArticleCount>
    <Articles>";
	
	$retStr = sprintf ( $tpl, $toUserName, $fromUserName, time (), 'news', $arrLen );
	
	$article = "
<item>
	<Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
    <PicUrl><![CDATA[%s]]></PicUrl>
    <Url><![CDATA[%s]]></Url>
</item>";

	for($i = 0; $i < $arrLen && $i < MAX_ARTICLE_NUM; $i ++) {
		$retStr .= sprintf ( $article, $articleArray[$i]->title, $articleArray[$i]->desc, $articleArray[$i]->picUrl, $articleArray[$i]->url);
	}
	
	$retStr .= "</Articles> </xml>";
	
	return $retStr;
}

function get_xml_obj($xmlString) {
	return simplexml_load_string ( $xmlString, 'SimpleXMLElement', LIBXML_NOCDATA );
}
function reply_weixin_mp($postObj) {
	$msgType = trim ( $postObj->MsgType );
	switch ($msgType) {
		case 'text' :
			process_text_request ( $postObj );
			break;
		case 'image' :
			process_image_request ( $postObj );
			break;
		case 'location' :
			process_location_request ( $postObj );
			break;
		case 'link' :
			process_link_request ( $postObj );
			break;
		case 'voice' :
			process_voice_request ( $postObj );
			break;
		case 'video' :
			process_video_request ( $postObj );
			break;
		case 'event' :
			process_event_request ( $postObj );
			break;
	}
}

class Article {
	public $title;
	public $desc;
	public $picUrl;
	public $url;
}

class MusicInfo {
	public $musicTitle;
	public $musicesc;
	public $musicUrl;
	public $HQMusicUrl;
}

/* InterfaceTestCode Begin ========================================================= */
function test_post_text_interface($postObj, $response) {
	echo post_text_msg ( $postObj->ToUserName, $postObj->FromUserName, $postObj->ToUserName . "\n" . $postObj->FromUserName . "\n" . $response );
}
function test_post_music_interface($postObj) {
	$musicInfo = new MusicInfo ();
	$musicInfo->musicTitle = "Better In Time";
	$musicInfo->musicDesc = "
    歌手： Leona Lewis
    所属专辑： Songs for Japan
    发行时间：2011-03-25";
	$musicInfo->musicUrl = "testdata/BetterInTime.mp3";
	$musicInfo->HQMusicUrl = "";
	echo post_music_msg ( $postObj->ToUserName, $postObj->FromUserName, $musicInfo );
}

function test_post_news_msg($postObj) {
	$item = new Article();
	$item->desc = "some beautiful pictures.";
	$item->picUrl = get_base_url() . "/testdata/bg.jpg";
	$item->url = get_base_url() . "/images.php";
	$item->title = 'Lovely Girls';
	$articleArray[0] = $item;
	echo post_news_msg($postObj->ToUserName, $postObj->FromUserName, $articleArray);
}
/* InterfaceTestCode End ========================================================= */

error_reporting ( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
function main() {
	$postStr = $GLOBALS ["HTTP_RAW_POST_DATA"];
	$postObj = get_xml_obj ( $postStr );
	reply_weixin_mp ( $postObj );
}

main ();

/* TestCode Begin ========================================================= */
$testTextPostString = "<xml>
 <ToUserName>James</ToUserName>
 <FromUserName>Karl</FromUserName> 
 <CreateTime>1348831860</CreateTime>
 <MsgType>text</MsgType>
 <Content>This is a test text.</Content>
 <MsgId>1234567890123456</MsgId>
 </xml>
";
function local_test_main() {
	global $testTextPostString;
	$postStr = &$testTextPostString;
	$postObj = get_xml_obj ( $postStr );
	reply_weixin_mp ( $postObj );
}

// Test start here
// local_test_main();
/* TestCode End========================================================= */
?>
