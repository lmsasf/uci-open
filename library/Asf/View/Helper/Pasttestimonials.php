<?php
/**
 * Helper par imprimir los testimonials asociados a un ocw
 * @author damills
 *
 */
class Asf_View_Helper_Pasttestimonials extends Zend_View_Helper_Abstract {
	public function pasttestimonials(){
		return $this;
	}
	/**
	 * 
	 * @param Zend_Db_Table_Rowset $Testimonials
	 * @param string $title
	 */
	public function render($Testimonials, $title="Testimonials"){

		echo '<h3>' . $title . '</h3>';
		foreach ($Testimonials as $testimonial ) {
			echo '<div class="media"><div class="media-body"><blockquote><p class="expand">';
			echo $testimonial->tesTestimonial . '</p>';
			echo '<small>';
			echo $testimonial->tesName;
			echo '</small></blockquote></div></div>';
		}
		
	}
}