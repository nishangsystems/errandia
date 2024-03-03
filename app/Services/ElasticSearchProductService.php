<?php

namespace App\Services;

use Elasticsearch\ClientBuilder;

class ElasticSearchProductService {

    protected $client;
    private string $index_name = 'errandia';
    private string $type = 'item';

    protected $settings = [
        'body' => [
            'settings' => [
                'number_of_shards' => 3,
                'number_of_replicas' => 2,
                'analysis' => [
                    'analyzer' => [
                        'search_index' => [
                            'tokenizer' => 'standard',
                            'filter' => ['lowercase','asciifolding', 'synonym', 'german'],
                        ]
                    ],
                    'filter' => [
                        'synonym' => [
                            'type' => 'synonym',
                            'ignore_case' => true,
                            'synonyms' => [],
                        ],
                        'german' => [
                            'type' => 'stemmer',
                            'ignore_case' => true,
                            'name' => 'german',
                        ]
                    ],
                ]

            ],
            'mappings' => [
                '_source' => [
                    'enabled' => true
                ],
                'properties' => [
                    'name' => ['type' => 'text', 'analyzer' => 'search_index'],
                    'description' => ['type' => 'text', 'analyzer' => 'search_index'],
                    'tags' => ['type' => 'text'],
                    'service' => ['type' => 'boolean'],
                    'status' => ['type' => 'boolean'],
                    'unit_price' => ['type' => 'long'],
                    'quantity' => ['type' => 'integer'],
                    'shop' => [
                        'type' => 'nested',
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'name' => ['type' => 'keyword'],
                            'description' => ['type' => 'text', 'analyzer' => 'search_index'],
                            'region' => [
                                'type' => 'nested',
                                'properties' => [
                                    'id' => ['type' => 'integer'],
                                    'name' => ['type' => 'keyword'],
                                ]
                            ],
                            'town' => [
                                'type' => 'nested',
                                'properties' => [
                                    'id' => ['type' => 'integer'],
                                    'name' => ['type' => 'keyword'],
                                ]
                            ],
                            'street' => [
                                'type' => 'nested',
                                'properties' => [
                                    'id' => ['type' => 'integer'],
                                    'name' => ['type' => 'keyword'],
                                ]
                            ]
                        ]
                    ],
                    'categories' => ['type' => 'keyword'],
                    'category_ids' => ['type' => 'keyword'],
                ]
            ]
        ]
    ];

    public function __construct()
    {
        $this->client = ClientBuilder::create()->build();
        if(!$this->client->indices()->exists(['index' => $this->index_name])) {
            $this->settings['index'] = $this->index_name;
            $this->client->indices()->create($this->settings);
        }
    }

    public static function init()
    {
        return new ElasticSearchProductService();
    }

    public function create_document($id, $item)
    {
        $this->client->index([
            'index' => $this->index_name,
            'id' => $id,
            'body' => $this->getDocument($item)
        ]);
    }

    public function bulk_documents($items = array())
    {
        $params['body'] = [];
        foreach ($items as $item) {
            $params['body'][] = [
                'index' => [
                    '_index' => $this->index_name,
                    '_id' => $item->id,
                ]
            ];
            $params['body'][] = $this->getDocument($item);
        }

        if (!empty($items)) {
            $this->client->bulk($params);
        }
    }

    public function search($search_term)
    {
        $params = [
            'index' => $this->index_name,
            'size' => 10,
            'body' => [
                'query' => [
                    'bool' => [
                        'should' => [
                            ['match' => ['name' => $search_term]],
                            ['match' => ['shop.name' => $search_term]],
                            ['match' => ['description' => $search_term]],
                            ['match' => ['shop.description' => $search_term]],
                        ]
                    ]
                ]
            ]
        ];

        return $this->client->search($params);
    }

    public function update_docuemnt($id, $item)
    {
        $this->client->update([
            'index' => $this->index_name,
            'id' => $id,
            'body' => $this->getDocument($item)
        ]);
    }

    public function delete_docuemnt($id)
    {
        $this->client->index([
            'index' => $this->index_name,
            'id' => $id,
        ]);
    }

    private function getDocument($item)
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'description' => $item->description,
            'unit_price' => $item->unit_price,
            'quantity' => $item->quantity,
            'service' => $item->service == 1,
            'status' => $item->status == 1,
            'tags' => explode(',', $item->tags),
            'shop' => [
                'id' => $item->shop->id,
                'name' => $item->shop->name,
                'description' => $item->shop->description,
                'region' => [
                    'id' => $item->shop->region->id,
                    'name' => $item->shop->region->name,
                ],
                'town' => [
                    'id' => $item->shop->town->id,
                    'name' => $item->shop->town->name,
                ],
                'street' => [
                    'id' => $item->shop->street->id,
                    'name' => $item->shop->street->name,
                ]
            ],
        ];
    }

}