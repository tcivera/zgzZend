<?php
namespace Album\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Album\Model\Album;     
use Zend\view\model\JsonModel;
use Zend\Json\Json;

class AlbumRestController extends AbstractRestfulController
{
	protected $albumTable;
	
	public function getListAction()
	{	
		$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) {
			$results = $this->getAlbumTable()->fetchAll();
			$data = array();
			foreach($results as $result) {
				$data[] = $result;
			}
		} else {
			$data = $this->getAlbumTable()->getAlbum($id);			
		}	
		return new JsonModel(array(
				'data' => $data,
		));		
	}
	
	public function indexAction() {
		if ($this->getRequest()->isGet()) {
			$id = (int) $this->params()->fromRoute('id', 0);
			$data = $this->getAlbumTable()->getAlbum($id);		
			return new JsonModel(array(
					'data' => $data,
			));
		} else if ($this->getRequest()->isPost()) {
			$data = Json::decode($this->getRequest()->getContent('data'), Json::TYPE_ARRAY);					
			$album = new Album();			
			$album->title = $data["title"];
			$album->artist = $data["artist"];	
			$this->getAlbumTable()->saveAlbum($album);			
			return new JsonModel(array(
					'data' => 'OK',
			));
		} else if ($this->getRequest()->isPut()) {				
			$data = Json::decode($this->getRequest()->getContent('data'), Json::TYPE_ARRAY);					
			$album = new Album();
			$album->id = $data["id"];
			$album->title = $data["title"];
			$album->artist = $data["artist"];	
			$this->getAlbumTable()->saveAlbum($album);			
			return new JsonModel(array(
					'data' => 'OK',
			));
		} else if ($this->getRequest()->isDelete()) {				
			$id = (int) $this->params()->fromRoute('id', 0);
			$data = $this->getAlbumTable()->deleteAlbum($id);		
			return new JsonModel(array(
					'data' => 'OK',
			));
		}
	}


	
	public function getAlbumTable()
	{
		if (!$this->albumTable) {
			$sm = $this->getServiceLocator();
			$this->albumTable = $sm->get('Album\Model\AlbumTable');
		}
		return $this->albumTable;
	}


}