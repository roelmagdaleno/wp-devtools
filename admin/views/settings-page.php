<div class="wrap">
	<h1>WP DevTools</h1>

	<form method="POST" action="options.php">
		<?php

		settings_fields( 'wp_devtools_group' );
		do_settings_sections( 'wp-devtools' );

		submit_button();

		?>
	</form>
</div>
