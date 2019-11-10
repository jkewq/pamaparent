<?php

require 'pamaparent.php';

// A program code snippet that was written in LISP is highly ideal choice for testing our class :)
// Example is taken from: https://www2.cs.sfu.ca/CourseCentral/310/pwfong/Lisp/1/tutorial1.html
$str = '(defun list-member (E L) "Test if E is a member of L." (cond ((null L) nil) ((eq E (first L)) t) (t (list-member E (rest L)))))';

$pp = new PamaParent('(', ')'); // we are specifying the opening and closing elements
$success = $pp->parse($str); // let's parse our example

if ($success === TRUE) {
	echo "<pre>"; var_dump( $pp->getOpeningPositions() ); echo "</pre>";

	echo "<hr /><pre>"; var_dump( $pp->getClosingPositions() ); echo "</pre>";

	echo "<hr /><pre>"; var_dump( $pp->getPairPositions() ); echo "</pre>";

	echo "<hr /><pre>"; var_dump( $pp->getRightJumpIndexes() ); echo "</pre>";

	echo "<hr /><pre>"; var_dump( $pp->getLeftJumpIndexes() ); echo "</pre>";
	
	echo "<hr /><pre>"; var_dump( $pp->getMaxLevel() ); echo "</pre>";
	
	echo "<hr /><pre>"; var_dump( $pp->getContentsByLevel(2) ); echo "</pre>";
	
	echo "<hr /><pre>"; var_dump( $pp->getContentsByLevel() ); echo "</pre>";
								 
} else {
	var_dump($this->getErrorType());
	var_dump($this->getErrorPosition());
	
}
								 
								 
		