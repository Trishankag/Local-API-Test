<?php
include_once("../autoload.php"); 


class ApiTest extends PHPUnit_Framework_TestCase
{
    protected $client;

    protected function setUp()
    {
		 $this->http = new GuzzleHttp\Client(['base_uri' => 'http://localhost']);
    }

    public function testApiData()
    {
		
		
	$response = $this->http->request('GET', '/testapi/users');
    $this->assertEquals(200, $response->getStatusCode());
     $data = json_decode($response->getBody(), true);
	 print_r($data);

        
    }
	
	public function testEmptyApiData()
    {
		
		
	$response = $this->http->request('GET', '/testapi/users');
     $data = [];
	$this->assertEmpty($data);
        return $data ;
        
    }
	 public function testGetData()
    {
		
		
	$response = $this->http->request('GET', 'testapi/user/?id=3');
    $this->assertEquals(200, $response->getStatusCode());
     $data = json_decode($response->getBody(), true);
	 print_r($data);

        
    }
	
	public function testEmptyGetData()
    {
		
		
	$response = $this->http->request('GET', 'testapi/user/?id=2');
     $data = [];
	$this->assertEmpty($data);
        return $data ;
        
    }
	
	public function testDelete_Error()
{
    $response = $this->http->request('DELETE', '/testapi/deleteUser/?id=2');

    $this->assertEquals(200, $response->getStatusCode(), "successfully deleted");
}
	
public function testDelete_Empty()
    {
		
		
	$response = $this->http->request('DELETE', '/testapi/deleteUser/?id=2');
     $data = [];
	$this->assertEmpty($data);
        return $data ;
        
    }
	
	
	public function testPOST()
{
	
	 $client = new Client('http://localhost', array(
        'request.options' => array(
            'exceptions' => false,
        )
    ));

    $data = array(
        'id'  => 15,
        'Name' => 'testuser',
        'Email' => 'testdddd15@gmail.com',
		'created_date'  => 2017-03-30,
		'isadmin'  => 0
    );

    $request = $client->post('/testapi/insertUser', null, json_encode($data));
   $response = $request->send();

    $this->assertEquals(201, $response->getStatusCode());
    $data = json_decode($response->getBody(true), true);
}
	
	
}

?>