<?php

namespace Stamps\PassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use PKPass;

class DefaultController extends Controller {
	public function indexAction($name) {
		$request = Request::createFromGlobals();
		$userAgent = $request->headers->get('User-Agent') . "Passbook";
		if (strpos($userAgent,'Passbook') !== false) {
			$serialNumber = $this->generateSerialNumber();
			$passData = $this->generatePass($serialNumber);

			return $this->render('StampsPassBundle:Default:index.html.twig', array('name' => $userAgent));
		} else {
			return $this->render('StampsPassBundle:Default:index.html.twig', array('name' => $name));
		}
	}


	function generatePass($serialNumber) {
		$pass = new PKPass\PKPass();
		$pass->setCertificate(__DIR__ . '/../Resources/config/Certificate.p12');  // 2. Set the path to your Pass Certificate (.p12 file)
		$pass->setCertificatePassword('');     // 2. Set password for certificate
		$pass->setWWDRcertPath(__DIR__ . '/../Resources/config/AppleWWDRCA.pem'); // 3. Set the path to your WWDR Intermediate certificate (.pem file)


		$passJson = file_get_contents(__DIR__ . '/../Resources/config/souperbe.pass/pass.json');
		$passData = json_decode($passJson);

		// modify the pass template
		$passData->{'serialNumber'} = $serialNumber;
		$passData->{'barcode'}->{'message'} = $serialNumber;
		$passData->{'barcode'}->{'altText'} = $serialNumber;

		$pass->setJSON(json_encode($passData));

		// Add files to the PKPass package

		// add all images from the template folder
		$files = glob(__DIR__ . '/../Resources/config/souperbe.pass/*.{jpg,png,gif}', GLOB_BRACE);
		foreach($files as $file) {
			$pass->addFile($file);
		}

		// alternative add every file seperate
		//$pass->addFile('../souperbe.pass/icon.png');
		//$pass->addFile('../souperbe.pass/icon@2x.png');
		//$pass->addFile('../souperbe.pass/logo.png');

		$passOutput = $pass->create(true);
		if(!$passOutput) { // Create and output the PKPass
			throw new \Exception('Error: '.$pass->getError());
		}
		return $passOutput;
	}

	function generateSerialNumber(){
	  $serialNumber = substr(md5(uniqid(mt_rand(), true)), 0, 9); // No two passes with the same pass type identifier may have the same serial number.
	  $serialNumber = strtoupper($serialNumber);
	  $serialNumber[4] = "-";
	  return $serialNumber;
	}
}
