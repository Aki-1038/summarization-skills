<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$message = $input['message'];

// 使用 OpenAI API - php
$apiKey = 'OpenAI API 金鑰'; // 請替換為你的 OpenAI API 金鑰
$url = 'https://api.openai.com/v1/chat/completions';
/*
$data = [
    'model' => 'gpt-4o-mini',
    'messages' => [
        ['role' => 'system', 'content' => '# CONTEXT # 我們正在設計一個專注學習和加強記憶的對話模式。這個模式的目的是幫助學習者利用視覺化技巧、聯想法和空間記憶來加強記憶。這種技巧基於空間記憶系統（例如記憶宮殿）或利用符號或圖像作為記憶線索，可以幫助更有效地組織和存儲資訊。例如，可以使用押韻或縮寫來增強記憶效果來幫助記住信息。 # OBJECTIVE # ChatGPT需要扮演一個記憶和學習的大師，利用強化記憶的技巧來指導學習者。特別是，要通過將信息與熟悉的空間（如記憶宮殿）聯繫來增強記憶效果。 # STYLE # 回答方式應該具有指導性、深思熟慮，並以一種清晰且易於跟隨的方式呈現。這樣的風格可以幫助學習者在過程中感到自信和舒適。 # TONE # 語氣應該是專業且富有啟發性，充滿激勵與支持，使學習者能夠覺得自己在一位大師的指導下進行學習，並且能夠實現進步 # AUDIENCE # 學習者，無論是學生還是成人學習者，對記憶加強技巧有興趣，希望提升自己記憶力和學習效率的人群。# RESPONSE # 我將引導你進入一個能夠幫助你加強記憶的世界，這是一個能夠幫助你記住信息並讓學習變得更具趣味和深度的技巧。 #zh-TW'],
        ['role' => 'user', 'content' => $message],
    ],
];

*/

// 這是你存儲上下文的地方
session_start();
if (!isset($_SESSION['context'])) {
    $_SESSION['context'] = [];
}

// 將當前用戶消息添加到上下文中
array_push($_SESSION['context'], ['role' => 'user', 'content' => $message]);
// 合併上下文與當前消息

/*

$data = [
    'model' => 'gpt-4o-mini',

    'messages' => [//array_merge($context,
    //'messages' => array_merge($_SESSION['context'], [['role' => 'user', 'content' => $message]]);
        ['role' => 'system', 'content' => '**Context（上下文）**  你正在尋找一位英文老師來幫助你提高英語能力。ChatGPT將作為你的英文老師，提供各種學習資源和練習機會。**Objective（目標）**  利用ChatGPT的多種功能來提升你的英語寫作、口語、單字和文法能力，並制定有效的學習計劃。**Style（風格）**  - **友好且鼓勵**：以支持和鼓勵的語氣進行教學，讓學習者感到放鬆。- **清晰易懂**：使用簡單明瞭的語言解釋概念和技巧。**Tone（語氣）**  - **專業但輕鬆**：保持專業的態度，同時讓學習過程變得輕鬆愉快。**Audience（受眾）**  針對想要提高英語能力的學生，無論是初學者還是進階學習者。**Response（回應格式）**  以對話形式呈現，包括具體的指導、範例和練習建議。'],
        ['role' => 'user', 'content' => $message],
],
];
*/

$data = [
    'model' => 'gpt-4o-mini',
    'messages' => [
        ['role' => 'system', 'content' => '
        我希望 ChatGPT 在回應時使用臺灣特有的慣用語和口語表達，讓交流更自然。
        1. Context（上下文）提供背景信息，讓模型了解所要表達的概念所處的情境。
        2. Objective（目標）明確希望模型達成的目標，即用一句話表達一個概念。
        3. Style（風格）指定所需的語言風格，比如正式、非正式、簡潔等。
        4. Tone（語氣）設定語氣，例如積極、專業或輕鬆。
        5. Audience（受眾）確定目標受眾，這樣可以使表達更具針對性。
        6. Response（回應格式）要求以簡潔的句子形式回應。 不要顯示#prompt#。取得 #prompt# 之後，再用 #prompt# 請chatGP以 #prompt# 的內容回答一次 
        #zh-TW'],
        ['role' => 'user', 'content' => $message],
    ],
];





$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\n" .
                     "Authorization: Bearer $apiKey\r\n",
        'method'  => 'POST',
        'content' => json_encode($data),
    ],
];

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
$response = json_decode($result, true);

$reply = $response['choices'][0]['message']['content'] ?? '抱歉，我無法回答。';

error_log(print_r($data, true)); // 輸出請求資料
error_log(print_r($response, true)); // 輸出回應資料

echo json_encode(['reply' => $reply]);
?>
