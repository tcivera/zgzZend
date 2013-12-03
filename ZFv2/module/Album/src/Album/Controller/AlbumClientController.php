<?php
namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Album\Model\Album;         
use Album\Form\AlbumForm;
use Zend\Http\Client as HttpClient;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;

class AlbumClientController extends AbstractActionController
{
	protected $albumTable;
	
	public function indexAction()
	{
		$client = new HttpClient();
		$client->setAdapter('Zend\Http\Client\Adapter\Curl');
		 
		$method = $this->params()->fromQuery('method', 'get');
		$client->setUri('http://zf2.local/album-rest/getList');
		$client->setMethod('GET');
		$response = $client->send();
        if (!$response->isSuccess()) {
            // report failure
            $message = $response->getStatusCode() . ': ' . $response->getReasonPhrase();             
            $response = $this->getResponse();
            $response->setContent($message);            
            return $response;
        }
        $body = $response->getBody();
         
        $response = $this->getResponse();
        $response->setContent($body);  
        $albums = Json::decode($response->getContent(), Json::TYPE_OBJECT);
        $result = new ViewModel(array(
        		'albums' => $albums->data,
        ));
        $result->setTemplate('album/album-client/index');
        return $result;
	}
	
	private function getAlbum($id) {
		$client = new HttpClient();
		$client->setAdapter('Zend\Http\Client\Adapter\Curl');
			
		$method = $this->params()->fromQuery('method', 'get');
		$client->setUri('http://zf2.local/album-rest/' . $id);
		$client->setMethod('GET');		
		$response = $client->send();
		if (!$response->isSuccess()) {
			// report failure
			$message = $response->getStatusCode() . ': ' . $response->getReasonPhrase();
			$response = $this->getResponse();
			$response->setContent($message);
			return $response;
		}
		$body = $response->getBody();
		 
		$response = $this->getResponse();
		$response->setContent($body);
		$album = Json::decode($response->getContent(), Json::TYPE_ARRAY);
		$albumModel = new Album();
		$albumModel->exchangeArray($album["data"]);
		return $albumModel;
	}

	public function addAction()
	{
		$form = new AlbumForm();
		$form->get('submit')->setValue('Add');
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$album = new Album();
			$form->setInputFilter($album->getInputFilter());
			$form->setData($request->getPost());
		
			if ($form->isValid()) {				
				$client = new HttpClient();
				$client->setAdapter('Zend\Http\Client\Adapter\Curl');				
				$client->setUri('http://zf2.local/album-rest/');
				$client->setMethod('POST');
				$requ = $form->getData();
				$requ = Json::encode($requ);
				//\Zend\Debug\Debug::dump($requ);
				$client->setRawBody($requ);				
				$response = $client->send();
				if (!$response->isSuccess()) {
					// report failure
					$message = $response->getStatusCode() . ': ' . $response->getReasonPhrase();
					$response = $this->getResponse();
					$response->setContent($message);
					return $response;
				}				
		
				// Redirect to list of albums
				return $this->redirect()->toRoute('album-client');
			}
		}
		$result = new ViewModel(array(				
				'form' => $form,
		));
		$result->setTemplate('album/album-client/add');
		return $result;
	}

	public function editAction()
	{
		$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) {
			return $this->redirect()->toRoute('album-client', array(
					'action' => 'add'
			));
		}
		
		// Get the Album with the specified id.  An exception is thrown
		// if it cannot be found, in which case go to the index page.
		try {
			$album = $this->getAlbum($id);			
		}
		catch (\Exception $ex) {
			return $this->redirect()->toRoute('album-client', array(
					'action' => 'index'
			));
		}

		$form  = new AlbumForm();
		$form->bind($album);
		$form->get('submit')->setAttribute('value', 'Edit');
		
		$request = $this->getRequest();
				
		if ($request->isPost()) {			
			$form->setInputFilter($album->getInputFilter());
			$form->setData($request->getPost());
		
			if ($form->isValid()) {
				//$this->getAlbumTable()->saveAlbum($album);
				$client = new HttpClient();
				$client->setAdapter('Zend\Http\Client\Adapter\Curl');				
				$client->setUri('http://zf2.local/album-rest/'. $id);
				$client->setMethod('PUT');
				$requ = $form->getData();				
				$requ = Json::encode($requ);						
				$client->setRawBody($requ);		
				$response = $client->send();
				if (!$response->isSuccess()) {
					// report failure
					$message = $response->getStatusCode() . ': ' . $response->getReasonPhrase();
					$response = $this->getResponse();
					$response->setContent($message);					
				}				
				// Redirect to list of albums
				return $this->redirect()->toRoute('album-client');
			}
		}		
		
		$result = new ViewModel(array(
				'id' => $id,
				'form' => $form,
		));
		$result->setTemplate('album/album-client/edit');
		return $result;
	}

	public function deleteAction()
	{
		$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) {
			return $this->redirect()->toRoute('album-client');
		}
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$del = $request->getPost('del', 'No');
		
			if ($del == 'Yes') {
				$id = (int) $request->getPost('id');
				$client = new HttpClient();
				$client->setAdapter('Zend\Http\Client\Adapter\Curl');
				$client->setUri('http://zf2.local/album-rest/' . $id);
				$client->setMethod('DELETE');				
				$response = $client->send();
				if (!$response->isSuccess()) {
					// report failure
					$message = $response->getStatusCode() . ': ' . $response->getReasonPhrase();
					$response = $this->getResponse();
					$response->setContent($message);
					return $response;
				}				
			}
		
			// Redirect to list of albums
			return $this->redirect()->toRoute('album');
		}
		
		$result = new ViewModel(array(
				'id' => $id,
				'album' => $this->getAlbum($id),
		));
		$result->setTemplate('album/album-client/delete');
		return $result;		
	}
}