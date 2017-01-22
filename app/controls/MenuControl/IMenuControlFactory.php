<?php
/**
 * Created by PhpStorm.
 * User: raven
 * Date: 3.1.17
 * Time: 18:59
 */

namespace App\Controls;


interface IMenuControlFactory {

	/**
	 * @return MenuControl
	 */
	public function create();
}