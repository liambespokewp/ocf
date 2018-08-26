<?php
/**
 * Created by PhpStorm.
 * User: liammaclachlan
 * Date: 26/08/2018
 * Time: 09:55
 */

namespace OrganicForm;

class Form {

	public function getFormOutput() {

		ob_start(); ?>

		<form method="post">

			<div>
				<div></div>
			</div>

		</form><?php

		return ob_get_clean();

	}
}