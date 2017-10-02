<?php

interface iRemoteController
{
	public function getLat();
	public function getLng();
	public function getHeight();
}

class RemoteController implements iRemoteController
{
	/**
	 * Gets the current RC Latitude position
	 */
	public function getLat(){}

	/**
	 * Gets the current RC Longitude position
	 */
	public function getLng(){}

	/**
	 * Get current RC height (from the ground)
	 */
	public function getHeight(){}
}

interface iEngine
{
	public function increaseThrust();
	public function decreaseThrust();
	public function start($initialThrust);
}


class Engine implements iEngine
{
	private $step = 1; // in centimeters
	private $thrust;  // in kilograms  dahaf

	public function __construct() {

	}

	/**
	 * Increases thrust by the step value
	 */
	public function increaseThrust() {
		$this->thrust += $this->step;
	}

	/**
	 * Decreases thrust by the step value
	 */
	public function decreaseThrust() {
		$this->thrust -= $this->step;
	}

	/**
	 * Start engines with an initial thrust value
	 * @param $initialThrust is the hover thrust level
	 */
	public function start($initialThrust) {
		$this->thrust = $initialThrust;
	}
}


interface iDrone
{
	public function up();
	public function down();
	public function left();
	public function right();
	public function faster();
	public function slower();
	public function takePicture();
	public function followTheLeader($distance, $height);
	public function unfollowTheLeader();
	public function start();
	public function addEngine(iEngine $engine);
	public function mountRemoteController(iRemoteController $rc);


}

class Drone implements iDrone
{
	public $state;

	private $weight = 2.3; // Kilograms
	private $engines = array();
	private $rc;

	const FOLLOW = 'follow';
	const HOVER = 'hover';

	public function __construct() {

	}

	/**
	 * Moves the drone up by increasing the thrust of all engines
	 */
	public function up() {
		foreach($this->engines as $engine) {
			$engine->increaseThrust();
		}
	}

	/**
	 * Moves the drone down by decreasing the thrust of all engines
	 */
	public function down() {
		foreach($this->engines as $engine) {
			$engine->decreaseThrust();
		}
	}

	/**
	 * Moves the drone left by increasing the thrust of half right engines and decreasing the thrust of half left engines
	 */
	public function left() {
		for ($i=0; $i<count($this->engines); $i++) {
			if ($i < count($this->engines) / 2) {
				$this->engines[$i]->increaseThrust();
			} else {
				$this->engines[$i]->decreaseThrust();
			}

		}
	}

	/**
	 * Moves the drone right by increasing the thrust of half left engines and decreasing the thrust of half right engines
	 */
	public function right() {
		for ($i=0; $i<count($this->engines); $i++) {
			if ($i < count($this->engines) / 2) {
				$this->engines[$i]->decreaseThrust();
			} else {
				$this->engines[$i]->increaseThrust();
			}

		}
	}

	/**
	 * Moves the drone faster...
	 */
	public function faster() {

	}

	/**
	 * Moves the drone slower...
	 */
	public function slower() {

	}

	/**
	 * Taking a picture from the camera on the device
	 */
	public function takePicture() {

	}

	/**
	 * Following the Remote control while it's on FOLLOW state
	 * @param $distance integer in meters
	 * @param $height integer in meters
	 */
	public function followTheLeader($distance, $height) {
		$this->state = FOLLOW;
		$this->autoPilot($distance, $height);
	}

	/**
	 * Exiting the FOLLOW state and staying on the current height and distance
	 */
	public function unfollowTheLeader() {
		$this->state = HOVER;
	}

	/**
	 * Starting all the engines to the initial engine thrust
	 */
	public function start(){
		foreach($this->engines as $engine) {
			$engine->start($this->weight / count($this->engines));
		}
	}

	/**
	 * Initiates the Remote Controller
	 * @param iRemoteController $rc
	 */
	public function mountRemoteController(iRemoteController $rc){
		$this->rc = $rc;
	}

	/**
	 * Adding engine to the drone
	 * @param iEngine $engine
	 */
	public function addEngine(iEngine $engine) {
		$this->engines[] = $engine;
	}

	/**
	 * Changing the drone position by distance and height parameters while in FOLLOW state
	 * @param $distance integer in meters
	 * @param $height integer in meters
	 */
	private function autoPilot($distance, $height) {
		while ($this->state == FOLLOW){
			if (abs($this->rc->getLat() - $this->getLat()) > $distance){
				$this->left();
			} else if (abs($this->rc->getLat() - $this->getLat()) < $distance){
				$this->right();
			}

			if (abs($this->rc->getLng() - $this->getLng()) > $distance){
				$this->right();
			} else if (abs($this->rc->getLng() - $this->getLng()) < $distance){
				$this->left();
			}

			if($this->getHeight() - $this->rc->getHeight() < $height){
				$this->up();
			} else if ($this->getHeight() - $this->rc->getHeight() > $height){
				$this->down();
			}
		}
	}

	/**
	 * Gets drone lat position
	 */
	private function getLat() {
		// data from sensors
	}

	/**
	 * Gets drone lng position
	 */
	private function getLng(){
		// data from sensors
	}

	/**
	 * Gets drone height from the ground
	 */
	private function getHeight(){
		// data from sensors
	}

}


$Drone = new Drone();
$Drone->addEngine(new engine()); // Engine #1
$Drone->addEngine(new engine()); // Engine #2
$Drone->addEngine(new engine()); // Engine #3
$Drone->addEngine(new engine()); // Engine #4
$Drone->mountRemoteController(new RemoteController());
$Drone->start();
$Drone->followTheLeader(10, 5); // meters
