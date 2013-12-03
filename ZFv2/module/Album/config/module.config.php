<?php
return array(
		'controllers' => array(
				'invokables' => array(
						'Album\Controller\Album' => 'Album\Controller\AlbumController',
						'Album\Controller\AlbumRest' => 'Album\Controller\AlbumRestController',
						'Album\Controller\AlbumClient' => 'Album\Controller\AlbumClientController',
				),
		),

		// The following section is new and should be added to your file
		'router' => array(
				'routes' => array(
						'album' => array(
								'type'    => 'segment',
								'options' => array(
										'route'    => '/album[/][:action][/:id]',
										'constraints' => array(
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'id'     => '[0-9]+',
										),
										'defaults' => array(
												'controller' => 'Album\Controller\Album',
												'action'     => 'index',
										),
								),
						),
						'album-rest' => array(
								'type'    => 'segment',
								'options' => array(
										'route'    => '/album-rest[/][:action][/:id]',
										'constraints' => array(
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'id'     => '[0-9]+',
										),
										'defaults' => array(
												'controller' => 'Album\Controller\AlbumRest',
												'action'     => 'index',
										),
								),
						),
						'album-client' => array(
								'type'    => 'segment',
								'options' => array(
										'route'    => '/album-client[/][:action][/:id]',
										'constraints' => array(
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'id'     => '[0-9]+',
										),
										'defaults' => array(
												'controller' => 'Album\Controller\AlbumClient',
												'action'     => 'index',
										),
								),
						),
				),
		),

		'view_manager' => array(
				'template_path_stack' => array(
						'album' => __DIR__ . '/../view',
						'album-client' => __DIR__ . '/../view',
				),
				'strategies' => array(
						'album-rest' => 'ViewJsonStrategy',
				)
		),
);

