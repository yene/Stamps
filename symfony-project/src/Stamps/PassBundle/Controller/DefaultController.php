<?php

namespace Stamps\PassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller {
	public function indexAction($name) {
		$request = Request::createFromGlobals();
		$userAgent = $request->headers->get('User-Agent') . "Passbook";
		if (strpos($userAgent,'Passbook') !== false) {
			$serialNumber = $this->generateSerialNumber();
			//$this->generatePass($serialNumber);

			return $this->render('StampsPassBundle:Default:index.html.twig', array('name' => $userAgent));
		} else {
			return $this->render('StampsPassBundle:Default:index.html.twig', array('name' => $name));
		}
	}


	function generatePass($serialNumber) {

		//$loader = require __DIR__.'/../../../../vendor/autoload.php';
		//$loader->add('PKPass\PKPass', __DIR__.'pkpass/pkpass/src');

		$pass = new PKPass\PKPass();
		$pass->setCertificate('../Certificate.p12');  // 2. Set the path to your Pass Certificate (.p12 file)
		$pass->setCertificatePassword('');     // 2. Set password for certificate
		$pass->setWWDRcertPath(@StampsPassBundle/Resources/config/AppleWWDRCA.pem); // 3. Set the path to your WWDR Intermediate certificate (.pem file)
		$pass->setJSON(json_encode($passData));


		// Add files to the PKPass package

		// add all images from the template folder
		$files = glob('../souperbe.pass/*.{jpg,png,gif}', GLOB_BRACE);
		foreach($files as $file) {
			$pass->addFile($file);
		}

		// alternative add every file seperate
		//$pass->addFile('../souperbe.pass/icon.png');
		//$pass->addFile('../souperbe.pass/icon@2x.png');
		//$pass->addFile('../souperbe.pass/logo.png');

		if(!$pass->create(true)) { // Create and output the PKPass
			echo 'Error: '.$pass->getError();
		}

	}




	function generateSerialNumber(){
	  $serialNumber = substr(md5(uniqid(mt_rand(), true)), 0, 9); // No two passes with the same pass type identifier may have the same serial number.
	  $serialNumber = strtoupper($serialNumber);
	  $serialNumber[4] = "-";
	  return $serialNumber;
	}
}
