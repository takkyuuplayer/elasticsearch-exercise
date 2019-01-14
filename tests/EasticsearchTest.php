<?php

use PHPUnit\Framework\TestCase;

final class ElasticsearchTest extends TestCase
{
    private $client = null;
    public function setUp()
    {
        $this->client = \Elasticsearch\ClientBuilder::create()
            ->setHosts(
                [
                    getenv('ELASTICSEARCH_HOST')
                ]
            )
            ->build();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(
            \Elasticsearch\Client::class,
            $this->client
        );
    }

    public function testGet()
    {
        $response = $this->client->get(
            [
                'index' => 'bank',
                'type' => '_doc',
                'id' => '1',
            ]
        );
        $this->assertSame(
            array(
                '_index' => 'bank',
                '_type' => '_doc',
                '_id' => '1',
                '_version' => $response['_version'],
                'found' => true,
                '_source' =>
                    array(
                    'account_number' => 1,
                    'balance' => 39225,
                    'firstname' => 'Amber',
                    'lastname' => 'Duke',
                    'age' => 32,
                    'gender' => 'M',
                    'address' => '880 Holmes Lane',
                    'employer' => 'Pyrami',
                    'email' => 'amberduke@pyrami.com',
                    'city' => 'Brogan',
                    'state' => 'IL',
                ),
            ),
            $response
        );
    }

    public function testSearch()
    {
        $response = $this->client->search(
            [
                "index" => "bank",
                "type" => "_doc",
                "body" => [
                    "query" => [
                        "bool" => [
                            "filter" => [
                                "range" => [
                                    "balance" => [
                                        "gte" => 20000,
                                        "lte" => 30000,
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );
        $this->assertSame(217, $response['hits']['total']);
    }
}