<?php
/**
 * SlUserEnrolledCourse.
 * php version 5.6
 *
 * @category SlUserEnrolledCourse
 * @package  SureTriggers
 * @author   BSF <username@example.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @link     https://www.brainstormforce.com/
 * @since    1.0.0
 */

namespace SureTriggers\Integrations\SenseiLMS\Triggers;

use SureTriggers\Controllers\AutomationController;
use SureTriggers\Integrations\WordPress\WordPress;
use SureTriggers\Traits\SingletonLoader;

/**
 * SlUserEnrolledCourse
 *
 * @category SlUserEnrolledCourse
 * @package  SureTriggers
 * @author   BSF <username@example.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @link     https://www.brainstormforce.com/
 * @since    1.0.0
 */
class SlUserEnrolledCourse {

	/**
	 * Integration type.
	 *
	 * @var string
	 */
	public $integration = 'SenseiLMS';

	/**
	 * Trigger name.
	 *
	 * @var string
	 */
	public $trigger = 'sl_user_enrolled_course';

	use SingletonLoader;

	/**
	 * Constructor
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
		add_filter( 'sure_trigger_register_trigger', [ $this, 'register' ] );
	}

	/**
	 * Register action.
	 *
	 * @param array $triggers trigger data.
	 *
	 * @return array
	 */
	public function register( $triggers ) {
		$triggers[ $this->integration ][ $this->trigger ] = [
			'label'         => __( 'User Enrolled Course', 'suretriggers' ),
			'action'        => $this->trigger,
			'common_action' => 'sensei_user_course_start',
			'function'      => [ $this, 'trigger_listener' ],
			'priority'      => 10,
			'accepted_args' => 2,
		];

		return $triggers;
	}

	/**
	 * Trigger listener.
	 *
	 * @param  int $user_id         ID of user being graded.
	 * @param  int $course_id       ID of course.
	 *
	 * @return void
	 */
	public function trigger_listener( $user_id, $course_id ) {
		$course = get_post( $course_id );

		$context                  = WordPress::get_user_context( $user_id );
		$context['sensei_course'] = $course_id;
		if ( $course instanceof \WP_Post ) {
			$context['course_title'] = $course->post_title;
		}
		AutomationController::sure_trigger_handle_trigger(
			[
				'trigger' => $this->trigger,
				'context' => $context,
			]
		);
	}
}

SlUserEnrolledCourse::get_instance();
