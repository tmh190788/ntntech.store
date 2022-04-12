<?php
/**
 * Object that handles theme options page.
 *
 * @package xts
 */

namespace XTS\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Framework\Options;
use XTS\Singleton;
use XTS\Framework\Dashboard;

/**
 * Create page and display the form with all sections and fields.
 */
class Page extends Singleton {
	/**
	 * Options array loaded from the database.
	 *
	 * @var array
	 */
	private $_options;

	/**
	 * Array of all the available sections.
	 *
	 * @var array
	 */
	private $_sections;

	/**
	 * Array of all the available Field objects.
	 *
	 * @var array
	 */
	private $_fields;

	/**
	 * Array of all the available Presets.
	 *
	 * @var array
	 */
	private $_presets;

	/**
	 * Options set prefix.
	 *
	 * @var array
	 */
	public $opt_name = XTS_THEME_SLUG;

	/**
	 * Register hooks and load base data.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'admin_page' ) );

		$this->_presets = Presets::get_all();
	}

	/**
	 * Load all field objects and add them to the sections set.
	 *
	 * @since 1.0.0
	 */
	private function load_fields() {
		$this->_sections = Options::get_sections();
		$this->_fields   = Options::get_fields();

		foreach ( $this->_fields as $key => $field ) {
			$this->_sections[ $field->args['section'] ]['fields'][] = $field;
		}

		$this->_options = Options::get_options();
	}

	/**
	 * Add theme settings links to the admin bar.
	 *
	 * @since 1.0.0
	 *
	 * @param object $admin_bar Admin bar object.
	 */
	public function admin_bar_links( $admin_bar ) {
		$this->load_fields();

		$dashboard_text = esc_html__( 'Space Dashboard', 'xts-theme' );

		if ( xts_get_opt( 'white_label', '0' ) ) {
			$dashboard_text = esc_html__( 'Theme Dashboard', 'xts-theme' );
		}

		$admin_bar->add_node(
			array(
				'id'    => 'xts_dashboard',
				'title' => '<span class="xf-dashboard"></span>' . $dashboard_text,
				'href'  => admin_url( 'admin.php?page=xts_dashboard' ),
			)
		);

		$admin_bar->add_node(
			array(
				'id'     => 'xts_theme_settings',
				'title'  => '<span class="xf-theme-setting"></span>' . esc_html__( 'Theme Settings', 'xts-theme' ),
				'href'   => admin_url( 'admin.php?page=xtemos_options' ),
				'parent' => 'xts_dashboard',
			)
		);

		if ( $this->_sections ) {
			foreach ( $this->_sections as $key => $section ) {
				if ( isset( $section['parent'] ) ) {
					continue;
				}

				$admin_bar->add_node(
					array(
						'id'     => $section['id'],
						'title'  => '<span class="' . $section['icon'] . '"></span>' . $section['name'],
						'href'   => admin_url( 'admin.php?page=xtemos_options&tab=' . $key ),
						'parent' => 'xts_theme_settings',
					)
				);
			}
		}

		$active_presets = Presets::get_active_presets();
		$all_presets    = Presets::get_all();
		if ( $active_presets ) {
			$admin_bar->add_node(
				array(
					'id'     => 'xts_theme_settings_presets',
					'title'  => '<span class="xf-theme-setting-presets"></span>' . esc_html__( 'Active presets', 'xts-theme' ),
					'href'   => admin_url( 'admin.php?page=xtemos_options' ),
					'parent' => 'xts_dashboard',
					'meta'   => array(
						'title' => esc_html__( 'Active presets', 'xts-theme' ),
					),
				)
			);

			foreach ( $active_presets as $preset ) {
				$name = isset( $all_presets[ $preset ]['name'] ) ? $all_presets[ $preset ]['name'] : 'Preset name';

				$admin_bar->add_node(
					array(
						'id'     => 'xts_theme_settings_presets_' . $preset,
						'title'  => $name,
						'href'   => admin_url( 'admin.php?page=xtemos_options&preset=' . $preset ),
						'parent' => 'xts_theme_settings_presets',
						'meta'   => array(
							'title' => $name,
						),
					)
				);
			}
		}

		$admin_bar->add_node(
			array(
				'id'     => 'xts_header_builder',
				'title'  => '<span class="xf-header-builder"></span>' . esc_html__( 'Header builder', 'xts-theme' ),
				'href'   => admin_url( 'admin.php?page=xts_header_builder' ),
				'parent' => 'xts_dashboard',
			)
		);

		$header = xts_get_header();

		if ( $header && ! is_admin() ) {
			$hb_url = admin_url( 'admin.php?page=xts_header_builder#/builder/' . $header->get_id() );
			$admin_bar->add_node(
				array(
					'id'     => 'xts_edit_header',
					'title'  => esc_html__( 'Edit current header', 'xts-theme' ),
					'href'   => $hb_url,
					'parent' => 'xts_header_builder',
					'meta'   => array(
						'title' => $header->get_name(),
					),
				)
			);
		}

		if ( ( xts_get_opt( 'white_label_dummy_content', '1' ) && xts_get_opt( 'white_label', '0' ) ) || ! xts_get_opt( 'white_label', '0' ) ) {
			$admin_bar->add_node(
				array(
					'id'     => 'xts_import',
					'title'  => '<span class="xf-dummy-content"></span>' . esc_html__( 'Dummy content', 'xts-theme' ),
					'href'   => admin_url( 'admin.php?page=xts_import' ),
					'parent' => 'xts_dashboard',
				)
			);
		}

		if ( ( xts_get_opt( 'white_label_license', '1' ) && xts_get_opt( 'white_label', '0' ) ) || ! xts_get_opt( 'white_label', '0' ) ) {
			$admin_bar->add_node(
				array(
					'id'     => 'xts_activation',
					'title'  => '<span class="xf-activation"></span>' . esc_html__( 'Activation', 'xts-theme' ),
					'href'   => admin_url( 'admin.php?page=xts_activation' ),
					'parent' => 'xts_dashboard',
				)
			);
		}

		$admin_bar->add_node(
			array(
				'id'     => 'xts_system_status',
				'title'  => '<span class="xf-system-status"></span>' . esc_html__( 'System status', 'xts-theme' ),
				'href'   => admin_url( 'admin.php?page=xts_system_status' ),
				'parent' => 'xts_dashboard',
			)
		);

		$admin_bar->add_node(
			array(
				'id'     => 'xts_plugins',
				'title'  => '<span class="xf-plugins"></span>' . esc_html__( 'Plugins', 'xts-theme' ),
				'href'   => admin_url( 'admin.php?page=xts_plugins' ),
				'parent' => 'xts_dashboard',
			)
		);

		do_action( 'xts_admin_bar_submenu', $admin_bar );
	}

