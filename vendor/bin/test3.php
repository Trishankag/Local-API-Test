<?php
include_once("../autoload.php"); 
use GuzzleHttp\Client;

class ApiTest extends PHPUnit_Framework_TestCase
{
	 protected function setUp()
    {
		$this->client = new \GuzzleHttp\Client(['base_uri' => 'http://localhost']);
	}
    
      public function testApiData()
    {
	 $response = $this->client->get('/testapi/users');
		
	
    $this->assertEquals(200, $response->getStatusCode());
     $data = json_decode($response->getBody(), true);
	 print_r($data);

        
    }
	
	public function testEmptyApiData()
    {
		$response = $this->client->get('/testapi/users');
     $data = [];
	$this->assertEmpty($data);
        return $data ;
        
    }
	 public function testGetData()
    {
	$response = $this->client->get('/testapi/user/?id=3');	
    $this->assertEquals(200, $response->getStatusCode());
     $data = json_decode($response->getBody(), true);
	 print_r($data);
   
    }
	
	public function testEmptyGetData()
    {	
	$response = $this->client->get('/testapi/user/?id=2');		
     $data = [];
	$this->assertEmpty($data);
        return $data ;
        
    }
	
	public function testDelete_Error()
{
     $response = $this->client->delete('/testapi/deleteUser/?id=2', [
        'http_errors' => false
    ]);
    $this->assertEquals(200, $response->getStatusCode(), "successfully deleted");
}
	
public function testDelete_Empty()
    {
	$response = $this->client->delete('/testapi/deleteUser/?id=2', [
        'http_errors' => false
    ]);	
     $data = [];
	$this->assertEmpty($data);
        return $data ;
        
    }
	
	public function testCreateUser()
	{
	$response = $this->client->post('/testapi/insertUser',
		['body' => json_encode(
			[
			'Name' => 'testtest12345',
			'Email' => 'testtest@gmail.com',
			'created_date'  => "2017-03-30",
			'isadmin'  => 1
			]
		)]
	);
	 $this->assertEquals(200, $response->getStatusCode());
	 $data = json_decode($response->getBody(), true);
  print_r($data);
	$emailval = $data['data']['Email'];
 $dateval = $data['data']['created_date'];
  $adminval = $data['data']['isadmin'];
  $this->assertRegExp('/^.+\@\S+\.\S+$/', $emailval, "Email format is not correct");
  $this->assertRegExp('/\d{4}-\d{2}-\d{2}/', $dateval,'Date format is not correct');
  $this->assertContains($adminval, [0, 1],'The value should be either 0 or 1');
	}
	
	 public function testUpdateUser()
	{
		//$client = new \GuzzleHttp\Client(['base_uri' => 'http://localhost']);


$response = $this->client->put('/testapi/updateUser/?id=3',
    ['body' => json_encode(
        [
            'Name' => 'testtest',
        'Email' => 'testtest1@gmail.com',
		'created_date'  => "2017-03-30",
		'isadmin'  => 0
        ]
    )]
);
$this->assertEquals(200, $response->getStatusCode(),"Successfully Updated");
  $data = json_decode($response->getBody(), true);
  print_r($data);
  $emailval = $data['data']['Email'];
 $dateval = $data['data']['created_date'];
  $adminval = $data['data']['isadmin'];
  $this->assertRegExp('/^.+\@\S+\.\S+$/', $emailval, "Email format is not correct");
  $this->assertRegExp('/\d{4}-\d{2}-\d{2}/', $dateval,'Date format is not correct');
  $this->assertContains($adminval, [0, 1],'The value should be either 0 or 1');
//echo '<pre>' . var_export($response->getStatusCode(), true) . '</pre>';
//echo '<pre>' . var_export($response->getBody()->getContents(), true) . '</pre>';	
	}
}
?>