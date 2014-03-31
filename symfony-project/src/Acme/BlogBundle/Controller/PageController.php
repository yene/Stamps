<?php

class PageController extends FOSRestController
{
	public function getPageAction($id)
	{
	    return $this->container->get('doctrine.entity_manager')->getRepository('Page')->find($id);
	}


	
}