	/**
	 * Callback to register a page in the dashboard.
	 *
	 * @since 1.0.0
	 */
	public function admin_page() {
		$this->load_fields();

		// Create admin page.
		add_menu_page(
			esc_html__( 'Theme Settings', 'xts-theme' ),
			esc_html__( 'Theme Settings', 'xts-theme' ),
			'manage_options',
			'xtemos_options',
			array( &$this, 'page_content' ),
			'',
			'55.500'
		);

		foreach ( $this->_sections as $key => $section ) {
			if ( isset( $section['parent'] ) ) {
				continue;
			}

			add_submenu_page(
				'xtemos_options',
				$section['name'],
				$section['name'],
				'manage_options',
				'xtemos_options&tab=' . $key,
				array( &$this, 'page_content' )
			);
		}

		remove_submenu_page( 'xtemos_options', 'xtemos_options' );

		$this->add_admin_menu_separator( '58.3000' );
	}

	/**
	 * Add divider.
	 *
	 * @param integer $position Position.
	 */
	public function add_admin_menu_separator( $position ) {
		global $menu;

		$index = 0;

		foreach ( $menu as $offset => $section ) {
			if ( substr( $section[2], 0, 9 ) === 'separator' ) {
				$index++;
			}

			if ( $offset >= $position ) {
				$menu[ $position ] = array( '', 'read', 'xts-menu-separator', '', 'xts-menu-separator wp-menu-separator' ); // phpcs:ignore
				break;
			}
		}

		ksort( $menu );
	}

