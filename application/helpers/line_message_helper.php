<?php defined('BASEPATH') or exit('No direct script access allowed');

use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\RawMessageBuilder;

function line_message_appointment(){
    if (!empty($customer['lineuserid'])){
        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(config('line_access_token'));
        $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => config('line_secret')]);

        $RawMessageBuilder = new \LINE\LINEBot\MessageBuilder\RawMessageBuilder(
            [
                'type' => 'flex',
                'altText' => '預約服務成功訊息',
                'contents' => [
                    'type' => 'bubble',
                        'body' => [
                            'type' => 'box',
                            'layout' => 'vertical',
                            'contents' => [
                                [
                                    'type' => 'text',
                                    'text' => '預約成功',
                                    'weight' => 'bold',
                                    'color' => '#1DB446',
                                    'size' => 'sm'
                                ],
                                [
                                    'type' => 'text',
                                    'text' => "XX分店",
                                    'weight' => 'bold',
                                    'size' => 'xxl',
                                    'margin' => 'md'
                                ],
                                [
                                    'type' => 'text',
                                    'text' => "台北市信義區大馬路xxx",
                                    'size' => 'xs',
                                    'color' => '#aaaaaa',
                                    'wrap' => true
                                ],
                                [
                                    'type' => 'separator',
                                    'margin' => 'xxl'
                                ],
                                [
                                    'type' => 'box',
                                    'layout' => 'vertical',
                                    'margin' => 'xxl',
                                    'spacing' => 'sm',
                                    'contents' => [
                                        [
                                            'type' => 'box',
                                            'layout' => 'horizontal',
                                            'contents' => [
                                                [
                                                    'type' => 'text',
                                                    'text' => '姓名',
                                                    'size' => 'sm',
                                                    'color' => '#555555',
                                                    'flex' => 0
                                                ],
                                                [
                                                    'type' => 'text',
                                                    'text' => $customer['last_name'] .  $customer['first_name'],
                                                    'size' => 'sm',
                                                    'color' => '#111111',
                                                    'align' => 'end'
                                                ]
                                            ]
                                        ],
                                        [
                                            'type' => 'box',
                                            'layout' => 'horizontal',
                                            'contents' => [
                                        [
                                            'type' => 'text',
                                            'text' => '預約日期',
                                            'size' => 'sm',
                                            'color' => '#555555',
                                            'flex' => 0
                                        ],
                                        [
                                            'type' => 'text',
                                            'text' => $appointment['start_datetime'],
                                            'size' => 'sm',
                                            "color" => '#111111',
                                            'align' => 'end'
                                        ]
                                    ]
                                ],
                                [
                                    'type' => 'box',
                                    'layout' => 'horizontal',
                                    'contents' => [
                                        [
                                            'type'=> 'text',
                                            'text' => '預約時間',
                                            'size' => 'sm',
                                            'color' => '#555555',
                                            'flex' => 0
                                        ],
                                        [
                                            'type' => 'text',
                                            'text' => $appointment['end_datetime'],
                                            'size' => 'sm',
                                            'color' => '#111111',
                                            'align' => 'end'
                                        ]
                                    ]
                                ],
                                [
                                    'type' => 'box',
                                    'layout' => 'horizontal',
                                    'contents' => [
                                        [
                                            'type' => 'text',
                                            'text' => '預約服務',
                                            'size' => 'sm',
                                            'color' => '#555555'
                                        ],
                                        [
                                            'type' => 'text',
                                            'text' => $service['name'],
                                            'size' => 'sm',
                                            'color' => '#111111',
                                            'align' => 'end'
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        [
                            'type' => 'separator',
                            'margin' => 'xxl'
                        ],
                        [
                            'type' => 'box',
                            'layout' => 'horizontal',
                            'margin' => 'md',
                            'contents' => [
                                [
                                    'type' => 'text',
                                    'text' => '注意事項，請提前 30 分鐘到場',
                                    'size' => 'xs',
                                    'color' => '#aaaaaa',
                                    'flex' => 0
                                ]
                            ]
                        ]
                    ]
                ],
                'styles' => [
                    'footer' => [
                        'separator' => true
                    ]
                ]
            ]
        ]);
        
        $bot->pushMessage($customer['lineuserid'], $RawMessageBuilder);
    }
}