	/**
	 * Render the options page content.
	 *
	 * @since 1.0.0
	 */
	public function page_content() {
		Dashboard::get_instance()->before();

		Dashboard::get_instance()->header( esc_html__( 'Theme settings', 'xts-theme' ), esc_html__( 'Configure your website layout, colors, typography and other options', 'xts-theme' ) );

		do_action( 'xts_before_theme_settings' );

		?>
			<div class="xts-row xts-dashboard xts-page xts-options">
				<div class="xts-col xts-col-xxl-8">
					<form action="options.php" method="post">
						<?php $this->display_message(); ?>

						<div class="xts-fields-tabs">
							<div class="xts-sections-nav">
								<ul>
									<?php $this->display_sections_tree(); ?>
								</ul>
							</div>

							<div class="xts-sections">

								<div class="xts-option-header">
									<div class="xts-options-search">
										<input type="text" placeholder="<?php esc_html_e( 'Start typing to find options...', 'xts-theme' ); ?>">
									</div>
								</div>

								<?php $this->display_sections(); ?>

								<div class="xts-options-actions">
									<div class="xts-options-actions-inner">
										<input type="hidden" class="xts-last-tab-input" name="xts-<?php echo esc_attr( $this->opt_name ); ?>-options[last_tab]" value="<?php echo esc_attr( $this->get_last_tab() ); ?>" />
										<button class="xts-save-options-btn xts-btn xts-btn-primary xts-btn-shadow xts-size-l xf-save"><?php esc_html_e( 'Save options', 'xts-theme' ); ?></button>
										<button class="xts-reset-options-btn xts-btn-bordered xts-btn-primary xts-size-l" name="xts-<?php echo esc_attr( $this->opt_name ); ?>-options[reset-defaults]" value="1"><?php esc_html_e( 'Reset defaults', 'xts-theme' ); ?></button>
									</div>
									<?php if ( isset( $_GET['preset'] ) ) : // phpcs:ignore ?>
										<a href="<?php echo esc_url( admin_url( 'admin.php?page=xtemos_options' ) ); ?>" class="xts-btn-bordered xts-btn-disable xts-size-l">
											<?php esc_html_e( 'To global settings', 'xts-theme' ); ?>
										</a>
									<?php endif; ?>
								</div>
							</div>
						</div>

						<input type="hidden" name="page_options" value="xts-<?php echo esc_attr( $this->opt_name ); ?>-options" />
						<input type="hidden" name="action" value="update" />

						<?php if ( Presets::get_current_preset() ) : ?>
							<input type="hidden" class="xts-fields-to-save" name="xts-<?php echo esc_attr( $this->opt_name ); ?>-options[fields_to_save]" value="<?php echo esc_attr( $this->get_fields_to_save() ); ?>" />
							<input type="hidden" name="xts-<?php echo esc_attr( $this->opt_name ); ?>-options[preset]" value="<?php echo esc_attr( Presets::get_current_preset() ); ?>" />
						<?php endif; ?>

						<?php settings_fields( 'xts-options-group' ); ?>
					</form>
				</div>

				<div class="xts-col xts-col-xxl-4">
					<?php Presets::output_ui(); ?>

					<?php if ( ! xts_get_opt( 'white_label', '0' ) ) : ?>
						<div class="xts-dashboard-video">
							<div class="xts-info">
								<span><?php esc_html_e( 'Need help? Read our documentation about how to use settings presets.', 'xts-theme' ); ?></span>

								<a href="https://space.xtemos.com/article/theme-settings-presets" class="xts-inline-btn" target="_blank">
									<?php esc_html_e( 'Read tutorial', 'xts-theme' ); ?>
								</a>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<?php do_action( 'xts_after_theme_settings' ); ?>
		<?php

		Dashboard::get_instance()->after();
	}

	/**
	 * Get last visited tab by visitor.
	 *
	 * @since 1.0.0
	 */
	private function get_last_tab() {
		reset( $this->_sections );

		if ( isset( $this->_options['last_tab'] ) && isset( $_GET['settings-updated'] ) ) { // phpcs:ignore
			$current_tab = $this->_options['last_tab'];
		} elseif ( isset( $_GET['tab'] ) ) { // phpcs:ignore
			$current_tab = $_GET['tab']; // phpcs:ignore
		} else {
			$current_tab = 'general_layout_section';
		}

		return $current_tab;
	}

	/**
	 * Display saved/imported message.
	 *
	 * @since 1.0.0
	 */
	private function display_message() {
		$message = $this->get_last_message();

		$text = false;

		if ( 'save' === $message ) {
			$text = esc_html__( 'Settings are successfully saved.', 'xts-theme' );
		} elseif ( 'import' === $message ) {
			$text = esc_html__( 'New options are successfully imported.', 'xts-theme' );
		} elseif ( 'reset' === $message ) {
			$text = esc_html__( 'All options are set to default values.', 'xts-theme' );
		}

		if ( $text ) {
			echo '<div class="xts-options-message xts-notice xts-success">' . $text . '</div>'; // phpcs:ignore
		}
	}

	/**
	 * Get last message.
	 *
	 * @since 1.0.0
	 */
	private function get_last_message() {
		return ( isset( $this->_options['last_message'] ) && isset( $_GET['settings-updated'] ) ) ? $this->_options['last_message'] : ''; // phpcs:ignore
	}

	/**
	 * Display sections navigation tree.
	 *
	 * @since 1.0.0
	 */
	private function display_sections_tree() {
		$current_tab   = $this->get_last_tab();
		$active_parent = '';

		if ( isset( $this->_sections[ $current_tab ]['parent'] ) ) {
			$active_parent = $this->_sections[ $current_tab ]['parent'];
		}

		foreach ( $this->_sections as $key => $section ) {
			if ( isset( $section['parent'] ) ) {
				continue;
			}

			$subsections = array_filter(
				$this->_sections,
				function( $el ) use ( $section ) {
					return isset( $el['parent'] ) && $el['parent'] === $section['id'];
				}
			);

			$classes = '';

			if ( $key === $current_tab || $key === $active_parent ) {
				$classes .= ' xts-active-nav';
			}
			if ( is_array( $subsections ) && count( $subsections ) > 0 ) {
				$classes .= ' xts-has-child';
			}

			?>
				<li class="<?php echo esc_attr( $classes ); ?>">
					<a href="javascript:void(0);" data-id="<?php echo esc_attr( $key ); ?>" data-id="<?php echo esc_attr( $key ); ?>">
						<?php if ( isset( $section['icon'] ) && $section['icon'] ) : ?>
							<span class="xts-section-icon <?php echo esc_attr( $section['icon'] ); ?>"></span>
						<?php endif; ?>

						<?php echo esc_html( $section['name'] ); ?>
					</a>

					<?php if ( is_array( $subsections ) && count( $subsections ) > 0 ) : ?>
						<ul>
							<?php foreach ( $subsections as $key => $subsection ) : ?>
								<?php $class_subsection = $key === $current_tab ? 'xts-active-nav' : ''; ?>
								<li class="xts-subsection-nav <?php echo esc_attr( $class_subsection ); ?>">
									<a href="" data-id="<?php echo esc_attr( $key ); ?>">
										<?php echo esc_html( $subsection['name'] ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

				</li>
			<?php
		}
	}

	/**
	 * Loop through all the sections and render all the fields.
	 *
	 * @since 1.0.0
	 */
	private function display_sections() {
		?>
		<?php foreach ( $this->_sections as $key => $section ) : ?>
			<?php $classes = $this->get_last_tab() !== $key ? 'xts-hidden' : 'xts-active-section'; ?>
			<div class="xts-fields-section <?php echo esc_attr( $classes ); ?>" data-id="<?php echo esc_attr( $key ); ?>">
				<div class="xts-section-title">
					<?php if ( isset( $section['icon'] ) && $section['icon'] ) : ?>
						<span class="xts-section-icon <?php echo esc_attr( $section['icon'] ); ?>"></span>
					<?php endif; ?>

					<h3><?php echo esc_html( $section['name'] ); ?></h3>
				</div>
				<div class="xts-section-content xts-row">
					<?php
					$previous_group = false;

					if ( isset( $section['fields'] ) ) {
						foreach ( $section['fields'] as $key => $field ) {
							if ( $previous_group && ( ! isset( $field->args['group'] ) || $previous_group !== $field->args['group'] ) ) {
								echo '</div><!-- close group ' . esc_html( $previous_group ) . '-->';
								$previous_group = false;
							}

							if ( isset( $field->args['group'] ) && $previous_group !== $field->args['group'] ) {
								$previous_group = $field->args['group'];
								echo '<div class="xts-group-title"><span>' . esc_html( $previous_group ) . '</span></div>';
								echo '<div class="xts-fields-group xts-row">';
							}

							if ( $this->is_inherit_field( $field->get_id() ) ) {
								$field->inherit_value( true );
							}

							$field->render( null, Presets::get_current_preset() );
						}
					}

					if ( $previous_group ) {
						echo '</div><!-- close group ' . esc_html( $previous_group ) . '-->';
					}
					?>
				</div>
			</div>
		<?php endforeach; ?>
		<?php
	}

	/**
	 * Get fields to save value.
	 *
	 * @since 1.0.0
	 */
	private function get_fields_to_save() {
		if ( ! isset( $this->_options[ Presets::get_current_preset() ] ) || ! isset( $this->_options[ Presets::get_current_preset() ]['fields_to_save'] ) ) {
			return '';
		}

		return $this->_options[ Presets::get_current_preset() ]['fields_to_save'];
	}

	/**
	 * Is field by id inherits value.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id Field's id.
	 *
	 * @return bool
	 */
	private function is_inherit_field( $id ) {
		$fields_to_save = explode( ',', $this->get_fields_to_save() );
		return ! in_array( $id, $fields_to_save, true );
	}
}
